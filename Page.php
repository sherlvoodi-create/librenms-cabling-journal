<?php
namespace App\Plugins\CablingJournal;

use App\Plugins\Hooks\PageHook;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Authenticatable;

class Page extends PageHook
{
    // Путь к шаблону относительно resources/views
    public string $view = 'plugins.CablingJournal.resources.views.page';

    /**
     * Разрешаем доступ всем авторизованным пользователям
     */
    public function authorize(Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Основная логика: обработка ввода и подготовка данных для таблицы
     */
    public function data(): array
    {
        $request = request();

        // 1. Логика сохранения (из вашего скрипта)
        if ($request->isMethod('post')) {
            DB::table('cabling_journal')->insert([
                'device_id'   => $request->input('device_id'),
                'port_id'     => $request->input('port_id'),
                'cable_type'  => $request->input('cable_type'),
                'description' => $request->input('description'),
                // 'created_at' => now(), // Раскомментируйте, если добавили это поле в БД
            ]);
            
            session()->flash('status', 'Запись в кабельный журнал успешно добавлена!');
        }

        // 2. Получение данных (Аналог вашего SQL запроса с JOIN)
        // Мы соединяем записи журнала с таблицами LibreNMS, чтобы получить имена
        $cables = DB::table('cabling_journal')
            ->leftJoin('devices', 'cabling_journal.device_id', '=', 'devices.device_id')
            ->leftJoin('ports', 'cabling_journal.port_id', '=', 'ports.port_id')
            ->select(
                'cabling_journal.*', 
                'devices.hostname as device_name', 
                'ports.ifName as port_name'
            )
            ->orderBy('cabling_journal.id', 'desc')
            ->get();

        // 3. Данные для выпадающих списков (чтобы удобно выбирать в форме)
        $devices = DB::table('devices')->select('device_id', 'hostname')->orderBy('hostname')->get();

        return [
            'cables'  => $cables,
            'devices' => $devices,
            'title'   => 'Кабельный журнал'
        ];
    }
}
