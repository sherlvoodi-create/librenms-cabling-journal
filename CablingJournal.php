<?php

namespace App\Plugins\CablingJournal;

use LibreNMS\Interfaces\Plugins\Plugin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CablingJournal implements Plugin
{
    /**
     * Основной метод входа в плагин
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $rack_id = request()->get('rack_id');

        // --- ОБРАБОТЧИК POST (ДОБАВЛЕНИЕ) ---
        if (request()->isMethod('post') && request()->get('action') == 'add_item') {
            return $this->handlePost(request());
        }

        // Подключаем ресурсы (пути через симлинк в public)
        echo '<link rel="stylesheet" href="/plugins/CablingJournal/public/css/style.css">';
        echo '<script src="/plugins/CablingJournal/public/js/cabling.js" defer></script>';
        echo '<div id="custom-tooltip"></div>';

        if (!$rack_id) {
            return $this->showList();
        } else {
            return $this->showRack($rack_id);
        }
    }

    /**
     * Вывод списка всех шкафов
     */
    private function showList()
    {
        $racks = DB::select("SELECT r.*, l.location FROM custom_racks r LEFT JOIN locations l ON r.location_id = l.id");
        
        // Используем твой файл шаблона из resources/views/list.php
        ob_start();
        include __DIR__ . '/resources/views/list.php';
        return ob_get_clean();
    }

    /**
     * Вывод конкретного шкафа (Rack Explorer)
     */
    private function showRack($rack_id)
    {
        $rack = DB::selectOne("SELECT * FROM custom_racks WHERE id = ?", [$rack_id]);
        if (!$rack) return "Шкаф не найден";

        $items = [];
        
        // Собираем Активку
        $devices = DB::select("SELECT rd.*, d.hostname FROM custom_rack_devices rd JOIN devices d ON rd.device_id = d.device_id WHERE rd.rack_id = ?", [$rack_id]);
        foreach ($devices as $d) {
            $ports = DB::select("SELECT ifOperStatus, ifName, port_id, ifAlias FROM ports WHERE device_id = ? ORDER BY ifIndex ASC LIMIT 52", [$d->device_id]);
            $items[$d->start_unit] = [
                'type' => 'device', 
                'name' => $d->hostname, 
                'id' => $d->device_id, 
                'ports' => $ports
            ];
        }
        
        // Собираем Панели (внутри стойки)
        $panels = DB::select("SELECT * FROM custom_panels WHERE rack_id = ? AND distance_from_rack = 0", [$rack_id]);
        foreach ($panels as $p) {
            $p_ports = DB::select("SELECT * FROM custom_panel_ports WHERE panel_id = ? ORDER BY port_number ASC", [$p->id]);
            $items[$p->start_unit] = [
                'type' => 'panel', 
                'name' => $p->name, 
                'id' => $p->id, 
                'ports' => $p_ports
            ];
        }

        // Внешние узлы
        $external_nodes = DB::select("SELECT * FROM custom_panels WHERE rack_id = ? AND distance_from_rack > 0", [$rack_id]);
        foreach ($external_nodes as &$node) {
            $node->ports = DB::select("SELECT * FROM custom_panel_ports WHERE panel_id = ? ORDER BY port_number ASC", [$node->id]);
        }

        // Отрисовка через буфер, чтобы вписаться в дизайн LibreNMS
        ob_start();
        include __DIR__ . '/resources/views/rack.php';
        include __DIR__ . '/resources/views/modals.php';
        return ob_get_clean();
    }

    /**
     * Обработка сохранения данных
     */
    private function handlePost(Request $request)
    {
        $r_id = intval($request->get('rack_id'));
        $u_id = intval($request->get('start_unit'));
        
        if ($request->get('item_type') == 'panel') {
            $id = DB::table('custom_panels')->insertGetId([
                'rack_id' => $r_id,
                'name' => $request->get('panel_name'),
                'type' => $request->get('panel_kind'),
                'start_unit' => $u_id
            ]);
            $p_count = intval($request->get('port_count'));
            for ($i = 1; $i <= $p_count; $i++) {
                DB::table('custom_panel_ports')->insert(['panel_id' => $id, 'port_number' => $i]);
            }
        } else {
            DB::table('custom_rack_devices')->insert([
                'rack_id' => $r_id,
                'device_id' => intval($request->get('device_id')),
                'start_unit' => $u_id
            ]);
        }
        return redirect()->to(url()->current() . "?rack_id=$r_id");
    }
}
