@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header"><i class="fa fa-ticket" aria-hidden="true"> </i> Очереди</h1>
    <h5>Просмотр и редактирование очередей</h5>
@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
@stop

@section('content')

    @if(isset($message_success))
        <div class="alert alert-success alert-dismissible" role="alert">
            {!! $message_success !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{--<table id="example" class="table datatable-admin" style="width:100%">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th width="50px"></th>--}}
            {{--<th class="hidden-xs"> <a href="?sort=name&dir={{--}}
                    {{--$order == 'name' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Название--}}
                    {{--@if($order == 'name')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th class="hidden-xs"> <a href="?sort=date&dir={{--}}
                    {{--$order == 'date' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Дата--}}
                    {{--@if($order == 'date')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th> <a href="?sort=start_time&dir={{--}}
                    {{--$order == 'start_time' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Время начала--}}
                    {{--@if($order == 'start_time')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th> <a href="?sort=end_time&dir={{--}}
                    {{--$order == 'end_time' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Время конца--}}
                    {{--@if($order == 'end_time')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th></th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}

        {{--@if(isset($allActivities))--}}
            {{--@foreach($allActivities as $activity)--}}
                {{--<tr data-activiti-id="{{$activity->id}}">--}}
                    {{--<td><input type="checkbox" name="for-delete[]" value="{{$activity->id}}"></td>--}}
                    {{--<td class="hidden-xs">--}}
                        {{--{{$activity->name}}--}}
                    {{--</td>--}}
                    {{--<td class="hidden-xs">--}}
                        {{--{{$activity->date}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{substr($activity->start_time, 0, -3)}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{substr($activity->end_time, 0, -3)}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--@can('users-edit')--}}
                            {{--<a data-toggle="tooltip" href="{{route('admin_queue_dashboard', $activity->id)}}" title="Панель управления" class="btn btn-success rules-tooltip">--}}
                                {{--<i class="fa fa-tachometer" aria-hidden="true"></i>--}}
                            {{--</a>--}}
                        {{--@endcan--}}
                    {{--</td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
        {{--@endif--}}
        {{--</tbody>--}}
    {{--</table>--}}

    {{--<div style="text-align: right">--}}
        {{--{{ $allActivities->links() }}--}}
    {{--</div>--}}

    <div class="form-block">
        <div class="box-body">
            <table id="queue" class="table table-bordered table-striped dt-responsive dataTable" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Дата</th>
                    <th>Время начала</th>
                    <th>Время конца</th>
                    <th>Людей в очереди</th>
                    <th>Время ожидания</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

@stop

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    {{--<script src="{{ URL::asset('js/jquery-ui/jquery-ui.min.js') }}"></script>--}}
    <script src="{{ URL::asset('js/app.js') }}"></script>

    <script>
        var queueTable = $("#queue").DataTable({
            paging: true,
            language: {
                "zeroRecords": "Нет записей",
                "info": "Showing page _PAGE_ of _PAGES_",
                "infoEmpty": "Нет доступных записей",
                "infoFiltered": "(отфильтровано из _MAX_ доступных записей)",
                "info": "Показаны с _START_ по _END_ из _TOTAL_ записей",
                "search": "Поиск:",
                "lengthMenu": "Показывать _MENU_ на странице",
                "paginate": {
                    "first":      "Первая",
                    "last":       "Последняя",
                    "next":       "Вперед",
                    "previous":   "Назад"
                },
            },
            processing: true,
            serverSide: true,
            responsive: true,
            lengthMenu: [[10, 25, 50, 100, 150], [10, 25, 50, 100, 150]],
            stateSave: true,
            ajax: "{{ route("admin_get_queues") }}",
            searching: false,
            fixedColumns:   {
                heightMatch: 'none'
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'date', name: 'date'},
                { data: 'start_time', name: 'start_time'},
                { data: 'end_time', name: 'end_time'},
                { data: 'length', name: 'length', orderable:false},
                { data: 'average_time', name: 'average_time', orderable:false},
                { data: 'action', name: 'action'}
            ]
        });
    </script>
@stop