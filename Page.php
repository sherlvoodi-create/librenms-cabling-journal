<?php
namespace App\Plugins\CablingJournal;

use App\Plugins\Hooks\PageHook;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

class Page extends PageHook
{
    // Путь к шаблону относительно resources/views
//    public string $view = 'plugins.CablingJournal.resources.views.page';
//    public string $view = 'resources.views.page';
    private string $dbPath;

    public function __construct()
    {
        file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt',"Call  __construct()\n",FILE_APPEND);
        // Путь к файлу базы данных внутри плагина
        $this->dbPath = __DIR__ . '/data/database.php';
    }

    /**
     * Разрешаем доступ всем авторизованным пользователям
     */
    public function authorize(Authenticatable $user): bool
    {
        file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt',"Call  authorize()\n",FILE_APPEND);
        return true;
    }

    /**
     * Основная логика: обработка ввода и подготовка данных для таблицы
     */
    public function data(): array
    {
        file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt',"Call  data()\n",FILE_APPEND);
       $request = \Illuminate\Support\Facades\Request::isMethod('post');
        $isGET = \Illuminate\Support\Facades\Request::isMethod('get');
        $selectedLocationId = \Illuminate\Support\Facades\Request::get('location_id');
        $selectedRackId = \Illuminate\Support\Facades\Request::get('rack_id');
        if ($selectedLocationId == '') {$selectedLocationId = 0;}
        if ($selectedRackId == '') {$selectedRackId = 0;}
        file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt', "post:\t". print_r($request,true) ."\n",FILE_APPEND);
        file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt', "GET:\t". print_r($isGET,true) ."\n",FILE_APPEND);
        file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt', "GET:\t". print_r($selectedLocationId,true) ."\n",FILE_APPEND);
        // 1. Загружаем данные из файла (или создаем пустой массив)
        $data = file_exists($this->dbPath) ? include $this->dbPath : ['racks' => []];

        /* 2. Обработка сохранения (POST)
        if ($request->isMethod('post')) {
            $newRack = [
                'id'          => time(), // Простой ID на основе времени
                'name'        => $request->input('name'),
                'location'    => $request->input('location'),
                'units'       => $request->input('units', 42),
                'description' => $request->input('description'),
            ];

            $data['racks'][] = $newRack;

            // Сохраняем обратно в файл через var_export
            $code = "<?php\nreturn " . var_export($data, true) . ";";
            file_put_contents($this->dbPath, $code);

            session()->flash('status', 'Запись сохранена в файл!');
        }
        */
        if ($selectedLocationId == 0) {
        // --- РЕЖИМ 1: Список всех Зданий ---
        $locations = DB::table('locations')->orderBy('location', 'asc')->get();
        //file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt', "LOCATIONS:\t". print_r($locations,true) ."\n",FILE_APPEND);
        return [
            'mode' => 'all',
            'locations' => $locations,
            'title'             => 'Кабельный журнал',
            'selected_location' => $selectedLocationId, // Для @if($selected_location)
        ];
        }
        elseif ($selectedRackId == 0){
        // --- РЕЖИМ 2: Список всех шкаф в Заднии ---
            // Получаем объект выбранной локации
            $currentLocation = DB::table('locations')->where('id', $selectedLocationId)->first();

            return [
            'racks' => $data['custom_racks'],
            'title'             => 'Кабельный журнал',
            'selected_rack' => $selectedRackId, // Для @if($selected_location)
            'selected_location' => $selectedLocationId, // Для @if($selected_location)
            'location_name' => $currentLocation->location ?? 'Неизвестно',
        ];

        }
        else{
        // --- РЕЖИМ 3: Список всех devices в Шкафу ---
            $rackDevices = collect($data['custom_rack_devices'] ?? [])->where('rack_id', $selectedRackId);
            $currentLocation = DB::table('locations')->where('id', $selectedLocationId)->first();

    foreach ($rackDevices as $rd) {
        // Подтягиваем имя устройства из БД LibreNMS
        $hostname = DB::table('devices')
            ->where('device_id', $rd['device_id'])
            ->value('hostname');
        // Получаем порты из БД LibreNMS
    $ports = DB::table('ports')
    ->where('device_id', $rd['device_id'])
    ->select('ifOperStatus', 'ifIndex', 'ifName', 'port_id', 'ifAlias')
    ->orderBy('ifIndex', 'asc')
    ->limit(52)
    ->get()
    ->map(function ($item) {
        return (array) $item; // Превращаем объект порта в массив
    })
    ->all();
        $items[$rd['start_unit']] = [
            'name' => $hostname ?? "Device ID: " . $rd['device_id'],
            'type' => 'device',
            'id'   => $rd['device_id'],
            'unit_count' => $rd['unit_count'],
            'ports' => $ports,
        ];
    }

    //Фильтруем пассивные панели (из custom_panels, где distance = 0)
    $panels = collect($db['custom_panels'] ?? [])
        ->where('rack_id', $selectedRackId)
        ->where('distance_from_rack', 0);

    foreach ($panels as $p) {
        // Предполагаем, что в custom_panels у вас есть поле start_unit
        $unit = $p['start_unit'] ?? 1;
        $model = $p['model'] ?? " ";

        $items[$unit] = [
            'name' => $p['name'],
            'type' => $p['type'],
            'id'   => $p['id'],
            'unit_count' => $rd['unit_count'],
            'mode' => $model,
        ];
    }
        return [
            'racks' => $data['custom_racks'][$selectedRackId],
            'title'             => 'Кабельный журнал',
            'selected_rack' => $selectedRackId, // Для @if($selected_location)
            'selected_location' => $selectedLocationId, // Для @if($selected_location)
            'location_name' => $currentLocation->location ?? 'Неизвестно',
            'panels' => $items,
        ];

    }
//      file_put_contents('/opt/librenms/app/Plugins/CablingJournal/log.txt',print_r($data,true),FILE_APPEND);
    }
}

