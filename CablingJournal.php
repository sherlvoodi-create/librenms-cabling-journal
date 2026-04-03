<?php
namespace App\Plugins\CablingJournal;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

if (!Auth::check()) { die('Access Denied'); }

$rack_id = request()->get('rack_id');

// --- ОБРАБОТЧИК POST (СОХРАНЕНИЕ) ---
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
        $p_count = intval(request()->get('port_count'));
        for ($i = 1; $i <= $p_count; $i++) {
            DB::table('custom_panel_ports')->insert(['panel_id' => $id, 'port_number' => $i]);
        }
    } else {
        DB::table('custom_rack_devices')->insert([
            'rack_id' => $r_id,
            'device_id' => intval(request()->get('device_id')),
            'start_unit' => $u_id
        ]);
    }
    header("Location: " . url()->current() . "?rack_id=$r_id");
    exit;
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
    
    // Активка
    $devices = DB::select("SELECT rd.*, d.hostname FROM custom_rack_devices rd JOIN devices d ON rd.device_id = d.device_id WHERE rd.rack_id = ?", [$rack_id]);
    foreach ($devices as $d) {
        $ports = DB::select("SELECT ifOperStatus, ifName, port_id, ifAlias FROM ports WHERE device_id = ? ORDER BY ifIndex ASC LIMIT 52", [$d->device_id]);
        $items[$d->start_unit] = ['type' => 'device', 'name' => $d->hostname, 'id' => $d->device_id, 'ports' => $ports];
    }
    
    // Панели
    $panels = DB::select("SELECT * FROM custom_panels WHERE rack_id = ? AND distance_from_rack = 0", [$rack_id]);
    foreach ($panels as $p) {
        $p_ports = DB::select("SELECT * FROM custom_panel_ports WHERE panel_id = ? ORDER BY port_number ASC", [$p->id]);
        $items[$p->start_unit] = ['type' => 'panel', 'name' => $p->name, 'id' => $p->id, 'ports' => $p_ports];
    }

    $external_nodes = DB::select("SELECT * FROM custom_panels WHERE rack_id = ? AND distance_from_rack > 0", [$rack_id]);

    include 'resources/views/rack.php';
    include 'resources/views/modals.php';
}
