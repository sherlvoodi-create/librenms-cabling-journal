<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
	    {{-- Хлебные крошки --}}
<ol class="breadcrumb" style="background: none; padding-left: 0; margin-bottom: 20px;">
    <li><a href="{{ url('plugin/CablingJournal?location_id=0') }}"><i class="fa fa-home"></i> Локации</a></li>
    @if($selected_location != 0)
        <li>
            @if($selected_rack == 0)
                <strong>{{ $location_name }}</strong>
            @else
                <a href="{{ url('plugin/CablingJournal?location_id='.$selected_location) }}">{{ $location_name }}</a>
            @endif
        </li>
    @endif
    @if($selected_rack != 0 && $selected_panel != 0)
        <li>
            <a href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$selected_rack) }}">{{ $rack['name'] ?? 'Шкаф' }} ({{ $max_units }}U)</a>
        </li>
    @elseif($selected_rack != 0 && $selected_panel == 0)
        <li class="active">{{ $rack['name'] ?? 'Шкаф' }} ({{ $max_units }}U)</li>
    @endif
    @if($selected_panel != 0)
        <li class="active">{{ $panel['name'] ?? 'Панель' }}</li>
    @endif
</ol>

@if(session('success'))
    <div class="alert alert-success flash-message">{{ session('success') }}</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Находим все уведомления с классом flash-message
        const alerts = document.querySelectorAll('.flash-message');
        
        alerts.forEach(function(alert) {
            // Устанавливаем таймер на 3 секунды (3000 мс)
            setTimeout(function() {
                // Добавляем плавное исчезновение через CSS
                alert.style.transition = "opacity 0.6s ease";
                alert.style.opacity = "0";
                
                // Полностью удаляем элемент из верстки после завершения анимации
                setTimeout(() => alert.remove(), 600);
            }, 3000);
        });
    });
</script>
@endif
@if(session('error'))
    <div class="alert alert-danger flash-message">{{ session('error') }}</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Находим все уведомления с классом flash-message
        const alerts = document.querySelectorAll('.flash-message');
        
        alerts.forEach(function(alert) {
            // Устанавливаем таймер на 3 секунды (3000 мс)
            setTimeout(function() {
                // Добавляем плавное исчезновение через CSS
                alert.style.transition = "opacity 0.6s ease";
                alert.style.opacity = "0";
                
                // Полностью удаляем элемент из верстки после завершения анимации
                setTimeout(() => alert.remove(), 600);
            }, 3000);
        });
    });
</script>
@endif



            @if(!$selected_location || $selected_location == 0)
<div class="row">
    @foreach($locations as $loc)
        <div class="col-sm-4 col-md-2">
            <a href="{{ url('plugin/CablingJournal?location_id=' . $loc->id) }}" style="text-decoration: none; color: inherit;">
                <div class="panel panel-default panel-hover-effect" style="transition: all 0.2s; border-top: 3px solid #3498db; margin-bottom: 15px; border-radius: 4px;">
                    <div class="panel-body text-center" style="padding: 15px 10px;">
                        <div style="font-size: 18px; color: #e74c3c; margin-bottom: 5px;">
                            <i class="fa fa-building-o"></i>
                        </div>
                        <h5 style="margin: 0; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $loc->location }}">
                            {{ $loc->location }}
                        </h5>
                        <div style="margin-top: 5px; font-size: 10px; color: #999; text-transform: uppercase;">
                            ID: {{ $loc->id }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
<style>
    .panel-hover-effect:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-top-color: #e74c3c !important;
        background-color: #fcfcfc;
    }
</style>


            @elseif($selected_location != 0 && $selected_rack == 0)

{{-- Форма добавления шкафа --}}
<div class="panel panel-default">
    <div class="panel-heading" style="padding: 8px 15px;">
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleForm('addRackForm')">Добавить шкаф</button>
    </div>
    <div id="addRackForm" style="display:none;" class="panel-body">
        <form action="" method="GET">
            <input type="hidden" name="action" value="add_rack">
            <input type="hidden" name="location_id" value="{{ $selected_location }}">
            <div class="row">
                <div class="col-md-3">
                    <label>Название шкафа*</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Этаж</label>
                    <input type="text" name="floor" class="form-control" placeholder="например 2">
                </div>
                <div class="col-md-2">
                    <label>Высота (U)*</label>
                    <input type="number" name="units" class="form-control" value="42" required>
                </div>
                <div class="col-md-2">
                    <label>Тип</label>
                    <select name="type" class="form-control">
                        <option value="Rack">Rack</option>
                        <option value="Wall-Box">Wall-Box</option>
                        <option value="Cabinet">Cabinet</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Модель</label>
                    <input type="text" name="model" class="form-control" placeholder="например HyperLine 42U">
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-9">
                    <label>Примечание</label>
                    <input type="text" name="note" class="form-control" placeholder="доп. информация">
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Добавить шкаф</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Таблица шкафов --}}
<div class="panel panel-default">
    <div class="panel-heading">Список шкафов в этой локации</div>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-condensed">
            <thead>
                <tr><th>Имя шкафа</th><th>Этаж</th><th>Юниты</th><th>Тип</th><th>Заметки</th><th></th></tr>
            </thead>
            <tbody>
                @php $racksList = is_array($racks) ? $racks : []; @endphp
                @forelse($racksList as $rack)
                    @if($rack['location_id'] == $selected_location)
                    <tr>
                        <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;"><strong>{{ $rack['name'] }}</strong></td>
                        <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">{{ $rack['floor'] ?? '-' }}</td>
                        <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">{{ $rack['units'] }}U</td>
                        <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">{{ $rack['type'] }}</td>
                        <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;"><small>{{ $rack['note'] }}</small></td>
                        <td style="text-align: center;"><a href="javascript:void(0)" onclick="confirmDeleteRack({{ $rack['id'] }}, {{ $selected_location }})" class="text-danger" title="Удалить шкаф"><i class="fa fa-trash"></i></a></td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="6" class="text-center">В этой локации пока нет созданных шкафов.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});
function confirmDeleteRack(rackId, locationId) {
    if (confirm('Вы уверены, что хотите удалить этот шкаф? Все панели и устройства внутри него также будут удалены.')) {
        window.location.href = '{{ url("plugin/CablingJournal") }}?action=delete_rack&rack_id=' + rackId + '&location_id=' + locationId;
    }
}
function toggleForm(formId) {
    var formDiv = document.getElementById(formId);
    if (formDiv.style.display === 'none' || formDiv.style.display === '') {
        formDiv.style.display = 'block';
    } else {
        formDiv.style.display = 'none';
    }
}
</script>


            @elseif($selected_location != 0 && $selected_rack != 0 && $selected_panel == 0)

{{-- Форма добавления оборудования в шкаф --}}
<div class="panel panel-default">
    <div class="panel-heading">
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleForm('addPanelForm')">Добавить оборудование</button>
        <label style="margin: 0; display: flex; align-items: center; gap: 6px; font-weight: normal;">
            <input type="checkbox" id="hideEmptyUnits" checked onchange="toggleEmptyUnits()">
            Скрывать пустые юниты
        </label>
    </div>
    <div id="addPanelForm" class="panel-body" style="display:none;">
        <form action="" method="GET" novalidate>
            <input type="hidden" name="action" value="add_panel">
            <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
            <input type="hidden" name="location_id" value="{{ $selected_location }}">
            <div class="row">
                <div class="col-md-3">
                    <label>Тип:</label>
                    <select name="panel_type" id="panel_type" class="form-control">
                        <option value="device">Активное устройство (из LibreNMS)</option>
                        <option value="passive">Пассивная панель/компонент</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Начальный юнит Front(U):</label>
                    <select name="start_unit" id="start_unit" class="form-control" required>
                        @foreach($free_units_front as $unit)
                            <option value="{{ $unit }}">{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Количество юнитов (высота):</label>
                    <input type="number" name="unit_count" class="form-control" value="1">
                </div>
                <div class="col-md-3">
                    <label>Примечание:</label>
                    <input type="text" name="note" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Сторона шкафа:</label>
                    {{-- Добавляем id и onchange --}}
                    <select name="rack_side" id="rack_side_select" class="form-control" onchange="updateUnitList(this.value)">
                        <option value="front" selected>Front</option>
                        <option value="back">Back</option>
                    </select>
                </div>
            </div>
            <div id="device_select" class="row" style="margin-top:10px;">
                <div class="col-md-12">
                    <label>Устройство (из LibreNMS):</label>
                    <select name="device_id" class="form-control">
                        <option value="">-- Выберите устройство --</option>
                        @foreach($devices as $dev)
                            <option value="{{ $dev->device_id }}">{{ $dev->hostname }} ({{ $dev->sysName }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="passive_fields" class="row" style="margin-top:10px; display:none;">
                <div class="col-md-3">
                    <label>Имя панели:</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Технология:</label>
                    <select name="panel_tech" id="panel_tech" class="form-control">
                        <option value="panel">Медная (UTP)</option>
                        <option value="fiber">Оптическая (LC\SC)</option>
                        <option value="socket220">Блок розеток</option>
                        <option value="UPS">ИБП</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Количество портов:</label>
                    <select name="port_count" id="port_count" class="form-control">
                        <option value="24">24</option>
                        <option value="48">48</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Модель:</label>
                    <input type="text" name="model" class="form-control" placeholder="напр. Cat6a, LC">
                </div>
            </div>
            <div class="row" style="margin-top:10px;">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Контейнер шкафа --}}
<div class="rack-container" style="background:#fff; border:1px solid #ccc; max-width: 100%; overflow-x: auto;">
    <div class="rack-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
    {{-- Левая колонка (Front) --}}
    <div class="rack-column" style="flex: 1; min-width: 250px;">
        <div class="panel panel-default" style="margin-bottom: 5px;">
            <div class="panel-heading text-center"><strong>Передняя сторона (Front)</strong></div>
        </div>
        @for ($u = 1; $u <= $max_units; $u++)
            @php
                $item = $occupied_units_front[$u] ?? null;
                $hasItem = !is_null($item);
                $isEmpty = !$hasItem;
                $unitClass = $isEmpty ? 'empty-u' : ($item['type'] == 'device' ? 'occupied' : 'panel');
            @endphp
            <div class="rack-unit {{ $unitClass }}" data-unit="{{ $u }}">
                <div class="unit-num">{{ $u }}</div>
                <div class="unit-content">
@if($hasItem)
            @if($item['type'] == 'device')
                {{-- Блок просмотра устройства --}}
                <div id="device-view-{{ $item['id'] }}-{{ $u }}" class="device-view">
                    <div class="item-title">
                        <a href="/device/device={{ $item['id'] }}/" target="_blank"><strong>{{ $item['name'] }}</strong></a>
                        <div class="text-muted small">{{ $item['model'] }}</div>
                        <a href="javascript:void(0)" onclick="editDevice({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Переместить устройство"><i class="fa fa-arrows"></i></a>
                        <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                    </div>
                    @if(!empty($item['ports']))
                        @php
                            $row1 = array_slice($item['ports'], 0, 24);
                            $row2 = array_slice($item['ports'], 24, 24);
                            $sfp  = array_slice($item['ports'], 48);
                        @endphp
                        <div style="display: flex; align-items: flex-end; gap: 10px; margin-top: 6px;">
                            <div class="port-grid" style="background: #222; padding: 6px; border-radius: 4px;">
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    @foreach ([$row1, $row2] as $row)
                                        <div style="display: flex; gap: 2px;">
                                            @foreach ($row as $p)
                                                @php
                                                    $st = ($p['ifOperStatus'] == 'up') ? 'port-up' : 'port-down';
                                                    $port_url = "/device/device={$item['id']}/tab=port/port={$p['port_id']}/";
                                                    $full_title = !empty($p['ifAlias']) ? htmlspecialchars($p['ifAlias']) : $p['ifName'];
                                                @endphp
                                                <a href="{{ $port_url }}" target="_blank" class="port-box {{ $st }}" data-tip="{{ $full_title }}"></a>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                                @if(!empty($sfp))
                                    <div class="sfp-block" style="background: #222; padding: 6px; border-radius: 4px; display: flex; gap: 3px; align-items: flex-end;">
                                        @foreach ($sfp as $p)
                                            @php
                                                $st = ($p['ifOperStatus'] == 'up') ? 'port-up' : 'port-down';
                                                $port_url = "/device/device={$item['id']}/tab=port/port={$p['port_id']}/";
                                                $full_title = !empty($p['ifAlias']) ? htmlspecialchars($p['ifAlias']) : $p['ifName'];
                                            @endphp
                                            <a href="{{ $port_url }}" target="_blank" class="port-box {{ $st }}" data-tip="{{ $full_title }} (SFP)"></a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                {{-- Блок редактирования устройства (только перемещение) --}}
                <div id="device-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <input type="hidden" name="old_unit" value="{{ $u }}">
                        <div class="item-title">
                            <strong>Перемещение {{ $item['name'] }}</strong>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                    <option value="front"  selected>Front</option>
                                    <option value="back">Back</option>
                                </select>
                            <label>Новый начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>

            @elseif($item['type'] == 'panel' || $item['type'] == 'fiber')
                {{-- Блок просмотра панели --}}
                <div id="panel-view-{{ $item['id'] }}-{{ $u }}" class="panel-view">
                    <div class="item-title">
                        <strong>{{ $item['name'] }}</strong>
                        <a href="javascript:void(0)" onclick="editPanel({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Редактировать панель"><i class="fa fa-pencil"></i></a>
                        <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                    </div>
                    <div class="text-muted small">{{ $item['model'] }}</div>
                    <div class="panel-note small text-muted">{{ $item['note'] }}</div>
                    @if(!empty($item['ports']))
                        <div class="panel-ports" style="margin-top: 6px;">
                            @php $portsChunked = array_chunk($item['ports'], 24); @endphp
                            @foreach($portsChunked as $chunk)
                                <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                    @foreach($chunk as $port)
                                        @php
                                            $tooltip = "Порт {$port['port_number']}";
                                            if (!empty($port['note'])) $tooltip .= ": {$port['note']}";
                                            if (!empty($port['fiber_color'])) $tooltip .= " (Цвет: {$port['fiber_color']})";
                                            $bgColor = !empty($port['fiber_color']) ? $port['fiber_color'] : '#555';
                                            $border = (in_array($bgColor, ['White', 'Yellow', 'Lime', 'Cyan'])) ? '1px solid #888' : '1px solid #000';
                                        @endphp
                                        <div class="port-box" style="background: {{ $bgColor }}; border: {{ $border }};" data-tip="{{ $tooltip }}"></div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">[Нет портов]</div>
                    @endif
                    <div class="text-muted small" style="margin-top: 4px;">
                        <a href="{{ url('plugin/CablingJournal?panel_id='.$item['id'].'&rack_id='.$selected_rack.'&location_id='.$selected_location) }}" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> Редактировать порты</a>
                    </div>
                </div>
                {{-- Блок редактирования панели --}}
                <div id="panel-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <div class="item-title">
                            <input type="text" name="name" class="form-control input-sm" style="width: auto; display: inline-block;" value="{{ htmlspecialchars($item['name']) }}" required>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditPanel({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div>
                            <input type="text" name="model" class="form-control input-sm" style="width: 200px; margin-top: 5px;" value="{{ htmlspecialchars($item['model']) }}" placeholder="Модель">
                        </div>
                        <div>
                            <input type="text" name="note" class="form-control input-sm" style="width: 100%; margin-top: 5px;" value="{{ htmlspecialchars($item['note']) }}" placeholder="Примечание">
                        </div>
                        <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                    <option value="front"  selected>Front</option>
                                    <option value="back">Back</option>
                                </select>
                            <label>Начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>

            @elseif($item['type'] == 'socket220')
                {{-- Блок просмотра устройства --}}
                <div id="device-view-{{ $item['id'] }}-{{ $u }}" class="device-view">
                        <div class="item-title">
                               <i class="fa fa-bolt" style="color: #f39c12; margin-right: 5px;"></i><strong>{{ $item['name'] }}</strong>
                                <a href="javascript:void(0)" onclick="editDevice({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Переместить устройство"><i class="fa fa-arrows"></i></a>
                                <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                        </div>
                        <div class="text-muted small">{{ $item['model'] }}</div>
                        <div class="panel-note small text-muted">{{ $item['note'] }}</div>
                        <div style="display: flex; gap: 8px; margin-top: 5px;">
                            @for($i=1; $i<=7; $i++)
                                <div style="width: 30px; height: 30px; background: #eee; border: 1px solid #aaa; border-radius: 4px; text-align: center; line-height: 28px;">
                    <i class="fa fa-plug" style="color: #555;"></i>
                </div>
                            @endfor
                        </div>
                </div>
                {{-- Блок редактирования устройства (только перемещение) --}}
                <div id="device-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="name" value="{{ $item['name'] }}">
                        <input type="hidden" name="model" value="{{ $item['model'] }}">
                        <input type="hidden" name="note" value="{{ $item['note'] }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <input type="hidden" name="old_unit" value="{{ $u }}">
                        <div class="item-title">
                            <strong>Перемещение {{ $item['name'] }}</strong>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                    <option value="front"  selected>Front</option>
                                    <option value="back">Back</option>
                                </select>
                            <label>Новый начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>
                    
            @elseif($item['type'] == 'UPS')
               {{-- Блок просмотра устройства --}}
                <div id="device-view-{{ $item['id'] }}-{{ $u }}" class="device-view">
                        <div class="item-title">
                               <i class="fa fa-battery-full" style="font-size: 1.3em; color: #2ecc71;"></i><strong>{{ $item['name'] }}</strong>
                                <a href="javascript:void(0)" onclick="editDevice({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Переместить устройство"><i class="fa fa-arrows"></i></a>
                                <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                        </div>
                        <div class="text-muted small">{{ $item['model'] }}</div>
                        <div class="panel-note small text-muted">{{ $item['note'] }}</div>

                </div>
                {{-- Блок редактирования устройства (только перемещение) --}}
                <div id="device-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="name" value="{{ $item['name'] }}">
                        <input type="hidden" name="model" value="{{ $item['model'] }}">
                        <input type="hidden" name="note" value="{{ $item['note'] }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <input type="hidden" name="old_unit" value="{{ $u }}">
                        <div class="item-title">
                            <strong>Перемещение {{ $item['name'] }}</strong>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div style="margin-top: 5px;">                                
                            <select name="rack_side" class="form-control">
                                    <option value="front"  selected>Front</option>
                                    <option value="back">Back</option>
                                </select>
                            <label>Новый начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>
            @else
                <div class="text-muted">[Неизвестный тип панели]</div>
            @endif
        @else
            <span class="text-muted">— пусто —</span>
        @endif
                </div>
            </div>
        @endfor
    </div>

    {{-- Правая колонка (Back) --}}
    <div class="rack-column" style="flex: 1; min-width: 250px;">
        <div class="panel panel-default" style="margin-bottom: 5px;">
            <div class="panel-heading text-center"><strong>Задняя сторона (Back)</strong></div>
        </div>
        @for ($u = 1; $u <= $max_units; $u++)
            @php
                $item = $occupied_units_back[$u] ?? null;
                $hasItem = !is_null($item);
                $isEmpty = !$hasItem;
                $unitClass = $isEmpty ? 'empty-u' : ($item['type'] == 'device' ? 'occupied' : 'panel');
            @endphp
            <div class="rack-unit {{ $unitClass }}" data-unit="{{ $u }}">
                <div class="unit-num">{{ $u }}</div>
                <div class="unit-content">
                    @if($hasItem)
            @if($item['type'] == 'device')
                {{-- Блок просмотра устройства --}}
                <div id="device-view-{{ $item['id'] }}-{{ $u }}" class="device-view">
                    <div class="item-title">
                        <a href="/device/device={{ $item['id'] }}/" target="_blank"><strong>{{ $item['name'] }}</strong></a>
                        <div class="text-muted small">{{ $item['model'] }}</div>
                        <a href="javascript:void(0)" onclick="editDevice({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Переместить устройство"><i class="fa fa-arrows"></i></a>
                        <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                    </div>
                    @if(!empty($item['ports']))
                        @php
                            $row1 = array_slice($item['ports'], 0, 24);
                            $row2 = array_slice($item['ports'], 24, 24);
                            $sfp  = array_slice($item['ports'], 48);
                        @endphp
                        <div style="display: flex; align-items: flex-end; gap: 10px; margin-top: 6px;">
                            <div class="port-grid" style="background: #222; padding: 6px; border-radius: 4px;">
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    @foreach ([$row1, $row2] as $row)
                                        <div style="display: flex; gap: 2px;">
                                            @foreach ($row as $p)
                                                @php
                                                    $st = ($p['ifOperStatus'] == 'up') ? 'port-up' : 'port-down';
                                                    $port_url = "/device/device={$item['id']}/tab=port/port={$p['port_id']}/";
                                                    $full_title = !empty($p['ifAlias']) ? htmlspecialchars($p['ifAlias']) : $p['ifName'];
                                                @endphp
                                                <a href="{{ $port_url }}" target="_blank" class="port-box {{ $st }}" data-tip="{{ $full_title }}"></a>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                                @if(!empty($sfp))
                                    <div class="sfp-block" style="background: #222; padding: 6px; border-radius: 4px; display: flex; gap: 3px; align-items: flex-end;">
                                        @foreach ($sfp as $p)
                                            @php
                                                $st = ($p['ifOperStatus'] == 'up') ? 'port-up' : 'port-down';
                                                $port_url = "/device/device={$item['id']}/tab=port/port={$p['port_id']}/";
                                                $full_title = !empty($p['ifAlias']) ? htmlspecialchars($p['ifAlias']) : $p['ifName'];
                                            @endphp
                                            <a href="{{ $port_url }}" target="_blank" class="port-box {{ $st }}" data-tip="{{ $full_title }} (SFP)"></a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                {{-- Блок редактирования устройства (только перемещение) --}}
                <div id="device-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <input type="hidden" name="old_unit" value="{{ $u }}">
                        <div class="item-title">
                            <strong>Перемещение {{ $item['name'] }}</strong>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div style="margin-top: 5px;">
                                                            <select name="rack_side" class="form-control">
                                    <option value="front" >Front</option>
                                    <option value="back" selected>Back</option>
                                </select>
                            <label>Новый начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>

            @elseif($item['type'] == 'panel' || $item['type'] == 'fiber')
                {{-- Блок просмотра панели --}}
                <div id="panel-view-{{ $item['id'] }}-{{ $u }}" class="panel-view">
                    <div class="item-title">
                        <strong>{{ $item['name'] }}</strong>
                        <a href="javascript:void(0)" onclick="editPanel({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Редактировать панель"><i class="fa fa-pencil"></i></a>
                        <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                    </div>
                    <div class="text-muted small">{{ $item['model'] }}</div>
                    <div class="panel-note small text-muted">{{ $item['note'] }}</div>
                    @if(!empty($item['ports']))
                        <div class="panel-ports" style="margin-top: 6px;">
                            @php $portsChunked = array_chunk($item['ports'], 24); @endphp
                            @foreach($portsChunked as $chunk)
                                <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                    @foreach($chunk as $port)
                                        @php
                                            $tooltip = "Порт {$port['port_number']}";
                                            if (!empty($port['note'])) $tooltip .= ": {$port['note']}";
                                            if (!empty($port['fiber_color'])) $tooltip .= " (Цвет: {$port['fiber_color']})";
                                            $bgColor = !empty($port['fiber_color']) ? $port['fiber_color'] : '#555';
                                            $border = (in_array($bgColor, ['White', 'Yellow', 'Lime', 'Cyan'])) ? '1px solid #888' : '1px solid #000';
                                        @endphp
                                        <div class="port-box" style="background: {{ $bgColor }}; border: {{ $border }};" data-tip="{{ $tooltip }}"></div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">[Нет портов]</div>
                    @endif
                    <div class="text-muted small" style="margin-top: 4px;">
                        <a href="{{ url('plugin/CablingJournal?panel_id='.$item['id'].'&rack_id='.$selected_rack.'&location_id='.$selected_location) }}" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> Редактировать порты</a>
                    </div>
                </div>
                {{-- Блок редактирования панели --}}
                <div id="panel-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <div class="item-title">
                            <input type="text" name="name" class="form-control input-sm" style="width: auto; display: inline-block;" value="{{ htmlspecialchars($item['name']) }}" required>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditPanel({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div>
                            <input type="text" name="model" class="form-control input-sm" style="width: 200px; margin-top: 5px;" value="{{ htmlspecialchars($item['model']) }}" placeholder="Модель">
                        </div>
                        <div>
                            <input type="text" name="note" class="form-control input-sm" style="width: 100%; margin-top: 5px;" value="{{ htmlspecialchars($item['note']) }}" placeholder="Примечание">
                        </div>
                        <div style="margin-top: 5px;">
                                                            <select name="rack_side" class="form-control">
                                    <option value="front" >Front</option>
                                    <option value="back" selected>Back</option>
                                </select>
                            <label>Начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>

            @elseif($item['type'] == 'socket220')
                {{-- Блок просмотра устройства --}}
                <div id="device-view-{{ $item['id'] }}-{{ $u }}" class="device-view">
                        <div class="item-title">
                               <i class="fa fa-bolt" style="color: #f39c12; margin-right: 5px;"></i><strong>{{ $item['name'] }}</strong>
                                <a href="javascript:void(0)" onclick="editDevice({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Переместить устройство"><i class="fa fa-arrows"></i></a>
                                <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                        </div>
                        <div class="text-muted small">{{ $item['model'] }}</div>
                        <div class="panel-note small text-muted">{{ $item['note'] }}</div>
                        <div style="display: flex; gap: 8px; margin-top: 5px;">
                            @for($i=1; $i<=7; $i++)
                                <div style="width: 30px; height: 30px; background: #eee; border: 1px solid #aaa; border-radius: 4px; text-align: center; line-height: 28px;">
                    <i class="fa fa-plug" style="color: #555;"></i>
                </div>
                            @endfor
                        </div>
                </div>
                {{-- Блок редактирования устройства (только перемещение) --}}
                <div id="device-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="name" value="{{ $item['name'] }}">
                        <input type="hidden" name="model" value="{{ $item['model'] }}">
                        <input type="hidden" name="note" value="{{ $item['note'] }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <input type="hidden" name="old_unit" value="{{ $u }}">
                        <div class="item-title">
                            <strong>Перемещение {{ $item['name'] }}</strong>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div style="margin-top: 5px;">
                                                            <select name="rack_side" class="form-control">
                                    <option value="front" >Front</option>
                                    <option value="back" selected>Back</option>
                                </select>
                            <label>Новый начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>
                    
            @elseif($item['type'] == 'UPS')
               {{-- Блок просмотра устройства --}}
                <div id="device-view-{{ $item['id'] }}-{{ $u }}" class="device-view">
                        <div class="item-title">
                               <i class="fa fa-battery-full" style="font-size: 1.3em; color: #2ecc71;"></i><strong>{{ $item['name'] }}</strong>
                                <a href="javascript:void(0)" onclick="editDevice({{ $item['id'] }}, {{ $u }})" class="text-info pull-right" style="margin-left: 10px;" title="Переместить устройство"><i class="fa fa-arrows"></i></a>
                                <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                        </div>
                        <div class="text-muted small">{{ $item['model'] }}</div>
                        <div class="panel-note small text-muted">{{ $item['note'] }}</div>

                </div>
                {{-- Блок редактирования устройства (только перемещение) --}}
                <div id="device-edit-{{ $item['id'] }}-{{ $u }}" style="display:none;">
                    <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                        <input type="hidden" name="action" value="edit_panel">
                        <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                        <input type="hidden" name="panel_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="name" value="{{ $item['name'] }}">
                        <input type="hidden" name="model" value="{{ $item['model'] }}">
                        <input type="hidden" name="note" value="{{ $item['note'] }}">
                        <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        <input type="hidden" name="old_unit" value="{{ $u }}">
                        <div class="item-title">
                            <strong>Перемещение {{ $item['name'] }}</strong>
                            <button type="submit" class="btn btn-success btn-xs pull-right" style="margin-left: 10px;"><i class="fa fa-save"></i> Сохранить</button>
                            <a href="javascript:void(0)" onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})" class="text-muted pull-right" style="margin-left: 10px;"><i class="fa fa-times"></i> Отмена</a>
                        </div>
                        <div style="margin-top: 5px;">
                            <label>Сторона шкафа:</label>
                                <select name="rack_side" class="form-control">
                                    <option value="front" >Front</option>
                                    <option value="back" selected>Back</option>
                                </select>
                            <label>Новый начальный юнит:</label>
                            <select name="new_start_unit" class="form-control input-sm" style="width: auto; display: inline-block;">
                                <option value="{{ $u }}" selected>{{ $u }}</option>
                                @foreach($free_units as $su)
                                    <option value="{{ $su }}" >{{ $su }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted small"> (высота: {{ $item['unit_count'] }}U)</span>
                        </div>
                    </form>
                </div>
            @else
                <div class="text-muted">[Неизвестный тип панели]</div>
            @endif
        @else
            <span class="text-muted">— пусто —</span>
        @endif
                </div>
            </div>
        @endfor
    </div>
</div>
</div>
<script>
    // Передаем массивы из PHP в JS
    const freeUnits = {
        front: @json($free_units_front),
        back: @json($free_units_back)
    };

    function updateUnitList(side) {
        const unitSelect = document.getElementById('start_unit');
        const units = freeUnits[side] || [];
        
        // Очищаем текущий список
        unitSelect.innerHTML = '';
        
        // Заполняем новыми значениями
        units.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit;
            option.textContent = unit;
            unitSelect.appendChild(option);
        });
    }
</script>
<script>
    function editDevice(deviceId, unit) {
    document.getElementById('device-view-' + deviceId + '-' + unit).style.display = 'none';
    document.getElementById('device-edit-' + deviceId + '-' + unit).style.display = 'block';
}
function cancelEditDevice(deviceId, unit) {
    document.getElementById('device-view-' + deviceId + '-' + unit).style.display = 'block';
    document.getElementById('device-edit-' + deviceId + '-' + unit).style.display = 'none';
}
function editPanel(panelId, unit) {
    document.getElementById('panel-view-' + panelId + '-' + unit).style.display = 'none';
    document.getElementById('panel-edit-' + panelId + '-' + unit).style.display = 'block';
}
function cancelEditPanel(panelId, unit) {
    document.getElementById('panel-view-' + panelId + '-' + unit).style.display = 'block';
    document.getElementById('panel-edit-' + panelId + '-' + unit).style.display = 'none';
}
function toggleEmptyUnits() {
    const hide = document.getElementById('hideEmptyUnits').checked;
    document.querySelectorAll('.rack-unit.empty-u').forEach(unit => {
        if (hide) unit.classList.add('is-hidden');
        else unit.classList.remove('is-hidden');
    });
}
function confirmDeleteItem(rackId, unit, locationId) {
    if (confirm('Удалить оборудование, занимающее юнит ' + unit + '? Все связанные данные будут потеряны.')) {
        window.location.href = '{{ url("plugin/CablingJournal") }}?action=delete_item&rack_id=' + rackId + '&unit=' + unit + '&location_id=' + locationId;
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleEmptyUnits();
    var evt = new Event('change');
    document.getElementById('panel_type').dispatchEvent(evt);
});
document.getElementById('panel_type').addEventListener('change', function() {
    if (this.value === 'device') {
        document.getElementById('device_select').style.display = 'block';
        document.getElementById('passive_fields').style.display = 'none';
    } else {
        document.getElementById('device_select').style.display = 'none';
        document.getElementById('passive_fields').style.display = 'flex';
    }
});
const panelTechSelect = document.getElementById('panel_tech');
const portCountSelect = document.getElementById('port_count');
function updatePortOptions() {
    const type = panelTechSelect.value;
    let optionsHtml = '';
    switch (type) {
        case 'panel': optionsHtml = '<option value="24">24 порта</option><option value="48">48 портов</option>'; break;
        case 'fiber': optionsHtml = '<option value="8">8 портов</option><option value="16">16 портов</option><option value="24">24 порта</option><option value="32">32 порта</option>'; break;
        default: optionsHtml = '<option value="0">0 портов (нет)</option>'; break;
    }
    portCountSelect.innerHTML = optionsHtml;
    portCountSelect.disabled = (type === 'socket220' || type === 'UPS');
}
if(panelTechSelect) panelTechSelect.addEventListener('change', updatePortOptions);
document.addEventListener('DOMContentLoaded', updatePortOptions);
function toggleForm(formId) {
    var formDiv = document.getElementById(formId);
    if (formDiv.style.display === 'none' || formDiv.style.display === '') formDiv.style.display = 'block';
    else formDiv.style.display = 'none';
}
</script>


@elseif($selected_location != 0 && $selected_rack != 0 && $selected_panel != 0)

<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Порты панели: {{ $panel['name'] }}</strong>
        <a href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$selected_rack) }}" class="btn btn-default btn-xs pull-right">Вернуться к шкафу</a>
    </div>
    <div class="panel-body">
        <table class="table table-condensed" style="width: auto;">
            <thead>
                <tr><th>№ порта</th><th>Статус</th><th>Примечание (note)</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($ports as $port)
                <form method="GET" action="{{ url('plugin/CablingJournal') }}" style="margin:0;">
                    <input type="hidden" name="action" value="update_port_note">
                    <input type="hidden" name="port_id" value="{{ $port['id'] }}">
                    <input type="hidden" name="panel_id" value="{{ $panel['id'] }}">
                    <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                    <input type="hidden" name="location_id" value="{{ $selected_location }}">
                    <tr>
                        <td style="white-space: nowrap;">{{ $port['port_number'] }}</td>
                        <td>
                            @if($panel['type'] == 'fiber')
                                <select name="fiber_color" class="form-control input-sm" style="width: 100px;">
                                    <option value="">-- цвет --</option>
                                    <option value="Red" {{ ($port['fiber_color'] ?? '') == 'Red' ? 'selected' : '' }}>Красный</option>
                                    <option value="Green" {{ ($port['fiber_color'] ?? '') == 'Green' ? 'selected' : '' }}>Зелёный</option>
                                    <option value="Blue" {{ ($port['fiber_color'] ?? '') == 'Blue' ? 'selected' : '' }}>Синий</option>
                                    <option value="Yellow" {{ ($port['fiber_color'] ?? '') == 'Yellow' ? 'selected' : '' }}>Жёлтый</option>
                                    <option value="White" {{ ($port['fiber_color'] ?? '') == 'White' ? 'selected' : '' }}>Белый</option>
                                    <option value="SlateGray" {{ ($port['fiber_color'] ?? '') == 'SlateGray' ? 'selected' : '' }}>Серый</option>
                                    <option value="Brown" {{ ($port['fiber_color'] ?? '') == 'Brown' ? 'selected' : '' }}>Коричневый</option>
                                    <option value="Violet" {{ ($port['fiber_color'] ?? '') == 'Violet' ? 'selected' : '' }}>Фиолетовый</option>
                                </select>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <select name="status" class="form-control input-sm" style="width: 110px;">
                                <option value="Active" {{ ($port['status'] ?? 'Active') == 'Active' ? 'selected' : '' }}>Work</option>
                                <option value="Broken" {{ ($port['status'] ?? '') == 'Broken' ? 'selected' : '' }}>Broken</option>
                                <option value="Free" {{ ($port['status'] ?? '') == 'Free' ? 'selected' : '' }}>Free</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="note" class="form-control input-sm" style="min-width: 200px;" value="{{ htmlspecialchars($port['note'] ?? '') }}" placeholder="Описание порта">
                        </td>
                        <td>
                            <button type="submit" class="btn btn-primary btn-xs">Сохранить</button>
                        </td>
                    </tr>
                </form>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif
        </div>
    </div>
</div>

<style>
    .list-group::item { display: block; padding: 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; }
    .list-group::item:hover { background: #f9f9f9; }
        /* Общие отступы */
        body { background: #f4f4f4; padding: 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }

        /* Сетка портов */
        .port-grid { display: flex; flex-wrap: wrap; gap: 3px; margin-top: 5px; padding: 6px; background: #222; border-radius: 4px; width: fit-content; }
        .port-box { width: 12px; height: 12px; background: #555; border: 1px solid #000; cursor: pointer; display: block; border-radius: 1px; }
        .port-up { background: #2ecc71 !important; box-shadow: 0 0 4px #2ecc71; }
        .port-down { background: #e74c3c !important; }

        /* Группировка портов */
        .row-container { display: flex; flex-direction: column; gap: 3px; }
        .sfp-block { margin-left: 12px; border-left: 2px solid #444; padding-left: 8px; display: flex; align-items: flex-end; gap: 3px; height: 27px; }

        /* Юнит шкафа */
        .rack-unit {
            border-bottom: 1px solid #ddd;
            min-height: 55px; /* Увеличили, чтобы влезло 2 ряда портов */
            height: auto;
            display: flex;
            align-items: stretch;
            background: #fff;
        }
        .unit-num {
            width: 45px;
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
            border-right: 2px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            flex-shrink: 0;
        }
        .unit-content { flex-grow: 1; padding: 8px 15px; overflow: hidden; }

        /* Цвета заполнения */
        .occupied { background: #f0f9ff !important; border-left: 5px solid #3498db; }
        .panel { background: #fffef0 !important; border-left: 5px solid #f1c40f; }
        .text-muted { color: #bbb; font-size: 12px; }

    .rack-unit.empty-u.is-hidden {
        display: none !important;
    }
    /* Группировка волокон в муфте */
.fiber-module {
    display: flex;
    gap: 3px;
    padding: 5px;
    background: #444;
    border-radius: 3px;
    border: 1px solid #555;
}
.fiber-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 10px;
}
.well-ext {
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    padding: 15px;
    margin-bottom: 20px;
}

/* Кастомный тултип */
#custom-tooltip {
    position: fixed;
    display: none;
    padding: 10px 15px;
    background: rgba(0, 0, 0, 0.9);
    color: #fff;
    border-radius: 5px;
    font-size: 16px; /* Вот здесь регулируем размер текста */
    z-index: 10000;
    pointer-events: none; /* Чтобы не мешал кликам */
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    border: 1px solid #555;
    max-width: 400px;
    line-height: 1.4;
}
.tooltip-header { color: #3498db; font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #444; }
</style>


<div id="custom-tooltip"></div>
<script>
    // Общий код тултипа (работает во всех режимах)
    const tooltip = document.getElementById('custom-tooltip');
    document.addEventListener('mouseover', function(e) {
        const tipData = e.target.getAttribute('data-tip');
        if (tipData) {
            tooltip.innerHTML = tipData;
            tooltip.style.display = 'block';
        }
    });
    document.addEventListener('mousemove', function(e) {
        if (tooltip.style.display === 'block') {
            tooltip.style.left = (e.clientX + 15) + 'px';
            tooltip.style.top = (e.clientY + 15) + 'px';
        }
    });
    document.addEventListener('mouseout', function(e) {
        if (e.target.hasAttribute('data-tip')) {
            tooltip.style.display = 'none';
        }
    });
</script>
