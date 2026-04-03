<?php
// CablingJournal.php
namespace App\Plugins\CablingJournal;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

if (!Auth::check()) { die('Access Denied'); }

$rack_id = request()->get('rack_id');

// Подключаем стили и скрипты
echo '<link rel="stylesheet" href="/plugins/CablingJournal/public/css/style.css">';
echo '<script src="/plugins/CablingJournal/public/js/cabling.js" defer></script>';
echo '<div id="custom-tooltip"></div>';

if (!$rack_id) {
    // Получаем список шкафов
    $racks = DB::select("SELECT r.*, l.location FROM custom_racks r LEFT JOIN locations l ON r.location_id = l.id");
    include 'resources/views/list.php';
} else {
    // Получаем данные конкретного шкафа
    $rack = DB::selectOne("SELECT * FROM custom_racks WHERE id = ?", [$rack_id]);
    
    // Собираем девайсы и панели (в массив по юнитам)
    $items = [];
    // ... тут логика формирования массива $items из твоего first-step.txt ...

    include 'resources/views/rack.php';
}
