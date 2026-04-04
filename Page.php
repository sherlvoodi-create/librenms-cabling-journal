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

        // 1. Обработка POST (Сохранение нового шкафа/рэка из вашего скрипта)
        if ($request->isMethod('post') && $request->has('add_rack')) {
            DB::table('custom_racks')->insert([
                'name'        => $request->input('name'),
                'location_id' => $request->input('location_id'),
                'units'       => $request->input('units', 42),
                'description' => $request->input('description'),
            ]);
            
            session()->flash('status', 'Стойка успешно добавлена!');
        }

        // 2. Получение списка шкафов с привязкой к локациям (Ваш SQL с LEFT JOIN)
        // SQL: SELECT r.*, l.location FROM custom_racks r LEFT JOIN locations l ON r.location_id = l.id
        $racks = DB::table('custom_racks as r')
            ->leftJoin('locations as l', 'r.location_id', '=', 'l.id')
            ->select('r.*', 'l.location as location_name')
            ->orderBy('r.name', 'asc')
            ->get();

        // 3. Получение списка локаций для выпадающего списка в форме
        $locations = DB::table('locations')
            ->select('id', 'location')
            ->orderBy('location')
            ->get();

        return [
            'racks'     => $racks,
            'locations' => $locations,
            'title'     => 'Управление кабельным журналом (Стойки)'
        ];
    }
}
