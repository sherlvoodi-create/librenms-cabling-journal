<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="display-flex align-items-center mb-3">
                <h3 class="flex-grow-1">{{ $title }}</h3>
                @if($selected_location != 0)
                    <a href="{{ url('plugin/CablingJournal?location_id=0') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Назад к списку локаций
                    </a>
                @endif
            </div>

@if(isset($_SESSION['flash_status']))
    <div class="alert alert-success">{{ $_SESSION['flash_status'] }}</div>
    @php unset($_SESSION['flash_status']); @endphp
@endif
            {{-- СОСТОЯНИЕ 1: ЛОКАЦИЯ НЕ ВЫБРАНА --}}
            @if(!$selected_location || $selected_location == 0)
<div class="row">
        @foreach($locations as $loc)
            {{-- col-md-2 дает ровно 6 колонок (12 / 2 = 6) --}}
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
            @endif
            {{-- СОСТОЯНИЕ 2: ЛОКАЦИЯ ВЫБРАНА --}}
            @if($selected_location != 0 &&  $selected_rack == 0)
                {{-- Форма добавления (показывается только в контексте локации) --}}
                <div class="panel panel-default">
    <div class="panel-heading">Добавить шкаф в локацию: <strong>{{ $location_name }}</strong></div>
    <div class="panel-body">
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
                {{-- Таблица шкафов этой локации --}}
                <div class="panel panel-default">
                    <div class="panel-heading">Список шкафов в этой локации</div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>Имя шкафа</th>
                                    <th>Этаж</th>
                                    <th>Юниты</th>
                                    <th>Тип</th>
                                    <th>Заметки</th>
                                </tr>
                            </thead>
<tbody>
    @php $racksList = is_array($racks) ? $racks : []; @endphp
    @forelse($racksList as $rack)
        @if($rack['location_id'] == $selected_location)
        <tr>
            <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">
                <strong>{{ $rack['name'] }}</strong>
            </td>
            <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">
                {{ $rack['floor'] ?? '-' }}
            </td>
            <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">
                {{ $rack['units'] }}U
            </td>
            <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">
                {{ $rack['type'] }}
            </td>
            <td class="clickable-row" data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}" style="cursor: pointer;">
                <small>{{ $rack['note'] }}</small>
            </td>
            <td style="text-align: center;">
                <a href="javascript:void(0)" onclick="confirmDeleteRack({{ $rack['id'] }}, {{ $selected_location }})" class="text-danger" title="Удалить шкаф">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
        @endif
    @empty
        <tr><td colspan="6" class="text-center">В этой локации пока нет созданных шкафов.</td></tr>
    @endforelse
</tbody>
                        </table>
                    </div>
                </div>
            @endif
            {{-- СОСТОЯНИЕ 3: ЛОКАЦИЯ ВЫБРАНА И ВЫБРАН ШКАФ--}}
@if($selected_location != 0 && $selected_rack != 0)

    {{-- Хлебные крошки и заголовок --}}
    <div class="row">
        <div class="col-md-12">
            <a href="{{ url('plugin/CablingJournal?location_id='.$selected_location) }}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Назад к списку шкафов
            </a>
            <h3>Шкаф: {{ $rack['name'] ?? 'Без имени' }} ({{ $max_units }}U)</h3>
            <p>Локация: {{ $location_name }}</p>
        </div>
    </div>
{{-- Форма добавления оборудования в шкаф --}}
<div class="panel panel-default">
    <div class="panel-heading">Добавить оборудование в шкаф: <strong>{{ $rack['name'] }}</strong></div>
    <div class="panel-body">
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
    <label>Начальный юнит (U):</label>
    <select name="start_unit" id="start_unit" class="form-control" required>
        @foreach($free_units as $unit)
            <option value="{{ $unit }}">{{ $unit }}</option>
        @endforeach
    </select>
</div>                <div class="col-md-3">
                    <label>Количество юнитов (высота):</label>
                    <input type="number" name="unit_count" class="form-control" value="1">
                </div>
                <div class="col-md-3">
                    <label>Примечание:</label>
                    <input type="text" name="note" class="form-control">
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
        <select name="panel_tech" id="panel_type" class="form-control">
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

    {{-- Чекбокс скрытия пустых юнитов --}}
    <div style="margin-bottom: 15px; background: #fff; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
        <label>
            <input type="checkbox" id="hideEmptyUnits" checked onchange="toggleEmptyUnits()">
            Скрывать пустые юниты
        </label>
    </div>

    {{-- Контейнер шкафа --}}
    <div class="rack-container" style="background:#fff; border:1px solid #ccc; max-width: 100%; overflow-x: auto;">
        @for ($u = 1; $u <= $max_units; $u++)
            @php
                $item = $occupied_units[$u] ?? null;
                $hasItem = !is_null($item);
                $isEmpty = !$hasItem;
                $unitClass = $isEmpty ? 'empty-u' : ($item['type'] == 'device' ? 'occupied' : 'panel');
            @endphp

            <div class="rack-unit {{ $unitClass }}" data-unit="{{ $u }}">
                {{-- Номер юнита --}}
                <div class="unit-num">{{ $u }}</div>

                {{-- Содержимое юнита --}}
                <div class="unit-content">
                    @if($hasItem)
                        {{-- Заголовок устройства/панели --}}
                        <div class="item-title">
                            @if($item['type'] == 'device')
                                <a href="/device/device={{ $item['id'] }}/" target="_blank">
                                    <strong>{{ $item['name'] }}</strong>
                                </a>
                            @else
                                <strong>{{ $item['name'] }}</strong>
                            @endif
                            <a href="javascript:void(0)" onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})" class="text-danger pull-right" style="margin-left: 10px;" title="Удалить из шкафа">
        <i class="fa fa-trash-o"></i>
    </a>
                        </div>

                        {{-- Отрисовка портов для устройства --}}
                        @if($item['type'] == 'device' && !empty($item['ports']))
                            @php
                                $row1 = array_slice($item['ports'], 0, 24);
                                $row2 = array_slice($item['ports'], 24, 24);
                                $sfp  = array_slice($item['ports'], 48);
                            @endphp

                            <div style="display: flex; align-items: flex-end; gap: 10px; margin-top: 6px;">
                                {{-- Левая часть: два ряда портов (чёрный фон) --}}
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


                                {{-- Правая часть: SFP-порты (чёрный фон) --}}
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
                            </div></div>
                        @elseif($item['type'] == 'panel')
                            <div class="text-muted" style="margin-top: 5px;">[Пассивная панель, порты не отображаются]</div>
                        @endif
                    @else
                        {{-- Пустой юнит:  --}}
                        <span class="text-muted">— пусто —</span>
                    @endif
                </div>
            </div>
        @endfor
    </div>

    {{-- JavaScript для скрытия пустых юнитов и тултипа --}}
    <script>
        function toggleEmptyUnits() {
            const hide = document.getElementById('hideEmptyUnits').checked;
            document.querySelectorAll('.rack-unit.empty-u').forEach(unit => {
                if (hide) unit.classList.add('is-hidden');
                else unit.classList.remove('is-hidden');
            });
        }

        // Тултип (оставляем ваш старый код)
        const tooltip = document.getElementById('custom-tooltip') || (() => {
            const div = document.createElement('div');
            div.id = 'custom-tooltip';
            document.body.appendChild(div);
            return div;
        })();

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

        // При загрузке страницы запускаем скрытие
        document.addEventListener('DOMContentLoaded', toggleEmptyUnits);
    </script>
@endif
        </div>
    </div>
</div>

<style>
    .list-group::item { display: block; padding: 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; }
    .list-group::item:hover { background: #f9f9f9; }
<style>
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
    </style><style>
    /* Стиль для скрытия */
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
<script>
$(document).ready(function() {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
    const tooltip = document.getElementById('custom-tooltip');

document.addEventListener('mouseover', function(e) {
    const tipData = e.target.getAttribute('data-tip');
    if (tipData) {
        // Разбиваем текст (если там есть "|", сделаем красивый заголовок)
        const parts = tipData.split('|');
        let html = '';
        if (parts.length > 1) {
            html = '<div class="tooltip-header">' + parts[0] + '</div>' + parts[1];
        } else {
            html = tipData;
        }

        tooltip.innerHTML = html;
        tooltip.style.display = 'block';
    }
});

document.addEventListener('mousemove', function(e) {
    if (tooltip.style.display === 'block') {
        // Смещаем тултип чуть правее и ниже курсора
        tooltip.style.left = (e.clientX + 15) + 'px';
        tooltip.style.top = (e.clientY + 15) + 'px';
    }
});

document.addEventListener('mouseout', function(e) {
    if (e.target.hasAttribute('data-tip')) {
        tooltip.style.display = 'none';
    }
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


});

function confirmDeleteRack(rackId, locationId) {
    if (confirm('Вы уверены, что хотите удалить этот шкаф? Все панели и устройства внутри него также будут удалены.')) {
        window.location.href = '{{ url("plugin/CablingJournal") }}?action=delete_rack&rack_id=' + rackId + '&location_id=' + locationId;
    }
}
function confirmDeleteItem(rackId, unit, locationId) {
    if (confirm('Удалить оборудование, занимающее юнит ' + unit + '? Все связанные данные будут потеряны.')) {
        window.location.href = '{{ url("plugin/CablingJournal") }}?action=delete_item&rack_id=' + rackId + '&unit=' + unit + '&location_id=' + locationId;
    }
}
// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    var evt = new Event('change');
    document.getElementById('panel_type').dispatchEvent(evt);
});

const panelTypeSelect = document.getElementById('panel_type');
const portCountSelect = document.getElementById('port_count');

function updatePortOptions() {
    const type = panelTypeSelect.value;
    let optionsHtml = '';

    switch (type) {
        case 'panel':   // медная патч-панель
            optionsHtml = '<option value="24">24 порта</option><option value="48">48 портов</option>';
            break;
        case 'fiber':   // оптический кросс
            optionsHtml = '<option value="8">8 портов</option><option value="16">16 портов</option><option value="24">24 порта</option><option value="32">32 порта</option>';
            break;
        default:        // socket220, UPS и любые другие
            optionsHtml = '<option value="0">0 портов (нет)</option>';
            break;
    }

    portCountSelect.innerHTML = optionsHtml;

    // Если выбран тип без портов, можно дополнительно заблокировать поле (опционально)
    if (type === 'socket220' || type === 'UPS') {
        portCountSelect.disabled = true;
    } else {
        portCountSelect.disabled = false;
    }
}

// Вешаем обработчик изменения
panelTypeSelect.addEventListener('change', updatePortOptions);

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', updatePortOptions);

</script>
