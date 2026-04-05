<?php
namespace App\Plugins\CablingJournal;

use App\Plugins\Hooks\PageHook;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class Page extends PageHook
{
    // Путь к шаблону относительно resources/views
//    public string $view = 'plugins.CablingJournal.resources.views.page';
//    public string $view = 'resources.views.page';
    private string $dbPath;

    public function __construct()
    {
        // Путь к файлу базы данных внутри плагина
        $this->dbPath = __DIR__ . '/data/database.php';
    }

    /**
     * Разрешаем доступ всем авторизованным пользователям
     */
    public function authorize(Authenticatable $user): bool
    {
//      file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt',"Call  authorize()\n",FILE_APPEND);
        return true;
    }

    private function log_it($message)
    {
        file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt',date("d-m-Y H:i:s")."\t". print_r($message,true) . "\n",FILE_APPEND);
        return true;
    }

    /**
     * Основная логика: обработка ввода и подготовка данных для таблицы
     */

        public function data(): array
{

    $request = request(); // получаем объект запроса Laravel
    $selectedLocationId = $request->get('location_id', 0);
    $selectedRackId = $request->get('rack_id', 0);
//    $selectedLocationId = 0;
    // Загружаем базу данных
    $data = file_exists($this->dbPath) ? include $this->dbPath : ['custom_racks' => [], 'custom_panels' => [], 'custom_rack_devices' => []];
    $this->log_it($request->post());
    $this->log_it("GET:");
    $this->log_it($request->all());
    // --- ОБРАБОТКА действия ---
    if ($request->has('action')) {
    $this->log_it("Action found: " . $request->input('action'));
    $action = $request->input('action'); // add_rack, add_panel, add_device

        if ($action === 'add_rack') {
            // Получаем ID локации из формы (текущая локация)
            $location_id = $request->input('location_id', $selectedLocationId);
            // Генерируем новый ID для шкафа (максимальный + 1)
            $racks = $data['custom_racks'] ?? [];
            $newId = empty($racks) ? 1 : max(array_keys($racks)) + 1;

            $newRack = [
                'id'          => $newId,
                'location_id' => $location_id,
                'name'        => $request->input('name'),
                'floor'       => $request->input('floor'),
                'units'       => (int) $request->input('units', 42),
                'type'        => $request->input('type'),
                'note'        => $request->input('note'),
                'model'       => $request->input('model'),
            ];

            $data['custom_racks'][$newId] = $newRack;

            // Сохраняем обратно
            $code = "<?php\nreturn " . var_export($data, true) . ";";
            file_put_contents($this->dbPath, $code);
            // Сброс кеша
if (function_exists('opcache_invalidate')) {
    opcache_invalidate($this->dbPath, true);
}
clearstatcache(true, $this->dbPath);
            session()->flash('status', 'Шкаф успешно добавлен!');
            // Редиректим на ту же локацию, чтобы обновить список шкафов
//        return redirect(url('plugin/CablingJournal?location_id=' . $location_id));
            header("Location: ".url('plugin/CablingJournal?location_id=' . $location_id));

        }
        elseif ($action === 'delete_rack') {
    $rack_id = (int)$request->input('rack_id');
    $location_id = (int)$request->input('location_id', 0);

    if ($rack_id) {
        // Удаляем сам шкаф
        if (isset($data['custom_racks'][$rack_id])) {
            unset($data['custom_racks'][$rack_id]);
        }

        // Удаляем все панели, принадлежащие этому шкафу
        if (isset($data['custom_panels']) && is_array($data['custom_panels'])) {
            foreach ($data['custom_panels'] as $panel_id => $panel) {
                if (isset($panel['rack_id']) && $panel['rack_id'] == $rack_id) {
                    unset($data['custom_panels'][$panel_id]);
                }
            }
        }

        // Удаляем все устройства, принадлежащие этому шкафу
        if (isset($data['custom_rack_devices']) && is_array($data['custom_rack_devices'])) {
            foreach ($data['custom_rack_devices'] as $device_id => $device) {
                if (isset($device['rack_id']) && $device['rack_id'] == $rack_id) {
                    unset($data['custom_rack_devices'][$device_id]);
                }
            }
        }

        // Сохраняем изменения
        $code = "<?php\nreturn " . var_export($data, true) . ";";
        file_put_contents($this->dbPath, $code);

        // Сброс OPcache
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->dbPath, true);
        }
        clearstatcache(true, $this->dbPath);

        session()->flash('status', 'Шкаф и связанные элементы удалены.');
    }

    // Редирект на список шкафов этой локации
    header("Location: " . url('plugin/CablingJournal?location_id=' . $location_id));
    exit;
}
    elseif ($action === 'add_panel') {
    $rack_id = (int)$request->input('rack_id', $selectedRackId);
    $location_id = (int)$request->input('location_id', $selectedLocationId);
    $panel_type = $request->input('panel_type'); // 'device' or 'passive'
    $start_unit = (int)$request->input('start_unit', 1);
    $unit_count = (int)$request->input('unit_count', 1);
    $note = $request->input('note', '');

        // Проверка обязательных полей
    if ($start_unit <= 0 || $start_unit > $data['custom_racks'][$rack_id]['units']) {
        session()->flash('status', 'Ошибка: неверный начальный юнит');
        header("Location: " . url('plugin/CablingJournal?location_id=' . $location_id . '&rack_id=' . $rack_id));
        exit;
    }

    if ($panel_type === 'device') {
        $device_id = (int)$request->input('device_id');
        if ($device_id && $rack_id) {
            $rack_devices = $data['custom_rack_devices'] ?? [];
            $newId = empty($rack_devices) ? 1 : max(array_keys($rack_devices)) + 1;
            $data['custom_rack_devices'][$newId] = [
                'id' => $newId,
                'rack_id' => $rack_id,
                'device_id' => $device_id,
                'start_unit' => $start_unit,
                'unit_count' => $unit_count,
                'note' => $note,
            ];
            session()->flash('status', 'Устройство добавлено в шкаф');
        } else {
            session()->flash('status', 'Ошибка: не выбран device_id');
        }
    } else { // passive panel
        $name = $request->input('name');
        $type = $request->input('panel_tech', 'panel');
        $model = $request->input('model', '');
        $port_count = $request->input('port_count', 0);
        if ($name && $rack_id) {
            $panels = $data['custom_panels'] ?? [];
            $newId = empty($panels) ? 1 : max(array_keys($panels)) + 1;
            $data['custom_panels'][$newId] = [
                'id' => $newId,
                'rack_id' => $rack_id,
                'name' => $name,
                'type' => $type,
                'model' => $model,
                'distance_from_rack' => 0,
                'note' => $note,
                'unit_count' => $unit_count,
                'start_unit' => $start_unit,
                'port_count' => $port_count,
            ];
        // Генерация портов для пассивной панели

if($port_count > 0){
$panel_ports = $data['custom_panel_ports'] ?? [];
$nextPortId = empty($panel_ports) ? 1 : max(array_keys($panel_ports)) + 1;
for ($i = 1; $i <= $port_count; $i++) {
    $panel_ports[$nextPortId] = [
        'id'          => $nextPortId,
        'panel_id'    => $newId,
        'port_number' => $i,
        'fiber_color' => ($type == 'fiber') ? 'Grey' : null,
        'tube_color'  => null,
        'status'      => 'Active',
        'note'        => '',
        'type'  => $type,
    ];
    $nextPortId++;
}
$data['custom_panel_ports'] = $panel_ports;
}
            session()->flash('status', 'Панель добавлена в шкаф');

        } else {
            session()->flash('status', 'Ошибка: не заполнено имя панели');
        }
    }

    // Сохраняем данные
    $code = "<?php\nreturn " . var_export($data, true) . ";";
    file_put_contents($this->dbPath, $code);
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($this->dbPath, true);
    }
    clearstatcache(true, $this->dbPath);

    // Редирект обратно на просмотр шкафа
    header("Location: " . url('plugin/CablingJournal?location_id=' . $location_id . '&rack_id=' . $rack_id));
    exit;
}
elseif ($action === 'delete_item') {
    $rack_id = (int)$request->input('rack_id');
    $unit = (int)$request->input('unit');
    $location_id = (int)$request->input('location_id', 0);

    // Загружаем актуальные данные
    $data = file_exists($this->dbPath) ? include $this->dbPath : ['custom_racks' => [], 'custom_panels' => [], 'custom_rack_devices' => []];

    // Удаляем устройство, если оно начинается с этого юнита
    if (isset($data['custom_rack_devices'])) {
        foreach ($data['custom_rack_devices'] as $id => $dev) {
            if ($dev['rack_id'] == $rack_id && $dev['start_unit'] == $unit) {
                unset($data['custom_rack_devices'][$id]);
                break;
            }
        }
    }

    // Удаляем панель, если она начинается с этого юнита
    if (isset($data['custom_panels'])) {
        foreach ($data['custom_panels'] as $id => $panel) {
            if (isset($panel['rack_id']) && $panel['rack_id'] == $rack_id && ($panel['start_unit'] ?? 1) == $unit) {
                unset($data['custom_panels'][$id]);
                break;
            }
        }
    }

    // Сохраняем изменения
    $code = "<?php\nreturn " . var_export($data, true) . ";";
    file_put_contents($this->dbPath, $code);
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($this->dbPath, true);
    }
    clearstatcache(true, $this->dbPath);

    session()->flash('status', 'Элемент удалён из шкафа');
    header("Location: " . url('plugin/CablingJournal?location_id=' . $location_id . '&rack_id=' . $rack_id));
    exit;
}
        // Здесь можно добавить обработку add_panel и add_device позже
        // ...
    }

    // --- РЕЖИМ 1: список локаций ---
    if ($selectedLocationId == 0) {
        $locations = DB::table('locations')->orderBy('location', 'asc')->get();
        return [
            'mode' => 'all',
            'locations' => $locations,
            'title' => 'Кабельный журнал',
            'selected_location' => 0,
        ];
    }

    // --- РЕЖИМ 2: список шкафов в выбранной локации ---
    if ($selectedRackId == 0) {
        $currentLocation = DB::table('locations')->where('id', $selectedLocationId)->first();
        $racks = $data['custom_racks'] ?? [];
        return [
            'title' => 'Кабельный журнал',
            'selected_rack' => 0,
            'selected_location' => $selectedLocationId,
            'location_name' => $currentLocation->location ?? 'Неизвестно',
            'racks' => $racks,
        ];
    }

    // --- РЕЖИМ 3: просмотр конкретного шкафа ---
    $rack = $data['custom_racks'][$selectedRackId] ?? null;
    if (!$rack) {
        // Если шкаф не найден, возвращаемся к списку локаций
        header("Location: ".url('plugin/CablingJournal?location_id=' . $selectedLocationId));
        exit;
    }
    $devicesList = DB::table('devices')->orderBy('hostname')->get(['device_id', 'hostname', 'sysName']);
    $maxUnits = $rack['units'] ?? 42;
    $occupiedUnits = [];

    // Устройства в шкафу
    $rackDevices = collect($data['custom_rack_devices'] ?? [])->where('rack_id', $selectedRackId);
    foreach ($rackDevices as $rd) {
        $device_id = $rd['device_id'];
        $hostname = DB::table('devices')->where('device_id', $device_id)->value('hostname');
        $startUnit = $rd['start_unit'];
        $unitCount = $rd['unit_count'] ?? 1;

        $ports = DB::table('ports')
            ->where('device_id', $device_id)
            ->select('ifOperStatus', 'ifIndex', 'ifName', 'port_id', 'ifAlias')
            ->orderBy('ifIndex', 'asc')
            ->limit(52)
            ->get()
            ->map(function ($item) { return (array) $item; })
            ->toArray();

        $occupiedUnits[$startUnit] = [
            'type'       => 'device',
            'name'       => $hostname ?? 'Device ID: '.$device_id,
            'id'         => $device_id,
            'unit_count' => $unitCount,
            'ports'      => $ports,
        ];
    }

    // Панели в шкафу (distance_from_rack == 0)
    $panels = collect($data['custom_panels'] ?? [])->where('rack_id', $selectedRackId) ->where('distance_from_rack', 0);
    foreach ($panels as $p) {
        $unit = $p['start_unit'] ?? 1;
        $occupiedUnits[$unit] = [
            'type'       => $p['type'],
            'name'       => $p['name'] ?? 'Панель',
            'model'      => $p['model'] ?? '',
            'id'         => $p['id'],
            'unit_count' => $p['unit_count'] ?? '1',
            'ports'      => $p['port_count'] ?? '',
        ];
    }
    // Строим список свободных юнитов (для выпадающего списка)
$freeUnits = [];
for ($u = 1; $u <= $maxUnits; $u++) {
    if (!isset($occupiedUnits[$u])) {
        $freeUnits[] = $u;
    }
}
    $currentLocation = DB::table('locations')->where('id', $selectedLocationId)->first();

    return [
        'title'          => 'Кабельный журнал',
        'selected_location' => $selectedLocationId,
        'selected_rack'     => $selectedRackId,
        'rack'              => $rack,
        'occupied_units'    => $occupiedUnits,
        'max_units'         => $maxUnits,
        'location_name'     => $currentLocation->location ?? 'Неизвестно',
        'devices' => $devicesList,
        'free_units' => $freeUnits
    ];
}

}

