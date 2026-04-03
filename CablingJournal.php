<?php
namespace App\Plugins\CablingJournal;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

if (!Auth::check()) { die('Access Denied'); }

$rack_id = request()->get('rack_id');

// --- ОБРАБОТЧИК СОХРАНЕНИЯ (POST) ---
if (request()->isMethod('post') && request()->get('action') == 'add_item') {
    $r_id = intval(request()->get('rack_id'));
    $u_id = intval(request()->get('start_unit'));
    
    if (request()->get('item_type') == 'panel') {
        $id = DB::table('custom_panels')->insertGetId([
            'rack_id' => $r_id,
            'name' => request()->get('panel_name'),
            'type' => request()->get('panel_kind'),
            'start_unit' => $u_id
        ]);
        // Генерация портов
        for ($i = 1; $i <= intval(request()->get('port_count')); $i++) {
            DB::table('custom_panel_ports')->insert(['panel_id' => $id, 'port_number' => $i]);
        }
    } else {
        DB::table('custom_rack_devices')->insert([
            'rack_id' => $r_id,
            'device_id' => intval(request()->get('device_id')),
            'start_unit' => $u_id
        ]);
    }
    return redirect()->to(url()->current() . "?rack_id=$r_id");
}

// --- ПОДКЛЮЧЕНИЕ РЕСУРСОВ ---
echo '<link rel="stylesheet" href="/plugins/CablingJournal/public/css/style.css">';
echo '<script src="/plugins/CablingJournal/public/js/cabling.js" defer></script>';
echo '<div id="custom-tooltip"></div>';

if (!$rack_id) {
    $racks = DB::select("SELECT r.*, l.location FROM custom_racks r LEFT JOIN locations l ON r.location_id = l.id");
    include 'resources/views/list.php';
} else {
    $rack = DB::selectOne("SELECT * FROM custom_racks WHERE id = ?", [$rack_id]);
    $items = [];

// 1. Собираем активку
$devices = DB::select("SELECT rd.*, d.hostname FROM custom_rack_devices rd JOIN devices d ON rd.device_id = d.device_id WHERE rd.rack_id = ?", [$rack_id]);
foreach ($devices as $d) {
    // Тут вызываем функцию генерации сетки портов (можно вынести в хелпер)
    $ports = DB::select("SELECT ifOperStatus, ifName, port_id, ifAlias FROM ports WHERE device_id = ? ORDER BY ifIndex ASC LIMIT 52", [$d->device_id]);
    
    // Генерируем HTML сетки портов (упрощенно для примера)
    $html = "<div class='port-grid'><div class='row-container'>";
    foreach (array_chunk($ports, 24) as $row) {
        $html .= "<div style='display:flex; gap:2px;'>";
        foreach ($row as $p) {
            $status = ($p->ifOperStatus == 'up') ? 'port-up' : 'port-down';
            $tip = "Port {$p->ifName} | " . ($p->ifAlias ?: '');
            $html .= "<a href='/device/device={$d->device_id}/tab=port/port={$p->port_id}/' class='port-box $status' data-tip='$tip'></a>";
        }
        $html .= "</div>";
    }
    $html .= "</div></div>";

    $items[$d->start_unit] = [
        'type' => 'device',
        'name' => $d->hostname,
        'id'   => $d->device_id,
        'ports_html' => $html
    ];
}

// 2. Собираем панели (внутренние)
$panels = DB::select("SELECT * FROM custom_panels WHERE rack_id = ? AND distance_from_rack = 0", [$rack_id]);
foreach ($panels as $p) {
    $p_ports = DB::select("SELECT * FROM custom_panel_ports WHERE panel_id = ? ORDER BY port_number ASC", [$p->id]);
    $html = "<div class='port-grid'>";
    foreach ($p_ports as $pp) {
        $color = $pp->fiber_color ?: '#666';
        $html .= "<div class='port-box' style='background:$color' data-tip='Panel Port {$pp->port_number}'></div>";
    }
    $html .= "</div>";

    $items[$p->start_unit] = [
        'type' => 'panel',
        'name' => $p->name,
        'ports_html' => $html
    ];
}

// 3. Собираем внешние узлы (муфты) аналогично...
// ... (логика для $external_nodes) ...

    include 'resources/views/rack.php';
    include 'resources/views/modals.php'; // Подключаем модалки в конце

}
