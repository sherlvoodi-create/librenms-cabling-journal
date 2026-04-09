<div class="container-fluid">
    <div class="row">

        @if ($mode == 'cbl')
            {{-- Форма добавления кабеля (Расширенная) --}}
            <div class="panel panel-default">
                <div class="panel-heading" style="padding: 8px 15px;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="toggleForm('addCBL')">Добавить
                        Кабель</button>
                </div>
                <div id="addCBL" style="display:none;" class="panel-body">
                    <form action="" method="GET">
                        <input type="hidden" name="action" value="add_cbl">

                        <div class="row">
                            {{-- Основная информация --}}
                            <div class="col-md-3">
                                <label>Маркировка (cbl_label)*</label>
                                <input type="text" name="cbl_label" class="form-control" required
                                    placeholder="напр. FIB-to-Terminal">
                            </div>
                            <div class="col-md-3">
                                <label>Имя (name)*</label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="напр. FIB-to-Terminal">
                            </div>
                            <div class="col-md-2">
                                <label>Тип (cbl_type)</label>
                                <select name="cbl_type" class="form-control">
                                    <option value="fiber">Fiber (Оптика)</option>
                                    <option value="utp">UTP (Медь)</option>
                                    <option value="power">Power</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Кол-во жил/волокон</label>
                                <input type="number" name="cbl_count_cords" class="form-control" value="1">
                            </div>
                            <div class="col-md-2">
                                <label>Статус</label>
                                <select name="status" class="form-control">
                                    <option value="Active">Active</option>
                                    <option value="Reserved">Reserved</option>
                                    <option value="Broken">Broken</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                            {{-- Модель и линк --}}
                            <div class="col-md-4">
                                <label>Марка (модель из справочника)</label>
                                <select name="cbl_model" id="cbl_model_select" class="form-control">
                                    <option value="">-- Выберите из списка --</option>
                                    @foreach ($presets as $p)
                                        @if (($p['type'] ?? '') === 'link')
                                            <option value="{{ $p['name'] }}">{{ $p['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Тип линка (link_type)</label>
                                <select name="link_type" class="form-control">
                                    <option value="external">External (Внешний)</option>
                                    <option value="internal">Internal (Внутренний)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Дистанция (м)</label>
                                <input type="text" name="distance" class="form-control" placeholder="700">
                            </div>
                            <div class="col-md-3">
                                <label>Примечание</label>
                                <input type="text" name="note" class="form-control" placeholder="доп. информация">
                            </div>
                        </div>

                        <hr style="margin: 15px 0;">
                        {{-- Сторона А --}}
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="color: #337ab7; border-bottom: 1px solid #eee; padding-bottom: 5px;">Сторона
                                    А (Источник)</h4>
                            </div>

                            {{-- Скрытые поля для итоговых ID и Типа, которые пойдут в базу --}}
                            <input type="hidden" name="side_a_id" id="side_a_id_final" value="0">
                            <input type="hidden" name="side_a_type" id="side_a_type_final" value="Panel_Port">

                            {{-- Выбор Локации --}}
                            <div class="col-md-3">
                                <label>Локация</label>
                                <select id="select_loc_a" class="form-control selectpicker" data-live-search="true">
                                    <option value="">-- Выберите локацию --</option>
                                    @foreach ($locations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Выбор Шкафа --}}
                            <div class="col-md-3">
                                <label>Шкаф (Rack)</label>
                                <select id="select_rack_a" class="form-control" disabled>
                                    <option value="">-- Сначала выберите локацию --</option>
                                    @foreach ($custom_racks as $rack)
                                        <option value="{{ $rack['id'] }}" data-loc="{{ $rack['location_id'] }}"
                                            style="display:none;">
                                            {{ $rack['name'] }} ({{ $rack['model'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Выбор Панели --}}
                            <div class="col-md-3">
                                <label>Панель / Device</label>
                                <select id="select_panel_a" class="form-control" disabled>
                                    <option value="">-- Сначала выберите шкаф --</option>
                                    @foreach ($custom_panels as $panel)
                                        <option value="{{ $panel['id'] }}" data-rack="{{ $panel['rack_id'] }}"
                                            style="display:none;">
                                            {{ $panel['name'] }} ({{ $panel['port_count'] }} портов)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Выбор Порта --}}
                            <div class="col-md-3">
                                <label>Порт (side_a_id)*</label>
                                <select id="select_port_a" class="form-control" required disabled>
                                    <option value="">-- Выберите панель --</option>
                                    @foreach ($custom_panel_ports as $port)
                                        <option value="{{ $port['id'] }}" data-panel="{{ $port['panel_id'] }}"
                                            style="display:none;">
                                            Порт №{{ $port['port_number'] }} [{{ $port['status'] }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Сторона B --}}
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="color: #337ab7; border-bottom: 1px solid #eee; padding-bottom: 5px;">Сторона
                                    B (Назначение)</h4>
                            </div>

                            {{-- Скрытые поля для итоговых ID и Типа, которые пойдут в базу --}}
                            <input type="hidden" name="side_b_id" id="side_b_id_final" value="0">
                            <input type="hidden" name="side_b_type" id="side_b_type_final" value="Panel_Port">

                            {{-- Выбор Локации --}}
                            <div class="col-md-3">
                                <label>Локация</label>
                                <select id="select_loc_b" class="form-control selectpicker" data-live-search="true">
                                    <option value="">-- Выберите локацию --</option>
                                    @foreach ($locations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Выбор Шкафа --}}
                            <div class="col-md-3">
                                <label>Шкаф (Rack)</label>
                                <select id="select_rack_b" class="form-control" disabled>
                                    <option value="">-- Сначала выберите локацию --</option>
                                    @foreach ($custom_racks as $rack)
                                        <option value="{{ $rack['id'] }}" data-loc="{{ $rack['location_id'] }}"
                                            style="display:none;">
                                            {{ $rack['name'] }} ({{ $rack['model'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Выбор Панели --}}
                            <div class="col-md-3">
                                <label>Панель / Device</label>
                                <select id="select_panel_b" class="form-control" disabled>
                                    <option value="">-- Сначала выберите шкаф --</option>
                                    @foreach ($custom_panels as $panel)
                                        <option value="{{ $panel['id'] }}" data-rack="{{ $panel['rack_id'] }}"
                                            style="display:none;">
                                            {{ $panel['name'] }} ({{ $panel['port_count'] }} портов)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Выбор Порта --}}
                            <div class="col-md-3">
                                <label>Порт (side_b_id)*</label>
                                <select id="select_port_b" class="form-control" required disabled>
                                    <option value="">-- Выберите панель --</option>
                                    @foreach ($custom_panel_ports as $port)
                                        <option value="{{ $port['id'] }}" data-panel="{{ $port['panel_id'] }}"
                                            style="display:none;">
                                            Порт №{{ $port['port_number'] }} [{{ $port['status'] }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-6">
                                <label>Гео-координаты A</label>
                                <input type="text" name="side_a_geo" class="form-control" placeholder="lat,lon">
                            </div>
                            <div class="col-md-6">
                                <label>Гео-координаты B</label>
                                <input type="text" name="side_b_geo" class="form-control" placeholder="lat,lon">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Создать Кабель
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Секция Магистральные (external) --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-globe"></i> Магистральные кабели (External)</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-condensed table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Маркировка</th>
                                <th>Тип/Модель</th>
                                <th>Волокон/Жил</th>
                                <th>А-сторона</th>
                                <th>Б-сторона</th>
                                <th>Статус</th>
                                <th>Примечание</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($custom_links as $link)
                                @if ($link['link_type'] == 'external')
                                    <tr>
                                        <td><strong>{{ $link['cbl_label'] }}</strong></td>
                                        <td>{{ $link['cbl_type'] ?? $link['type'] }}
                                            ({{ $link['cbl_model'] ?? $link['model'] }})
                                        </td>
                                        <td>{{ $link['cbl_count_cords'] ?? $link['count_cords'] }}</td>
                                        <td><span class="label label-info">{{ $link['side_a_type'] }}</span>
                                            ID: {{ $link['side_a_id'] }}</td>
                                        <td><span class="label label-default">{{ $link['side_b_type'] }}</span>
                                            ID: {{ $link['side_b_id'] }}</td>
                                        <td><span
                                                class="label label-{{ $link['status'] == 'Active' ? 'success' : 'warning' }}">{{ $link['status'] }}</span>
                                        </td>
                                        <td><small>{{ $link['note'] }}</small></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Секция Внутренние (internal) --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-building"></i> Внутридомовая разводка (Internal)
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-condensed table-bordered table-striped">
                        <thead>
                            <tr>

                                <th>Маркировка кабеля</th>
                                <th>Локация / Стойка / Панель / Порт</th>
                                <th>Длина</th>
                                <th>Тип / Марка / Жилы</th>
                                <th>Примечание к кабелю</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($custom_links as $link)
                                @if ($link['link_type'] == 'internal')
                                    @php
                                        // Инициализируем переменные, чтобы избежать ошибок, если данных нет
                                        $port = null;
                                        $panel = null;
                                        $rack = null;
                                        $location = null;
                                        $portId = $link['side_a_id'] ?? 0;

                                        if ($portId && isset($custom_panel_ports[$portId])) {
                                            $port = $custom_panel_ports[$portId];

                                            if (isset($custom_panels[$port['panel_id']])) {
                                                $panel = $custom_panels[$port['panel_id']];

                                                if (isset($custom_racks[$panel['rack_id']])) {
                                                    $rack = $custom_racks[$panel['rack_id']];

                                                    if (isset($locations[$rack['location_id']])) {
                                                        $location = $locations[$rack['location_id']];
                                                    } else {
                                                        $location = 'unknown';
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr>

                                        <td><strong>{{ $link['cbl_label'] }}</strong></td>
                                        <td>
                                            @if ($rack)
                                                {{-- Ссылка на Стойку в конкретной локации --}}
                                                <a
                                                    href="{{ url('plugin/CablingJournal?location_id=' . $rack['location_id']) }}">
                                                    {{ $location }}
                                                </a>/

                                                <a href="{{ url('plugin/CablingJournal?location_id=' . $rack['location_id'] . '&rack_id=' . ($panel['rack_id'] ?? 0)) }}"
                                                    data-tip="{{ $rack['note'] }}">
                                                    {{ $rack['name'] }}
                                                </a>/

                                                @if ($panel['type'] !== 'device')
                                                    {{-- Ссылка на конкретную Панель в этой стойке --}}

                                                    <a href="{{ url('plugin/CablingJournal?location_id=' . $rack['location_id'] . '&rack_id=' . ($panel['rack_id'] ?? 0) . '&panel_id=' . $port['panel_id']) }}"
                                                        data-tip="Тип:{{ $panel['type'] }} Портов: {{ $panel['port_count'] }}">
                                                        Панель [{{ $panel['name'] }}]
                                                    </a>/

                                                    {{-- Ссылка на конкретный Порт  --}}
                                                    <a href="{{ url('plugin/CablingJournal?location_id=' . $rack['location_id'] . '&rack_id=' . ($panel['rack_id'] ?? 0) . '&panel_id=' . $port['panel_id'] . '&port_id=' . $portId) }}"
                                                        class="label label-default" style="margin-left: 5px;"
                                                        data-tip="{{ $port['note'] }}">
                                                        Порт: {{ $port['port_number'] ?? '?' }}
                                                    </a>
                                                @else
                                                @endif
                                            @else
                                                <span class="label label-danger">Порт #{{ $portId }}
                                                    (данные не найдены)
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $link['distance'] }}</td>
                                        <td>
                                            {{ $link['cbl_type'] }} / {{ $link['cbl_model'] }}<br>
                                            <small class="text-muted">{{ $link['cbl_count_cords'] }}
                                                жил</small>
                                        </td>

                                        <td><small>{{ $link['note'] }}</small></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


            <div id="custom-tooltip"></div>
            <style>
                /* Кастомный тултип */
                #custom-tooltip {
                    position: fixed;
                    display: none;
                    padding: 10px 15px;
                    background: rgba(0, 0, 0, 0.9);
                    color: #fff;
                    border-radius: 5px;
                    font-size: 16px;
                    /* Вот здесь регулируем размер текста */
                    z-index: 10000;
                    pointer-events: none;
                    /* Чтобы не мешал кликам */
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                    border: 1px solid #555;
                    max-width: 400px;
                    line-height: 1.4;
                }

                .tooltip-header {
                    color: #3498db;
                    font-weight: bold;
                    margin-bottom: 5px;
                    border-bottom: 1px solid #444;
                }
            </style>
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
            <script>
                $(document).ready(function() {
                    $(".clickable-row").click(function() {
                        window.location = $(this).data("href");
                    });
                });

                function confirmDeleteCBL(cblId) {
                    if (confirm(
                            'Вы уверены, что хотите удалить кабель?')) {
                        window.location.href = '{{ url('plugin/CablingJournal') }}?action=delete_cbl&cbl_id=' + cblId;
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
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Функция для фильтрации связанных списков
                    function setupCascade(parentSelector, childSelector, dataAttr) {
                        const parent = document.getElementById(parentSelector);
                        const child = document.getElementById(childSelector);
                        const childOptions = Array.from(child.options);

                        parent.addEventListener('change', function() {
                            const selectedVal = this.value;

                            // Сбрасываем дочерний список
                            child.value = "";
                            child.disabled = !selectedVal;

                            // Показываем только подходящие опции
                            childOptions.forEach(opt => {
                                if (opt.value === "" || opt.getAttribute(dataAttr) == selectedVal) {
                                    opt.style.display = "block";
                                } else {
                                    opt.style.display = "none";
                                }
                            });

                            // Триггерим событие изменения для следующего уровня в цепочке
                            child.dispatchEvent(new Event('change'));
                        });
                    }

                    // Настраиваем цепочку: Локация -> Шкаф -> Панель -> Порт
                    setupCascade('select_loc_a', 'select_rack_a', 'data-loc');
                    setupCascade('select_rack_a', 'select_panel_a', 'data-rack');
                    setupCascade('select_panel_a', 'select_port_a', 'data-panel');

                    // При выборе конечного порта — записываем его ID в скрытое поле side_a_id
                    document.getElementById('select_port_a').addEventListener('change', function() {
                        document.getElementById('side_a_id_final').value = this.value;
                    });

                    // Настраиваем цепочку: Локация -> Шкаф -> Панель -> Порт
                    setupCascade('select_loc_b', 'select_rack_b', 'data-loc');
                    setupCascade('select_rack_b', 'select_panel_b', 'data-rack');
                    setupCascade('select_panel_b', 'select_port_b', 'data-panel');

                    // При выборе конечного порта — записываем его ID в скрытое поле side_a_id
                    document.getElementById('select_port_b').addEventListener('change', function() {
                        document.getElementById('side_b_id_final').value = this.value;
                    });
                });
            </script>
            @php return; @endphp
        @endif

        {{-- РЕЖИМ РАБОТЫ СО ШКАФАМИ --}}
        <div class="col-md-12">
            {{-- Хлебные крошки --}}
            <ol class="breadcrumb" style="background: none; padding-left: 0; margin-bottom: 20px;">
                <li><a href="{{ url('plugin/CablingJournal?location_id=0') }}"><i class="fa fa-home"></i> Локации</a>
                </li>
                @if ($selected_location != 0)
                    <li>
                        @if ($selected_rack == 0)
                            <strong>{{ $location_name }}</strong>
                        @else
                            <a
                                href="{{ url('plugin/CablingJournal?location_id=' . $selected_location) }}">{{ $location_name }}</a>
                        @endif
                    </li>
                @endif
                @if ($selected_rack != 0 && $selected_panel != 0)
                    <li>
                        <a
                            href="{{ url('plugin/CablingJournal?location_id=' . $selected_location . '&rack_id=' . $selected_rack) }}">{{ $rack['name'] ?? 'Шкаф' }}
                            ({{ $max_units }}U)</a>
                    </li>
                @elseif($selected_rack != 0 && $selected_panel == 0)
                    <li class="active">{{ $rack['name'] ?? 'Шкаф' }} ({{ $max_units }}U)</li>
                @endif
                @if ($selected_panel != 0)
                    <li class="active">{{ $panel['name'] ?? 'Панель' }}</li>
                @endif
            </ol>

            @if (session('success'))
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
            @if (session('error'))
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




            @if (!$selected_location || $selected_location == 0)
                <div class="row">
                    @foreach ($locations as $loc)
                        <div class="col-sm-4 col-md-2">
                            <a href="{{ url('plugin/CablingJournal?location_id=' . $loc->id) }}"
                                style="text-decoration: none; color: inherit;">
                                <div class="panel panel-default panel-hover-effect"
                                    style="transition: all 0.2s; border-top: 3px solid #3498db; margin-bottom: 15px; border-radius: 4px;">
                                    <div class="panel-body text-center" style="padding: 15px 10px;">
                                        <div style="font-size: 18px; color: #e74c3c; margin-bottom: 5px;">
                                            <i class="fa fa-building-o"></i>
                                        </div>
                                        <h5 style="margin: 0; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                            title="{{ $loc->location }}">
                                            {{ $loc->location }}
                                        </h5>
                                        <div
                                            style="margin-top: 5px; font-size: 10px; color: #999; text-transform: uppercase;">
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
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                        border-top-color: #e74c3c !important;
                        background-color: #fcfcfc;
                    }
                </style>
            @elseif($selected_location != 0 && $selected_rack == 0)
                {{-- Форма добавления шкафа --}}
                <div class="panel panel-default">
                    <div class="panel-heading" style="padding: 8px 15px;">
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="toggleForm('addRackForm')">Добавить шкаф</button>
                    </div>
                    <div id="addRackForm" style="display:none;" class="panel-body">
                        <form action="" method="GET">
                            <input type="hidden" name="action" value="add_rack">
                            <input type="hidden" name="location_id" value="{{ $selected_location }}">

                            {{-- Скрытые поля для передачи параметров в PHP при сохранении --}}
                            <input type="hidden" name="units" id="rack_units_hidden" value="42">
                            <input type="hidden" name="type" id="rack_type_hidden" value="Rack">

                            <div class="row">
                                <div class="col-md-3">
                                    <label>Название (ID)*</label>
                                    <input type="text" name="name" class="form-control" required
                                        placeholder="напр. Шкаф-01">
                                </div>

                                <div class="col-md-2">
                                    <label>Этаж</label>
                                    <input type="text" name="floor" class="form-control" placeholder="напр. 2">
                                </div>
                                <div class="col-md-3">
                                    <label>Ниша / Комната</label>
                                    <input type="text" name="room" class="form-control"
                                        placeholder="напр. Серверная, коб.201 или Ниша А">
                                </div>

                                <div class="col-md-4">
                                    <label>Координаты (Lat, Lon)</label>
                                    <input type="text" name="coordinates" class="form-control"
                                        placeholder="напр. 55.75, 37.61">
                                </div>
                                <div class="col-md-4">
                                    <label>Модель оборудования (из справочника)*</label>
                                    <select name="model" id="rack_model_select" class="form-control" required
                                        onchange="applyRackPreset(this.value)">
                                        <option value="">-- Выберите модель из списка --</option>
                                        @foreach ($presets as $p)
                                            @if (($p['type'] ?? '') === 'rack')
                                                <option value="{{ $p['name'] }}">
                                                    {{ $p['name'] }} ({{ $p['unit'] ?? ($p['u'] ?? '?') }}U)
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div id="rack_info_hint" style="font-size: 11px; color: #666; margin-top: 4px;">
                                        {{-- Сюда JS подставит краткое описание выбранной модели --}}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label>Примечание</label>
                                    <input type="text" name="note" class="form-control"
                                        placeholder="доп. информация">
                                </div>
                            </div>

                            <div class="row" style="margin-top: 15px;">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Создать шкаф в этой локации
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Сетка шкафов --}}
                <div class="row">
                    @php $racksList = is_array($racks) ? $racks : []; @endphp
                    @forelse($racksList as $rack)
                        @if ($rack['location_id'] == $selected_location)
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="panel panel-default panel-hover-effect"
                                    style="transition: all 0.2s; border-top: 3px solid #3498db; margin-bottom: 15px; border-radius: 4px; cursor: pointer;"
                                    onclick="window.location.href='{{ url('plugin/CablingJournal?location_id=' . $selected_location . '&rack_id=' . $rack['id']) }}'">
                                    <div class="panel-body" style="padding: 12px;">
                                        {{-- Верхняя строка: название и кнопка удаления --}}
                                        <div class="clearfix" style="margin-bottom: 5px;">
                                            <strong style="font-size: 1.1em;">{{ $rack['name'] }}</strong>
                                            <a href="javascript:void(0)"
                                                onclick="event.stopPropagation(); confirmDeleteRack({{ $rack['id'] }}, {{ $selected_location }})"
                                                class="text-danger pull-right" title="Удалить шкаф">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                        {{-- Модель шкафа --}}
                                        @if (!empty($rack['model']))
                                            <div class="text-muted small" style="margin-bottom: 8px;">
                                                <i class="fa fa-cube"></i> {{ $rack['model'] ?? '—' }}
                                            </div>
                                        @endif
                                        {{-- Гео-координаты (если есть) --}}
                                        @if (!empty($rack['coordinates']))
                                            <div style="margin-bottom: 6px;">
                                                <a href="http://maps.yandex.ru/?text={{ urlencode($rack['coordinates']) }}"
                                                    target="_blank" onclick="event.stopPropagation();"
                                                    class="text-info" style="text-decoration: none;">
                                                    <i class="fa fa-map-marker"></i>
                                                    {{ $rack['coordinates'] ?? 'неизвестно' }}
                                                </a>
                                            </div>
                                        @endif
                                        {{-- Этаж и комната --}}
                                        <div class="small" style="margin-bottom: 4px;">

                                            <span><i class="fa fa-level-up"></i>
                                                {{ isset($rack['floor']) ? 'Этаж:' . $rack['floor'] : '—' }}</span>
                                            <span style="margin-left: 12px;"><i
                                                    class="fa fa-building"></i>{{ isset($rack['room']) ? 'Комната:' . $rack['room'] : '—' }}</span>

                                        </div>
                                        {{-- Высота (юниты) --}}
                                        <div class="small" style="margin-bottom: 4px;">
                                            <i class="fa fa-arrows-v"></i>
                                            {{ isset($rack['units']) ? $rack['units'] . 'U' : '—' }}
                                        </div>
                                        {{-- Тип шкафа --}}
                                        <div class="small" style="margin-bottom: 4px;">
                                            <i class="fa fa-tag"></i> {{ $rack['type'] ?? '—' }}
                                        </div>
                                        {{-- Примечание --}}
                                        @if (!empty($rack['note']))
                                            <div class="small text-muted"
                                                style="margin-top: 6px; border-top: 1px solid #eee; padding-top: 4px;">
                                                <i class="fa fa-comment-o"></i> {{ $rack['note'] ?? '—' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-md-12">
                            <div class="alert alert-info text-center">В этой локации пока нет созданных шкафов.</div>
                        </div>
                    @endforelse
                </div>

                {{-- Дополнительный стиль для эффекта при наведении (можно оставить из предыдущего) --}}
                <style>
                    .panel-hover-effect:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                        border-top-color: #e74c3c !important;
                        background-color: #fcfcfc;
                    }
                </style>
                <script>
                    const rackPresets = @json($presets);

                    function applyRackPreset(selectedName) {
                        const unitsHidden = document.getElementById('rack_units_hidden');
                        const typeHidden = document.getElementById('rack_type_hidden');
                        const hint = document.getElementById('rack_info_hint');

                        if (!selectedName) {
                            hint.innerHTML = '';
                            return;
                        }

                        // Ищем модель в справочнике
                        const found = rackPresets.find(p => p.name === selectedName);

                        if (found) {
                            // Подставляем высоту (U) и тип в скрытые инпуты
                            const height = found.unit || found.u || 42;
                            const type = found.type || 'Rack';

                            unitsHidden.value = height;
                            typeHidden.value = type;

                            // Выводим подсказку пользователю, чтобы он видел, что подставилось
                            hint.innerHTML = `<i class="fa fa-info-circle"></i> Автозаполнение: <b>${height}U</b>, Тип: <b>${type}</b>`;
                        }
                    }
                </script>
                <script>
                    $(document).ready(function() {
                        $(".clickable-row").click(function() {
                            window.location = $(this).data("href");
                        });
                    });

                    function confirmDeleteRack(rackId, locationId) {
                        if (confirm(
                                'Вы уверены, что хотите удалить этот шкаф? Все панели и устройства внутри него также будут удалены.')) {
                            window.location.href = '{{ url('plugin/CablingJournal') }}?action=delete_rack&rack_id=' + rackId +
                                '&location_id=' + locationId;
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
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="toggleForm('addPanelForm')">Добавить оборудование</button>
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
                                        @foreach ($free_units_front as $unit)
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
                                    <select name="rack_side" id="rack_side_select" class="form-control"
                                        onchange="updateUnitList(this.value)">
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
                                        @foreach ($devices as $dev)
                                            <option value="{{ $dev->device_id }}">{{ $dev->hostname }}
                                                ({{ $dev->sysName }})
                                            </option>
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
                                        <option value="ups">ИБП (UPS)</option>
                                        <option value="other">Другое</option>
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
                                    <label>Модель из справочника:</label>
                                    {{-- Используем select для выбора из пресетов --}}
                                    <select name="model_preset" id="panel_model_preset" class="form-control"
                                        onchange="applyPanelPreset(this.value)">
                                        <option value="">-- Выберите модель --</option>
                                        @foreach ($presets as $p)
                                            {{-- Исключаем шкафы из списка оборудования внутри шкафа --}}
                                            @if (($p['type'] ?? '') !== 'rack')
                                                <option value="{{ $p['name'] }}">{{ $p['name'] }}</option>
                                            @endif
                                        @endforeach
                                        <option value="custom">-- Ввести вручную --</option>
                                    </select>
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
                {{-- Контейнер шкафа (новый стиль) --}}
                <div class="rack-wrapper" style="display: flex; flex-wrap: wrap; gap: 30px; margin-top: 20px;">
                    {{-- Левая колонка (Front) --}}
                    <div class="rack-column" style="flex: 1; min-width: 320px;">
                        <div class="panel panel-default" style="margin-bottom: 5px;">
                            <div class="panel-heading text-center"><strong>Передняя сторона (Front)</strong></div>
                        </div>
                        @for ($u = $max_units; $u >= 1; $u--)
                            @php
                                $item = $occupied_units_front[$u] ?? null;
                                $hasItem = !is_null($item);
                                $isEmpty = !$hasItem;
                                $unitClass = $isEmpty ? 'empty-u' : ($item['type'] == 'device' ? 'occupied' : 'panel');
                            @endphp
                            <div class="rack-unit {{ $unitClass }}" data-unit="{{ $u }}">
                                <div class="unit-num">{{ $u }}</div>
                                <div class="unit-content">
                                    @if ($hasItem)
                                        @if ($item['type'] == 'device')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    <a href="/device/device={{ $item['device_id'] }}/"
                                                        target="_blank"><strong>{{ $item['name'] }}</strong></a>
                                                    <div class="text-muted small">{{ $item['model'] }}</div>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                @if (!empty($item['ports']))
                                                    @php
                                                        $portsCount = count($item['ports']);
                                                        if ($portsCount > 48) {
                                                            $row1 = array_slice($item['ports'], 0, 24);
                                                            $row2 = array_slice($item['ports'], 24, 24);
                                                            $sfp = array_slice($item['ports'], 48);
                                                        } else {
                                                            $row1 = array_slice($item['ports'], 0, 12);
                                                            $row2 = array_slice($item['ports'], 12, 12);
                                                            $sfp = array_slice($item['ports'], 24);
                                                        }
                                                    @endphp
                                                    <div custom-cls="{{ $portsCount }}"
                                                        style="display: flex; align-items: flex-end; gap: 10px; margin-top: 6px;">
                                                        <div class="port-grid"
                                                            style="background: #222; padding: 6px; border-radius: 4px;">
                                                            @if (!empty($row1))
                                                                <div
                                                                    style="display: flex; flex-direction: column; gap: 3px;">
                                                                    <div style="display: flex; gap: 2px;">
                                                                        @foreach ($row1 as $p)
                                                                            @php
                                                                                $st =
                                                                                    $p['ifOperStatus'] == 'up'
                                                                                        ? 'port-up'
                                                                                        : 'port-down';
                                                                                $port_url = "/device/device={$item['device_id']}/tab=port/port={$p['port_id']}/";
                                                                                $full_title = !empty($p['ifAlias'])
                                                                                    ? htmlspecialchars($p['ifAlias'])
                                                                                    : $p['ifName'];
                                                                            @endphp
                                                                            <a href="{{ $port_url }}"
                                                                                target="_blank"
                                                                                class="port-box {{ $st }}"
                                                                                data-tip="{{ $full_title }}"></a>
                                                                        @endforeach
                                                                    </div>

                                                                </div>
                                                            @endif
                                                            @if (!empty($row2))
                                                                <div
                                                                    style="display: flex; flex-direction: column; gap: 3px;">
                                                                    <div style="display: flex; gap: 2px;">
                                                                        @foreach ($row2 as $p)
                                                                            @php
                                                                                $st =
                                                                                    $p['ifOperStatus'] == 'up'
                                                                                        ? 'port-up'
                                                                                        : 'port-down';
                                                                                $port_url = "/device/device={$item['device_id']}/tab=port/port={$p['port_id']}/";
                                                                                $full_title = !empty($p['ifAlias'])
                                                                                    ? htmlspecialchars($p['ifAlias'])
                                                                                    : $p['ifName'];
                                                                            @endphp
                                                                            <a href="{{ $port_url }}"
                                                                                target="_blank"
                                                                                class="port-box {{ $st }}"
                                                                                data-tip="{{ $full_title }}"></a>
                                                                        @endforeach
                                                                    </div>

                                                                </div>
                                                            @endif

                                                            @if (!empty($sfp))
                                                                <div class="sfp-block"
                                                                    style="background: #222; padding: 6px; border-radius: 4px; display: flex; gap: 3px; align-items: flex-end;">
                                                                    @foreach ($sfp as $p)
                                                                        @php
                                                                            $st =
                                                                                $p['ifOperStatus'] == 'up'
                                                                                    ? 'port-up'
                                                                                    : 'port-down';
                                                                            $port_url = "/device/device={$item['device_id']}/tab=port/port={$p['port_id']}/";
                                                                            $full_title = !empty($p['ifAlias'])
                                                                                ? htmlspecialchars($p['ifAlias'])
                                                                                : $p['ifName'];
                                                                        @endphp
                                                                        <a href="{{ $port_url }}"
                                                                            target="_blank"
                                                                            class="port-box {{ $st }}"
                                                                            data-tip="{{ $full_title }} (SFP)"></a>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front" selected>Front</option>
                                                            <option value="back">Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_front as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'panel' || $item['type'] == 'fiber')
                                            {{-- Блок просмотра панели --}}
                                            <div id="panel-view-{{ $item['id'] }}-{{ $u }}"
                                                class="panel-view">
                                                <div class="item-title">
                                                    <strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editPanel({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Редактировать панель"><i class="fa fa-pencil"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}</div>
                                                @if (!empty($item['ports']))
                                                    @if ($item['type'] == 'fiber')
                                                        {{-- Оптическая панель: группировка по 8 портов в одну строку, увеличенные квадраты --}}
                                                        <div class="fiber-ports"
                                                            style="margin-top: 8px; display: flex; gap: 4px; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px;">
                                                            @foreach ($item['ports'] as $index => $port)
                                                                @php
                                                                    $tooltip = "Порт {$port['port_number']}";
                                                                    if (!empty($port['note'])) {
                                                                        $tooltip .= ": {$port['note']}";
                                                                    }
                                                                    if (!empty($port['fiber_color'])) {
                                                                        $tooltip .= " (Цвет: {$port['fiber_color']})";
                                                                    }
                                                                    $bgColor = !empty($port['fiber_color'])
                                                                        ? $port['fiber_color']
                                                                        : '#555';
                                                                    $border = in_array($bgColor, [
                                                                        'White',
                                                                        'Yellow',
                                                                        'Lime',
                                                                        'Cyan',
                                                                    ])
                                                                        ? '2px solid #888'
                                                                        : '2px solid #000';
                                                                    // Добавляем отступ справа после каждых 8 портов (кроме последнего элемента)
                                                                    $marginRight =
                                                                        ($index + 1) % 8 == 0 &&
                                                                        $index + 1 < count($item['ports'])
                                                                            ? 'margin-right: 20px;'
                                                                            : '';
                                                                @endphp
                                                                <div class="port-box-fiber"
                                                                    style="width: 24px; height: 24px; background: {{ $bgColor }}; border: {{ $border }}; border-radius: 3px; cursor: pointer; {{ $marginRight }}"
                                                                    data-tip="{{ $tooltip }}"></div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        {{-- Медная патч-панель: 24 порта в строке, стандартные квадраты --}}
                                                        <div class="panel-ports" style="margin-top: 6px;">
                                                            @php $portsChunked = array_chunk($item['ports'], 24); @endphp
                                                            @foreach ($portsChunked as $chunk)
                                                                <div
                                                                    style="display: flex; gap: 3px; margin-bottom: 3px;">
                                                                    @foreach ($chunk as $port)
                                                                        @php
                                                                            $tooltip = "Порт {$port['port_number']}";
                                                                            if (!empty($port['note'])) {
                                                                                $tooltip .= ": {$port['note']}";
                                                                            }
                                                                            if (!empty($port['fiber_color'])) {
                                                                                $tooltip .= " (Цвет: {$port['fiber_color']})";
                                                                            }
                                                                            $bgColor = !empty($port['fiber_color'])
                                                                                ? $port['fiber_color']
                                                                                : '#555';
                                                                            $border = in_array($bgColor, [
                                                                                'White',
                                                                                'Yellow',
                                                                                'Lime',
                                                                                'Cyan',
                                                                            ])
                                                                                ? '1px solid #888'
                                                                                : '1px solid #000';
                                                                        @endphp
                                                                        <div class="port-box"
                                                                            style="width: 12px; height: 12px; background: {{ $bgColor }}; border: {{ $border }};"
                                                                            data-tip="{{ $tooltip }}"></div>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="text-muted">[Нет портов]</div>
                                                @endif
                                                <div class="text-muted small" style="margin-top: 4px;">
                                                    <a href="{{ url('plugin/CablingJournal?panel_id=' . $item['id'] . '&rack_id=' . $selected_rack . '&location_id=' . $selected_location) }}"
                                                        class="btn btn-xs btn-default"><i class="fa fa-pencil"></i>
                                                        Редактировать порты</a>
                                                </div>
                                            </div>
                                            {{-- Блок редактирования панели --}}
                                            <div id="panel-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <div class="item-title">
                                                        <input type="text" name="name"
                                                            class="form-control input-sm"
                                                            style="width: auto; display: inline-block;"
                                                            value="{{ htmlspecialchars($item['name']) }}" required>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditPanel({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="model"
                                                            class="form-control input-sm"
                                                            style="width: 200px; margin-top: 5px;"
                                                            value="{{ htmlspecialchars($item['model']) }}"
                                                            placeholder="Модель">
                                                    </div>
                                                    <div>
                                                        <input type="text" name="note"
                                                            class="form-control input-sm"
                                                            style="width: 100%; margin-top: 5px;"
                                                            value="{{ htmlspecialchars($item['note']) }}"
                                                            placeholder="Примечание">
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front" selected>Front</option>
                                                            <option value="back">Back</option>
                                                        </select>
                                                        <label>Начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_front as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'socket220')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    <i class="fa fa-bolt"
                                                        style="color: #f39c12; margin-right: 5px;"></i><strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}
                                                </div>
                                                <div style="display: flex; gap: 8px; margin-top: 5px;">
                                                    @for ($i = 1; $i <= 7; $i++)
                                                        <div
                                                            style="width: 30px; height: 30px; background: #eee; border: 1px solid #aaa; border-radius: 4px; text-align: center; line-height: 28px;">
                                                            <i class="fa fa-plug" style="color: #555;"></i>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $item['name'] }}">
                                                    <input type="hidden" name="model"
                                                        value="{{ $item['model'] }}">
                                                    <input type="hidden" name="note"
                                                        value="{{ $item['note'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front" selected>Front</option>
                                                            <option value="back">Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_front as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'UPS')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    <i class="fa fa-battery-full"
                                                        style="font-size: 1.3em; color: #2ecc71;"></i><strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}
                                                </div>

                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $item['name'] }}">
                                                    <input type="hidden" name="model"
                                                        value="{{ $item['model'] }}">
                                                    <input type="hidden" name="note"
                                                        value="{{ $item['note'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front" selected>Front</option>
                                                            <option value="back">Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_front as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'other')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    </i><strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}
                                                </div>

                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $item['name'] }}">
                                                    <input type="hidden" name="model"
                                                        value="{{ $item['model'] }}">
                                                    <input type="hidden" name="note"
                                                        value="{{ $item['note'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front" selected>Front</option>
                                                            <option value="back">Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_front as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
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
                        @for ($u = $max_units; $u >= 1; $u--)
                            @php
                                $item = $occupied_units_back[$u] ?? null;
                                $hasItem = !is_null($item);
                                $isEmpty = !$hasItem;
                                $unitClass = $isEmpty ? 'empty-u' : ($item['type'] == 'device' ? 'occupied' : 'panel');
                            @endphp
                            <div class="rack-unit {{ $unitClass }}" data-unit="{{ $u }}">
                                <div class="unit-num">{{ $u }}</div>
                                <div class="unit-content">
                                    @if ($hasItem)
                                        @if ($item['type'] == 'device')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    <a href="/device/device={{ $item['device_id'] }}/"
                                                        target="_blank"><strong>{{ $item['name'] }}</strong></a>
                                                    <div class="text-muted small">{{ $item['model'] }}</div>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                @if (!empty($item['ports']))
                                                    @php
                                                        $portsCount = count($item['ports']);
                                                        if ($portsCount > 48) {
                                                            $row1 = array_slice($item['ports'], 0, 24);
                                                            $row2 = array_slice($item['ports'], 24, 24);
                                                            $sfp = array_slice($item['ports'], 48);
                                                        } else {
                                                            $row1 = array_slice($item['ports'], 0, 12);
                                                            $row2 = array_slice($item['ports'], 12, 12);
                                                            $sfp = array_slice($item['ports'], 24);
                                                        }
                                                    @endphp
                                                    <div custom-cls="{{ $portsCount }}"
                                                        style="display: flex; align-items: flex-end; gap: 10px; margin-top: 6px;">
                                                        <div class="port-grid"
                                                            style="background: #222; padding: 6px; border-radius: 4px;">
                                                            @if (!empty($row1))
                                                                <div
                                                                    style="display: flex; flex-direction: column; gap: 3px;">
                                                                    <div style="display: flex; gap: 2px;">
                                                                        @foreach ($row1 as $p)
                                                                            @php
                                                                                $st =
                                                                                    $p['ifOperStatus'] == 'up'
                                                                                        ? 'port-up'
                                                                                        : 'port-down';
                                                                                $port_url = "/device/device={$item['device_id']}/tab=port/port={$p['port_id']}/";
                                                                                $full_title = !empty($p['ifAlias'])
                                                                                    ? htmlspecialchars($p['ifAlias'])
                                                                                    : $p['ifName'];
                                                                            @endphp
                                                                            <a href="{{ $port_url }}"
                                                                                target="_blank"
                                                                                class="port-box {{ $st }}"
                                                                                data-tip="{{ $full_title }}"></a>
                                                                        @endforeach
                                                                    </div>

                                                                </div>
                                                            @endif
                                                            @if (!empty($row2))
                                                                <div
                                                                    style="display: flex; flex-direction: column; gap: 3px;">
                                                                    <div style="display: flex; gap: 2px;">
                                                                        @foreach ($row2 as $p)
                                                                            @php
                                                                                $st =
                                                                                    $p['ifOperStatus'] == 'up'
                                                                                        ? 'port-up'
                                                                                        : 'port-down';
                                                                                $port_url = "/device/device={$item['device_id']}/tab=port/port={$p['port_id']}/";
                                                                                $full_title = !empty($p['ifAlias'])
                                                                                    ? htmlspecialchars($p['ifAlias'])
                                                                                    : $p['ifName'];
                                                                            @endphp
                                                                            <a href="{{ $port_url }}"
                                                                                target="_blank"
                                                                                class="port-box {{ $st }}"
                                                                                data-tip="{{ $full_title }}"></a>
                                                                        @endforeach
                                                                    </div>

                                                                </div>
                                                            @endif

                                                            @if (!empty($sfp))
                                                                <div class="sfp-block"
                                                                    style="background: #222; padding: 6px; border-radius: 4px; display: flex; gap: 3px; align-items: flex-end;">
                                                                    @foreach ($sfp as $p)
                                                                        @php
                                                                            $st =
                                                                                $p['ifOperStatus'] == 'up'
                                                                                    ? 'port-up'
                                                                                    : 'port-down';
                                                                            $port_url = "/device/device={$item['device_id']}/tab=port/port={$p['port_id']}/";
                                                                            $full_title = !empty($p['ifAlias'])
                                                                                ? htmlspecialchars($p['ifAlias'])
                                                                                : $p['ifName'];
                                                                        @endphp
                                                                        <a href="{{ $port_url }}"
                                                                            target="_blank"
                                                                            class="port-box {{ $st }}"
                                                                            data-tip="{{ $full_title }} (SFP)"></a>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front" selected>Front</option>
                                                            <option value="back">Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_front as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'panel' || $item['type'] == 'fiber')
                                            {{-- Блок просмотра панели --}}
                                            <div id="panel-view-{{ $item['id'] }}-{{ $u }}"
                                                class="panel-view">
                                                <div class="item-title">
                                                    <strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editPanel({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Редактировать панель"><i class="fa fa-pencil"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}
                                                </div>
                                                @if (!empty($item['ports']))
                                                    @if ($item['type'] == 'fiber')
                                                        {{-- Оптическая панель: группировка по 8 портов в одну строку, увеличенные квадраты --}}
                                                        <div class="fiber-ports"
                                                            style="margin-top: 8px; display: flex; gap: 4px; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px;">
                                                            @foreach ($item['ports'] as $index => $port)
                                                                @php
                                                                    $tooltip = "Порт {$port['port_number']}";
                                                                    if (!empty($port['note'])) {
                                                                        $tooltip .= ": {$port['note']}";
                                                                    }
                                                                    if (!empty($port['fiber_color'])) {
                                                                        $tooltip .= " (Цвет: {$port['fiber_color']})";
                                                                    }
                                                                    $bgColor = !empty($port['fiber_color'])
                                                                        ? $port['fiber_color']
                                                                        : '#555';
                                                                    $border = in_array($bgColor, [
                                                                        'White',
                                                                        'Yellow',
                                                                        'Lime',
                                                                        'Cyan',
                                                                    ])
                                                                        ? '2px solid #888'
                                                                        : '2px solid #000';
                                                                    // Добавляем отступ справа после каждых 8 портов (кроме последнего элемента)
                                                                    $marginRight =
                                                                        ($index + 1) % 8 == 0 &&
                                                                        $index + 1 < count($item['ports'])
                                                                            ? 'margin-right: 20px;'
                                                                            : '';
                                                                @endphp
                                                                <div class="port-box-fiber"
                                                                    style="width: 24px; height: 24px; background: {{ $bgColor }}; border: {{ $border }}; border-radius: 3px; cursor: pointer; {{ $marginRight }}"
                                                                    data-tip="{{ $tooltip }}"></div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        {{-- Медная патч-панель: 24 порта в строке, стандартные квадраты --}}
                                                        <div class="panel-ports" style="margin-top: 6px;">
                                                            @php $portsChunked = array_chunk($item['ports'], 24); @endphp
                                                            @foreach ($portsChunked as $chunk)
                                                                <div
                                                                    style="display: flex; gap: 3px; margin-bottom: 3px;">
                                                                    @foreach ($chunk as $port)
                                                                        @php
                                                                            $tooltip = "Порт {$port['port_number']}";
                                                                            if (!empty($port['note'])) {
                                                                                $tooltip .= ": {$port['note']}";
                                                                            }

                                                                            $bgColor = !empty($port['fiber_color'])
                                                                                ? $port['fiber_color']
                                                                                : '#555';
                                                                            $border = in_array($bgColor, [
                                                                                'White',
                                                                                'Yellow',
                                                                                'Lime',
                                                                                'Cyan',
                                                                            ])
                                                                                ? '1px solid #888'
                                                                                : '1px solid #000';
                                                                        @endphp
                                                                        <div class="port-box"
                                                                            style=" background: {{ $bgColor }}; border: {{ $border }};"
                                                                            data-tip="{{ $tooltip }}"></div>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="text-muted">[Нет портов]</div>
                                                @endif
                                                <div class="text-muted small" style="margin-top: 4px;">
                                                    <a href="{{ url('plugin/CablingJournal?panel_id=' . $item['id'] . '&rack_id=' . $selected_rack . '&location_id=' . $selected_location) }}"
                                                        class="btn btn-xs btn-default"><i class="fa fa-pencil"></i>
                                                        Редактировать порты</a>
                                                </div>
                                            </div>
                                            {{-- Блок редактирования панели --}}
                                            <div id="panel-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <div class="item-title">
                                                        <input type="text" name="name"
                                                            class="form-control input-sm"
                                                            style="width: auto; display: inline-block;"
                                                            value="{{ htmlspecialchars($item['name']) }}" required>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditPanel({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="model"
                                                            class="form-control input-sm"
                                                            style="width: 200px; margin-top: 5px;"
                                                            value="{{ htmlspecialchars($item['model']) }}"
                                                            placeholder="Модель">
                                                    </div>
                                                    <div>
                                                        <input type="text" name="note"
                                                            class="form-control input-sm"
                                                            style="width: 100%; margin-top: 5px;"
                                                            value="{{ htmlspecialchars($item['note']) }}"
                                                            placeholder="Примечание">
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front">Front</option>
                                                            <option value="back" selected>Back</option>
                                                        </select>
                                                        <label>Начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_back as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'socket220')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    <i class="fa fa-bolt"
                                                        style="color: #f39c12; margin-right: 5px;"></i><strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}
                                                </div>
                                                <div style="display: flex; gap: 8px; margin-top: 5px;">
                                                    @for ($i = 1; $i <= 7; $i++)
                                                        <div
                                                            style="width: 30px; height: 30px; background: #eee; border: 1px solid #aaa; border-radius: 4px; text-align: center; line-height: 28px;">
                                                            <i class="fa fa-plug" style="color: #555;"></i>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $item['name'] }}">
                                                    <input type="hidden" name="model"
                                                        value="{{ $item['model'] }}">
                                                    <input type="hidden" name="note"
                                                        value="{{ $item['note'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front">Front</option>
                                                            <option value="back" selected>Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_back as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'UPS')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    <i class="fa fa-battery-full"
                                                        style="font-size: 1.3em; color: #2ecc71;"></i><strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}
                                                </div>

                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $item['name'] }}">
                                                    <input type="hidden" name="model"
                                                        value="{{ $item['model'] }}">
                                                    <input type="hidden" name="note"
                                                        value="{{ $item['note'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <label>Сторона шкафа:</label>
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front">Front</option>
                                                            <option value="back" selected>Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_back as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($item['type'] == 'other')
                                            {{-- Блок просмотра устройства --}}
                                            <div id="device-view-{{ $item['id'] }}-{{ $u }}"
                                                class="device-view">
                                                <div class="item-title">
                                                    </i><strong>{{ $item['name'] }}</strong>
                                                    <a href="javascript:void(0)"
                                                        onclick="editDevice({{ $item['id'] }}, {{ $u }})"
                                                        class="text-info pull-right" style="margin-left: 10px;"
                                                        title="Переместить устройство"><i
                                                            class="fa fa-arrows"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDeleteItem({{ $selected_rack }}, {{ $u }}, {{ $selected_location }})"
                                                        class="text-danger pull-right" style="margin-left: 10px;"
                                                        title="Удалить из шкафа"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                                <div class="text-muted small">{{ $item['model'] }}</div>
                                                <div class="panel-note small text-muted">{{ $item['note'] }}
                                                </div>

                                            </div>
                                            {{-- Блок редактирования устройства (только перемещение) --}}
                                            <div id="device-edit-{{ $item['id'] }}-{{ $u }}"
                                                style="display:none;">
                                                <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                                                    <input type="hidden" name="action" value="edit_panel">
                                                    <input type="hidden" name="rack_id"
                                                        value="{{ $selected_rack }}">
                                                    <input type="hidden" name="panel_id"
                                                        value="{{ $item['id'] }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $item['name'] }}">
                                                    <input type="hidden" name="model"
                                                        value="{{ $item['model'] }}">
                                                    <input type="hidden" name="note"
                                                        value="{{ $item['note'] }}">
                                                    <input type="hidden" name="location_id"
                                                        value="{{ $selected_location }}">
                                                    <input type="hidden" name="old_unit"
                                                        value="{{ $u }}">
                                                    <div class="item-title">
                                                        <strong>Перемещение {{ $item['name'] }}</strong>
                                                        <button type="submit"
                                                            class="btn btn-success btn-xs pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-save"></i>
                                                            Сохранить</button>
                                                        <a href="javascript:void(0)"
                                                            onclick="cancelEditDevice({{ $item['id'] }}, {{ $u }})"
                                                            class="text-muted pull-right"
                                                            style="margin-left: 10px;"><i class="fa fa-times"></i>
                                                            Отмена</a>
                                                    </div>
                                                    <div style="margin-top: 5px;">
                                                        <select name="rack_side" class="form-control">
                                                            <option value="front" selected>Front</option>
                                                            <option value="back">Back</option>
                                                        </select>
                                                        <label>Новый начальный юнит:</label>
                                                        <select name="new_start_unit" class="form-control input-sm"
                                                            style="width: auto; display: inline-block;">
                                                            <option value="{{ $u }}" selected>
                                                                {{ $u }}</option>
                                                            @foreach ($free_units_back as $su)
                                                                <option value="{{ $su }}">
                                                                    {{ $su }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-muted small"> (высота:
                                                            {{ $item['unit_count'] }}U)</span>
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
        <style>
            .list-group::item {
                display: block;
                padding: 15px;
                border-bottom: 1px solid #eee;
                text-decoration: none;
                color: #333;
            }

            .list-group::item:hover {
                background: #f9f9f9;
            }

            /* Общие отступы */
            body {
                background: #f4f4f4;
                padding: 20px;
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            }

            /* Сетка портов */
            .port-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 3px;
                margin-top: 5px;
                padding: 6px;
                background: #222;
                border-radius: 4px;
                width: fit-content;
            }

            .port-box {
                width: 18px;
                height: 18px;
                background: #555;
                border: 1px solid #000;
                cursor: pointer;
                display: block;
                border-radius: 1px;
            }

            .port-up {
                background: #2ecc71 !important;
                box-shadow: 0 0 4px #2ecc71;
            }

            .port-down {
                background: #e74c3c !important;
            }

            /* Группировка портов */
            .row-container {
                display: flex;
                flex-direction: column;
                gap: 3px;
            }

            .sfp-block {
                margin-left: 12px;
                border-left: 2px solid #444;
                padding-left: 8px;
                display: flex;
                align-items: flex-end;
                gap: 3px;
                height: 27px;
            }

            /* Юнит шкафа */
            .rack-unit {
                border-bottom: 1px solid #ddd;
                min-height: 55px;
                /* Увеличили, чтобы влезло 2 ряда портов */
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

            .unit-content {
                flex-grow: 1;
                padding: 8px 15px;
                overflow: hidden;
            }

            /* Цвета заполнения */
            .occupied {
                background: #f0f9ff !important;
                border-left: 5px solid #3498db;
            }

            .panel {
                background: #fffef0 !important;
                border-left: 5px solid #f1c40f;
            }

            .text-muted {
                color: #bbb;
                font-size: 12px;
            }

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
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
                font-size: 16px;
                /* Вот здесь регулируем размер текста */
                z-index: 10000;
                pointer-events: none;
                /* Чтобы не мешал кликам */
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                border: 1px solid #555;
                max-width: 400px;
                line-height: 1.4;
            }

            .tooltip-header {
                color: #3498db;
                font-weight: bold;
                margin-bottom: 5px;
                border-bottom: 1px solid #444;
            }
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
        <script>
            // Пробрасываем все пресеты в JS
            const allPresets = @json($presets);

            function applyPanelPreset(selectedName) {
                if (!selectedName) return;

                // Элементы формы, которые нужно заполнить
                const unitCountInput = document.querySelector('input[name="unit_count"]');
                const techSelect = document.getElementById('panel_tech');
                const portInput = document.getElementById('panel_port_count');
                const nameInput = document.getElementById('panel_name_input');
                const modelHidden = document.getElementById('panel_model_hidden');

                if (selectedName === 'custom') {
                    modelHidden.value = '';
                    return;
                }

                // Ищем модель в массиве
                const found = allPresets.find(p => p.name === selectedName);

                if (found) {
                    // 1. Заполняем высоту (юниты)
                    if (unitCountInput) {
                        unitCountInput.value = found.unit || found.u || 1;
                    }

                    // 2. Заполняем технологию (приводим к нижнему регистру для соответствия value в select)
                    if (techSelect) {
                        const type = (found.type || 'panel').toLowerCase();
                        techSelect.value = type;
                    }

                    // 3. Заполняем количество портов
                    if (portInput) {
                        portInput.value = found.ports || 0;
                    }

                    // 4. Сохраняем имя модели в скрытое поле для записи в БД
                    modelHidden.value = found.name;

                    // 5. Для удобства подставим имя модели в "Имя панели", если оно пустое
                    if (nameInput && !nameInput.value) {
                        nameInput.value = found.name;
                    }
                }
            }
        </script>
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
                    window.location.href = '{{ url('plugin/CablingJournal') }}?action=delete_item&rack_id=' + rackId +
                        '&unit=' + unit + '&location_id=' + locationId;
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
                    case 'panel':
                        optionsHtml = '<option value="24">24 порта</option><option value="48">48 портов</option>';
                        break;
                    case 'fiber':
                        optionsHtml =
                            '<option value="8">8 портов</option><option value="16">16 портов</option><option value="24">24 порта</option><option value="32">32 порта</option>';
                        break;
                    default:
                        optionsHtml = '<option value="0">0 портов (нет)</option>';
                        break;
                }
                portCountSelect.innerHTML = optionsHtml;
                portCountSelect.disabled = (type === 'socket220' || type === 'UPS');
            }
            if (panelTechSelect) panelTechSelect.addEventListener('change', updatePortOptions);
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
                <a href="{{ url('plugin/CablingJournal?location_id=' . $selected_location . '&rack_id=' . $selected_rack) }}"
                    class="btn btn-default btn-xs pull-right">Вернуться к шкафу</a>
            </div>
            <div class="panel-body">
                {{-- Форма управления кабелями (сверху) --}}
                <div class="panel panel-default">
                    <div class="panel-heading" style="padding: 8px 15px;">
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="toggleForm('cableForm')">Управление кабелями</button>
                    </div>
                    <div id="cableForm" style="display:none;" class="panel-body">
                        <form method="GET" action="{{ url('plugin/CablingJournal') }}">
                            @csrf
                            <input type="hidden" name="action" value="manage_cable">
                            <input type="hidden" name="panel_id" value="{{ $panel['id'] }}">
                            <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                            <input type="hidden" name="location_id" value="{{ $selected_location }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Выберите порт:</label>
                                    <select name="port_id" class="form-control" required>
                                        <option value="">-- выберите порт --</option>
                                        @foreach ($ports as $port)
                                            <option value="{{ $port['id'] }}">Порт
                                                {{ $port['port_number'] }}
                                                ({{ $port['status'] ?? 'Active' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Сторона кабеля:</label>
                                    <select name="side" class="form-control">
                                        <option value="A">Сторона A</option>
                                        <option value="B">Сторона B</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Действие:</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="existing_cable_id" class="form-control">
                                                <option value="">-- выбрать существующий кабель --
                                                </option>
                                                @foreach ($all_links as $linkId => $link)
                                                    <option value="{{ $linkId }}">
                                                        {{ $link['cbl_id'] ?? $link['name'] }}
                                                        ({{ $link['type'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" name="action_type" value="link_existing"
                                                class="btn btn-info btn-block">Привязать выбранный</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Создать новый кабель:</label>
                                            <input type="text" name="new_cable_name" class="form-control"
                                                placeholder="Имя кабеля (уникальное)">
                                            <input type="text" name="new_cable_type" class="form-control"
                                                placeholder="Тип (copper/fiber)">
                                            <input type="number" name="new_cable_count_cords"
                                                class="form-control" placeholder="Количество жил">
                                            <input type="text" name="new_cable_model" class="form-control"
                                                placeholder="Модель кабеля">
                                        </div>
                                        <div class="col-md-12 text-right" style="margin-top: 10px;">
                                            <button type="submit" name="action_type" value="create_and_link"
                                                class="btn btn-success">Создать и
                                                привязать</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Форма для массового обновления портов (сохранить все) --}}
                <form method="GET" action="{{ url('plugin/CablingJournal') }}" id="portsForm">
                    @csrf
                    <input type="hidden" name="action" value="update_all_ports">
                    <input type="hidden" name="panel_id" value="{{ $panel['id'] }}">
                    <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                    <input type="hidden" name="location_id" value="{{ $selected_location }}">
                    <table class="table table-condensed table-bordered" style="width: 100%; table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">№ порта</th>
                                @if ($panel['type'] == 'fiber')
                                    <th style="width: 100px;">Цвет</th>
                                @endif
                                <th style="width: 110px;">Статус</th>
                                <th>Примечание</th>
                                <th>Кабель</th>
                                <th style="width: 80px;">Отвязать</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ports as $port)
                                @php
                                    $linkedCable = collect($all_links)->first(function ($cable) use ($port) {
                                        return ($cable['side_a_type'] == 'Panel_Port' &&
                                            $cable['side_a_id'] == $port['id']) ||
                                            ($cable['side_b_type'] == 'Panel_Port' &&
                                                $cable['side_b_id'] == $port['id']);
                                    });
                                @endphp
                                <tr>
                                    <td style="width: 50px; white-space: nowrap;">{{ $port['port_number'] }}
                                    </td>
                                    @if ($panel['type'] == 'fiber')
                                        <td style="width: 100px;">
                                            <select name="fiber_color[{{ $port['id'] }}]"
                                                class="form-control input-sm">
                                                <option value="">-- цвет --</option>
                                                <option value="Red"
                                                    {{ ($port['fiber_color'] ?? '') == 'Red' ? 'selected' : '' }}>
                                                    Красный</option>
                                                <option value="Green"
                                                    {{ ($port['fiber_color'] ?? '') == 'Green' ? 'selected' : '' }}>
                                                    Зелёный</option>
                                                <option value="Blue"
                                                    {{ ($port['fiber_color'] ?? '') == 'Blue' ? 'selected' : '' }}>
                                                    Синий</option>
                                                <option value="Yellow"
                                                    {{ ($port['fiber_color'] ?? '') == 'Yellow' ? 'selected' : '' }}>
                                                    Жёлтый</option>
                                                <option value="White"
                                                    {{ ($port['fiber_color'] ?? '') == 'White' ? 'selected' : '' }}>
                                                    Белый</option>
                                                <option value="SlateGray"
                                                    {{ ($port['fiber_color'] ?? '') == 'SlateGray' ? 'selected' : '' }}>
                                                    Серый</option>
                                                <option value="Brown"
                                                    {{ ($port['fiber_color'] ?? '') == 'Brown' ? 'selected' : '' }}>
                                                    Коричневый</option>
                                                <option value="Violet"
                                                    {{ ($port['fiber_color'] ?? '') == 'Violet' ? 'selected' : '' }}>
                                                    Фиолетовый</option>
                                            </select>
                                        </td>
                                    @endif
                                    <td style="width: 110px;">
                                        <select name="status[{{ $port['id'] }}]" class="form-control input-sm">
                                            <option value="Active"
                                                {{ ($port['status'] ?? 'Active') == 'Active' ? 'selected' : '' }}>
                                                Work</option>
                                            <option value="Broken"
                                                {{ ($port['status'] ?? '') == 'Broken' ? 'selected' : '' }}>
                                                Broken</option>
                                            <option value="Free"
                                                {{ ($port['status'] ?? '') == 'Free' ? 'selected' : '' }}>Free
                                            </option>
                                        </select>
                                    </td>
                                    <td style="word-break: break-word;">
                                        <input type="text" name="note[{{ $port['id'] }}]"
                                            class="form-control input-sm"
                                            value="{{ htmlspecialchars($port['note'] ?? '') }}"
                                            placeholder="Описание порта">
                                    </td>
                                    <td style="word-break: break-word; font-size: 1.2em;">
                                        @if ($linkedCable)
                                            <span
                                                class="label label-info">{{ $linkedCable['cbl_id'] ?? $linkedCable['name'] }}</span>
                                            <small class="text-muted" style="font-size: 0.85rem;">
                                                ({{ $linkedCable['side_a_id'] == $port['id'] ? 'A' : 'B' }} →
                                                @if ($linkedCable['side_a_id'] == $port['id'])
                                                    {{ $linkedCable['side_b_type'] }}:{{ $linkedCable['side_b_id'] }}
                                                @else
                                                    {{ $linkedCable['side_a_type'] }}:{{ $linkedCable['side_a_id'] }}
                                                @endif
                                                )
                                            </small>
                                        @else
                                            <span class="text-muted">— не привязан —</span>
                                        @endif
                                    </td>
                                    <td style="width: 80px; text-align: center;">
                                        @if ($linkedCable)
                                            <button type="submit" form="unlinkForm_{{ $port['id'] }}"
                                                class="btn btn-danger btn-xs"><i class="fa fa-unlink"></i>
                                                Отвязать</button>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-right" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary">Сохранить изменения портов</button>
                    </div>
                </form>

                {{-- Формы отвязки, размещенные вне основной формы --}}
                @foreach ($ports as $port)
                    @php
                        $linkedCable = collect($all_links)->first(function ($cable) use ($port) {
                            return ($cable['side_a_type'] == 'Panel_Port' && $cable['side_a_id'] == $port['id']) ||
                                ($cable['side_b_type'] == 'Panel_Port' && $cable['side_b_id'] == $port['id']);
                        });
                    @endphp
                    @if ($linkedCable)
                        <form method="GET" action="{{ url('plugin/CablingJournal') }}"
                            id="unlinkForm_{{ $port['id'] }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="action" value="unlink_port">
                            <input type="hidden" name="port_id" value="{{ $port['id'] }}">
                            <input type="hidden" name="cable_id" value="{{ $linkedCable['id'] }}">
                            <input type="hidden" name="panel_id" value="{{ $panel['id'] }}">
                            <input type="hidden" name="rack_id" value="{{ $selected_rack }}">
                            <input type="hidden" name="location_id" value="{{ $selected_location }}">
                        </form>
                    @endif
                @endforeach
            </div>
        </div>

        <script>
            function toggleForm(formId) {
                var formDiv = document.getElementById(formId);
                if (formDiv.style.display === 'none' || formDiv.style.display === '') {
                    formDiv.style.display = 'block';
                } else {
                    formDiv.style.display = 'none';
                }
            }
        </script>

        @endif
    </div>
</div>
</div>
