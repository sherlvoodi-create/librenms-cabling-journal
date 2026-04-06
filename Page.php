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
    $selectedPanelId = $request->get('panel_id', 0);
    // Загружаем базу данных
    $data = file_exists($this->dbPath) ? include $this->dbPath : ['custom_racks' => [], 'custom_panels' => [], 'custom_rack_devices' => []];
    $this->log_it($request->post());
    $this->log_it("GET:");
    $this->log_it($request->all());
    // --- ОБРАБОТКА действия ---
    if ($request->has('action')) {
    $this->log_it("Action found: " . $request->input('action'));
    $action = $request->input('action'); // add_rack, add_panel, add_device

    if ($action === 'add_rack') 
        {
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
            session()->flash('success', 'Шкаф успешно добавлен!');
            // Редиректим на ту же локацию, чтобы обновить список шкафов
            header("Location: ".url('plugin/CablingJournal?location_id=' . $location_id));

        }
    elseif ($action === 'delete_rack') 
        {
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

            session()->flash('success', 'Шкаф и связанные элементы удалены.');
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
    $port_count = (int)$request->input('port_count', 0);
    $note = $request->input('note', '');
    $device_id = (int)$request->input('device_id', 0);

    if($panel_type == 'passive') {$panel_type = $request->input('panel_tech'); $device_id = 0;}
        // Проверка обязательных полей
    if ($start_unit <= 0 || $start_unit > $data['custom_racks'][$rack_id]['units']) {
        session()->flash('error', 'Юнит назначения - не существует');
        header("Location: " . url('plugin/CablingJournal?location_id=' . $location_id . '&rack_id=' . $rack_id));
        exit;
    }
    if($device_id > 0) {
        $device = DB::table('devices')->where('device_id', $device_id)->first(); 
        if($device->sysName == '') {$name = $device->hostname;} else {$name = $device->sysName;} 
        $model = $device->sysDescr;
    }
    else {$model = $request->input('model',''); $name = $request->input('name','');}    
        
        $port_count = $request->input('port_count', 0);
        if ($name && $rack_id) {
            $panels = $data['custom_panels'] ?? [];
            $newId = empty($panels) ? 1 : max(array_keys($panels)) + 1;
            $data['custom_panels'][$newId] = [
                'id' => $newId,
                'rack_id' => $rack_id,
                'name' => $name,
                'type' => $panel_type,
                'model' => $model,
                'distance_from_rack' => 0,
                'note' => $note,
                'unit_count' => $unit_count,
                'start_unit' => $start_unit,
                'port_count' => $port_count,
                'device_id' => $device_id
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
            session()->flash('success', 'Панель добавлена в шкаф');

        } else {
            session()->flash('error', 'Ошибка: не заполнено имя панели');
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
elseif ($action === 'edit_panel') {
    $this->log_it("edit_panel:");
    
    $panel_id = (int)$request->input('panel_id');
    $rack_id = (int)$request->input('rack_id');
    $location_id = (int)$request->input('location_id', 0);
    $new_name = trim($request->input('name', ''));
    $new_model = trim($request->input('model', ''));
    $new_note = trim($request->input('note', ''));
    $new_start_unit = (int)$request->input('new_start_unit', 0);
    

    // Загружаем данные
    $data = file_exists($this->dbPath) ? include $this->dbPath : ['custom_racks' => [], 'custom_panels' => []];

    if (!isset($data['custom_panels'][$panel_id])) {
        session()->flash('error', 'панель не найдена');
        session()->save();
        header("Location: " . url("plugin/CablingJournal?location_id=$location_id&rack_id=$rack_id"));
        exit;
    }

    $panel = &$data['custom_panels'][$panel_id];
    $old_start_unit = $panel['start_unit'];
    $unit_count = $panel['unit_count'] ?? 1;

    if ($panel['type'] == 'device') {
        $device = DB::table('devices')->where('device_id', $panel['device_id'])->first(); 
        if($device->sysName == '') {$new_name = $device->hostname;} else {$new_name = $device->sysName;} 
        $new_model = $device->sysDescr;
    }

    $this->log_it("custom_panels: ".$panel_id);
    $this->log_it($panel);
    // Валидация имени
    if (empty($new_name)) {
        session()->flash('error', 'Имя панели не может быть пустым');
        session()->save();
        header("Location: " . url("plugin/CablingJournal?location_id=$location_id&rack_id=$rack_id"));
        exit;
    }

    // Проверка границ шкафа
    $rack = $data['custom_racks'][$rack_id] ?? null;
    if (!$rack) {
        session()->flash('error', 'Шкаф не найден');
        session()->save();
        header("Location: " . url("plugin/CablingJournal?location_id=$location_id"));
        exit;
    }
    $max_units = $rack['units'];
    if ($new_start_unit < 1 || $new_start_unit + $unit_count - 1 > $max_units) {
        session()->flash('error', "Юнит назначения ($new_start_unit) выходит за пределы шкафа (1-$max_units)");
        session()->save();
        header("Location: " . url("plugin/CablingJournal?location_id=$location_id&rack_id=$rack_id"));
        exit;
    }

    // Проверка конфликта с другими панелями и устройствами
    $conflict = false;
    // Панели
    foreach ($data['custom_panels'] as $id => $p) {
        if ($id == $panel_id) continue;
        if ($p['rack_id'] != $rack_id) continue;
        $p_start = $p['start_unit'] ?? 1;
        $p_name = $p['name'];
        $p_id = $p['id'];
        $p_model = $p['model'];
        $p_unit_count = $p['unit_count'];
        
        $p_end = $p_start + ($p['unit_count'] ?? 1) - 1;
        $new_end = $new_start_unit + $unit_count - 1;
        if (($new_start_unit >= $p_start && $new_start_unit <= $p_end) || ($new_end >= $p_start && $new_end <= $p_end) ) {
            $conflict = true;
            break;
        }
    }

    if ($conflict) {
        session()->flash('error', 'Конфликт: новый начальный юнит занят другим оборудованием');
        session()->save();
        header("Location: " . url("plugin/CablingJournal?location_id=$location_id&rack_id=$rack_id"));
        exit;
    }

    // Обновляем поля
    $panel['name'] = $new_name;
    $panel['model'] = $new_model;
    $panel['note'] = $new_note;
    $panel['start_unit'] = $new_start_unit;

    // Сохраняем данные
    $code = "<?php\nreturn " . var_export($data, true) . ";";
    file_put_contents($this->dbPath, $code);
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($this->dbPath, true);
    }
    clearstatcache(true, $this->dbPath);

    session()->flash('success', 'Панель обновлена');
    session()->save();
    header("Location: " . url("plugin/CablingJournal?location_id=$location_id&rack_id=$rack_id"));
    exit;
}
elseif ($action === 'delete_item') {
    $rack_id = (int)$request->input('rack_id');
    $unit = (int)$request->input('unit');
    $location_id = (int)$request->input('location_id', 0);

    // Загружаем актуальные данные
    $data = file_exists($this->dbPath) ? include $this->dbPath : ['custom_racks' => [], 'custom_panels' => [], 'custom_rack_devices' => []];

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

    session()->flash('success', 'Элемент удалён из шкафа');
    header("Location: " . url('plugin/CablingJournal?location_id=' . $location_id . '&rack_id=' . $rack_id));
    exit;
}
elseif ($action === 'update_port_note') {
    $port_id = (int)$request->input('port_id');
    $panel_id = (int)$request->input('panel_id');
    $rack_id = (int)$request->input('rack_id');
    $location_id = (int)$request->input('location_id');
    $note = $request->input('note', '');
    $status = $request->input('status', 'Active');
    $fiber_color = $request->input('fiber_color', null);

    // Загружаем актуальные данные
    $data = file_exists($this->dbPath) ? include $this->dbPath : [];

    if ($port_id && isset($data['custom_panel_ports'][$port_id])) {
        $data['custom_panel_ports'][$port_id]['note'] = $note;
        $data['custom_panel_ports'][$port_id]['status'] = $status;
        if ($fiber_color !== null) {
            $data['custom_panel_ports'][$port_id]['fiber_color'] = $fiber_color;
        }
        // Сохраняем
        $code = "<?php\nreturn " . var_export($data, true) . ";";
        file_put_contents($this->dbPath, $code);
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->dbPath, true);
        }
        clearstatcache(true, $this->dbPath);
        session()->flash('success', 'Порт обновлён');
    } else {
        session()->flash('error', 'Ошибка: порт не найден');
    }

    // Редирект обратно на страницу панели
    header("Location: " . url('plugin/CablingJournal?panel_id=' . $panel_id . '&rack_id=' . $rack_id . '&location_id=' . $location_id));
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
        'selected_panel'     => $selectedPanelId,
            'selected_rack' => 0,
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
        'selected_panel'     => $selectedPanelId,
            'location_name' => $currentLocation->location ?? 'Неизвестно',
            'racks' => $racks,
        ];
    }
    
    if($selectedLocationId != 0 && $selectedRackId != 0 && $selectedPanelId == 0){
    // --- РЕЖИМ 3: просмотр конкретного шкафа ---
    $rack = $data['custom_racks'][$selectedRackId] ?? null;
    if (!$rack) {
        // Если шкаф не найден, возвращаемся к списку локаций
        header("Location: ".url('plugin/CablingJournal?location_id=' . $selectedLocationId));
        exit;
    }
    $devicesList = DB::table('devices')->orderBy('hostname')->get(['device_id', 'hostname', 'sysName','hardware']);
    $maxUnits = $rack['units'] ?? 42;
    $occupiedUnits = [];

    // Панели в шкафу (distance_from_rack == 0)
    $panels = collect($data['custom_panels'] ?? [])->where('rack_id', $selectedRackId) ->where('distance_from_rack', 0);
    foreach ($panels as $p) {
        if($p['device_id'] > 0) {
            $panelPorts = DB::table('ports')
            ->where('device_id', $p['device_id'])
            ->select('ifOperStatus', 'ifIndex', 'ifName', 'port_id', 'ifAlias')
            ->orderBy('ifIndex', 'asc')
            ->limit(52)
            ->get()
            ->map(function ($item) { return (array) $item; })
            ->toArray();
        }
        else{
            $panelPorts = collect($data['custom_panel_ports'] ?? [])
        ->where('panel_id', $p['id'])
        ->sortBy('port_number')
        ->values()
        ->toArray();    
        }
        $unit = $p['start_unit'] ?? 1;
	    // Получаем порты для этой панели из custom_panel_ports
    
        $occupiedUnits[$unit] = [
            'type'       => $p['type'],
            'name'       => $p['name'] ?? 'Панель',
            'model'      => $p['model'] ?? '',
            'id'         => $p['id'],
            'unit_count' => $p['unit_count'] ?? '1',
	        'ports'      => $panelPorts,
            'port_count'      => $p['port_count'] ?? '',
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
        'selected_panel'     => $selectedPanelId,
        'free_units' => $freeUnits
    ];
    }
    else{
        //Режим 4 внутри панели, редактируем порты
    $currentLocation = DB::table('locations')->where('id', $selectedLocationId)->first();
    $rack = $data['custom_racks'][$selectedRackId] ?? null;
    $panel = collect($data['custom_panels'] ?? [])->where('id', $selectedPanelId)->first();
    $max_units = $rack['units'] ?? 42;

    $panelPorts = collect($data['custom_panel_ports'] ?? [])
        ->where('panel_id', $selectedPanelId)
        ->sortBy('port_number')
        ->values()
        ->toArray();

    return [
        'title'          => 'Кабельный журнал',
        'selected_location' => $selectedLocationId,
        'selected_rack'     => $selectedRackId,
        'selected_panel'    => $selectedPanelId,
        'location_name'     => $currentLocation->location ?? 'Неизвестно',
        'rack'              => $rack,
        'max_units'         => $max_units,
        'ports'             => $panelPorts,
        'panel'             => $panel,
    ];
    }
    
}

}
