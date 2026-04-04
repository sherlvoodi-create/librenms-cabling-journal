@extends('layouts.app')

@section('title', 'Кабельный журнал')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h3>{{ $title }}</h3>

            {{-- Сообщение об успешном сохранении --}}
            @if(session('status'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Форма добавления (из вашего скрипта) --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Добавить новую запись</h3>
                </div>
                <div class="panel-body">
                    <form action="{{ route('plugin', ['plugin' => 'CablingJournal']) }}" method="POST" class="form-horizontal">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label>Устройство (Device ID):</label>
                                <input type="number" name="device_id" class="form-control" required placeholder="Например: 1">
                            </div>
                            <div class="col-md-3">
                                <label>Порт (Port ID):</label>
                                <input type="number" name="port_id" class="form-control" required placeholder="Например: 105">
                            </div>
                            <div class="col-md-3">
                                <label>Тип кабеля:</label>
                                <input type="text" name="cable_type" class="form-control" placeholder="UTP / Fiber">
                            </div>
                            <div class="col-md-3">
                                <label>Описание:</label>
                                <input type="text" name="description" class="form-control" placeholder="Стойка А, юнит 5">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Сохранить в журнал
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Таблица записей (результат вашего JOIN запроса) --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">История соединений</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-striped table-condensed borderless">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Устройство (Hostname)</th>
                                <th>Порт (Interface)</th>
                                <th>Тип кабеля</th>
                                <th>Описание</th>
                                <th style="width: 150px;">Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cables as $cable)
                                <tr>
                                    <td>{{ $cable->id }}</td>
                                    <td><strong>{{ $cable->device_name ?? 'ID: '.$cable->device_id }}</strong></td>
                                    <td>{{ $cable->port_name ?? 'ID: '.$cable->port_id }}</td>
                                    <td><span class="label label-info">{{ $cable->cable_type }}</span></td>
                                    <td>{{ $cable->description }}</td>
                                    <td><small>{{ $cable->created_at ?? '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Записей пока нет</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
