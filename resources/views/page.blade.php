cat page.blade.php
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

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
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
            {{-- СОСТОЯНИЕ 2: ЛОКАЦИЯ ВЫБРАНА --}}
            @elseif(!$selected_rack || $selected_rack == 0)
                {{-- Форма добавления (показывается только в контексте локации) --}}
                <div class="panel panel-default">
                    <div class="panel-heading">Добавить шкаф в локацию: <strong>{{ $location_name }}</strong></div>
                    <div class="panel-body">
                        <form action="{{ url('plugin/CablingJournal') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Шкаф / Rack:</label>
                                    <input type="text" name="rack_name" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Производитель (Модель):</label>
                                    <input type="text" name="vendor" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>U:</label>
                                    <input type="text" name="unit_size" class="form-control" placeholder="SM / UTP">
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Добавить</button>
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
                                @forelse($racks as $rack)
                                    @if($rack['location_id'] == $selected_location)
                                    <tr class="clickable-row"     data-href="{{ url('plugin/CablingJournal?location_id='.$selected_location.'&rack_id='.$rack['id']) }}"     style="cursor: pointer;">
                                        <td><strong>{{ $rack['name'] }}</strong></td>
                                        <td>{{ $rack['floor'] ?? '-' }}</td>
                                        <td>{{ $rack['units'] }}U</td>
                                        <td>{{ $rack['type'] }}</td>
                                        <td><small>{{ $rack['note'] }}</small></td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr><td colspan="5" class="text-center">В этой локации пока нет созданных шкафов в database.php</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- СОСТОЯНИЕ 3: ЛОКАЦИЯ ВЫБРАНА И ВЫБРАН ШКАФ--}}
            @else
                {{-- Форма добавления (показывается только в контексте шкафа) --}}
                <div class="panel panel-default">
                    <div class="panel-heading">Добавить панель в шкаф: <strong>{{ $rack_name }}</strong></div>
                    <div class="panel-body">
                        <form action="{{ url('plugin/CablingJournal') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Шкаф / Rack:</label>
                                    <input type="text" name="panel_name" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Производитель (Модель):</label>
                                    <input type="text" name="vendor" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>U:</label>
                                    <input type="text" name="unit_size" class="form-control" placeholder="SM / UTP">
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Добавить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Таблица панелей в этом шкафу --}}
                <div class="panel panel-default">
                    <div class="panel-heading">Список панелей в этом шкафу</div>
                    <div class='rack-container col-md-11' style='background:#fff; border:1px solid #ccc; padding:0;'>
                    @foreach($panels as $panel)
                        @if ($panel['type'] == 'device'
                            @php
                            $row1 = array_slice($panel['ports'], 0, 24);
                            $row2 = array_slice($panel['ports'], 24, 24);
                            $sfp  = array_slice($panel['ports'], 48);

                            @endphp
                            <div class='row-container'>
                            @foreach ([$row1, $row2] as $row)
                                <div style='display:flex; gap:2px;'>
                                @foreach ($row as $p)
                                        @php
                                        $st = ($p['ifOperStatus'] == 'up') ? 'port-up' : 'port-down';
                                    // Оборачиваем в ссылку на конкретный порт
                                        $port_url = "/device/device={$item['id']}/tab=port/port={$p['port_id']}/";
                                        // Если description (ifAlias) пустой, выводим только имя порта
                                        $full_title = !empty($p['ifAlias']) ? htmlspecialchars($p['ifAlias']) : $p['ifName'];

                                        @endphp
                                        <a href='{{ $port_url }}' target='_blank' class='port-box {{ $st }}' data-tip='{{ $full_title }}'></a>
                                @endforeach
                                </div>
                            @endforeach
                            </div>
                            <div class='sfp-block'>
                            @foreach ($sfp as $p)
                                @php
                                $st = ($p['ifOperStatus'] == 'up') ? 'port-up' : 'port-down';
                                $port_url = "/device/device={$item['id']}/tab=port/port={$p['port_id']}/";
                                $full_title = !empty($p['ifAlias']) ? htmlspecialchars($p['ifAlias']) : $p['ifName'];
                                @endphp
                                <a href='{{ $port_url }}' target='_blank' class='port-box {{ $st }}' data-tip='{{ $full_title }} (ID: {{ $p['port_id'] }})'></a>
                            @endforeach
                            </div>
                        @endif
                    @endforeach
                    </div>
                </div>

            @endif


        </div>
    </div>
</div>

<style>
    .list-group::item { display: block; padding: 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; }
    .list-group::item:hover { background: #f9f9f9; }
</style>
<script>
$(document).ready(function() {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});
</script>
