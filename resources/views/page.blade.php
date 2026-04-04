@extends('layouts.app')

@section('title', 'Кабельный журнал')

@section('content')
<div class="container-fluid">
    <h3>{{ $title }}</h3>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="panel panel-default">
        <div class="panel-body">
            {{-- Форма из вашего скрипта --}}
            <form action="{{ route('plugin', ['plugin' => 'CablingJournal']) }}" method="POST" id="journalForm">
                @csrf
                <div class="row">
                    {{-- Выбор типа устройства --}}
                    <div class="col-md-3">
                        <label>Тип устройства:</label>
                        <select name="device_type" id="device_type" class="form-control" onchange="toggleFields()">
                            <option value="active">Активное (LibreNMS)</option>
                            <option value="passive">Пассивное (ODF/PatchPanel)</option>
                        </select>
                    </div>

                    {{-- Поля для Активного устройства --}}
                    <div id="active_fields">
                        <div class="col-md-3">
                            <label>Устройство:</label>
                            <select name="device_id" class="form-control">
                                @foreach($devices as $device)
                                    <option value="{{ $device->device_id }}">{{ $device->hostname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Port ID:</label>
                            <input type="number" name="port_id" class="form-control" placeholder="105">
                        </div>
                    </div>

                    {{-- Поля для Пассивного устройства (скрыты по умолчанию) --}}
                    <div id="passive_fields" style="display:none;">
                        <div class="col-md-3">
                            <label>Название панели:</label>
                            <input type="text" name="passive_name" class="form-control" placeholder="ODF-01">
                        </div>
                        <div class="col-md-3">
                            <label>Юнит/Слот:</label>
                            <input type="text" name="passive_unit" class="form-control" placeholder="Slot 5">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>Тип кабеля:</label>
                        <input type="text" name="cable_type" class="form-control" placeholder="SM 9/125">
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Таблица --}}
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Устройство</th>
                        <th>Порт/Юнит</th>
                        <th>Тип кабеля</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cables as $cable)
                    <tr>
                        <td>{{ $cable->device_name ?? $cable->passive_name }}</td>
                        <td>{{ $cable->port_name ?? $cable->passive_unit }}</td>
                        <td>{{ $cable->cable_type }}</td>
                        <td>{{ $cable->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Вставляем ваш JS --}}
<script>
function toggleFields() {
    var type = document.getElementById("device_type").value;
    var activeFields = document.getElementById("active_fields");
    var passiveFields = document.getElementById("passive_fields");

    if (type === "active") {
        activeFields.style.display = "block";
        passiveFields.style.display = "none";
    } else {
        activeFields.style.display = "none";
        passiveFields.style.display = "block";
    }
}
    $(document).ready(function() {
    $('#device_type').on('change', function() {
        if($(this).val() == 'passive') {
            $('#active_fields').hide();
            $('#passive_fields').show();
        } else {
            $('#active_fields').show();
            $('#passive_fields').hide();
        }
    });
});
</script>
@endsection
