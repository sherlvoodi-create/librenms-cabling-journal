<?php
// app/Plugins/CablingJournal/Menu.php
namespace App\Plugins\CablingJournal;

use LibreNMS\Interfaces\Plugins\MenuEntryHook;

class Menu implements MenuEntryHook
{
    public function menu()
    {
        return [
            'title' => 'Cabling Journal',
            'url' => 'plugin/CablingJournal',
            'icon' => 'fa fa-microchip',
        ];
    }
}
