@extends('layouts.librenmsv1')

@section('title', 'Кабельный журнал')

@section('content')
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Журнал кабельных соединений</strong>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Порт A</th>
                            <th>Порт B</th>
                            <th>Тип кабеля</th>
                            <th>Примечания</th>
                        </thead>
                        <tbody>
                            @forelse($entries as $entry)
                                <tr>
                                    <td>{{ $entry->id }}</td>
                                    <td>{{ $entry->port_a }}</td>
                                    <td>{{ $entry->port_b }}</td>
                                    <td>{{ $entry->cable_type }}</td>
                                    <td>{{ $entry->notes }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5">Нет записей в кабельном журнале.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
