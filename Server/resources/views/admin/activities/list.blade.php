@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header"><i class="fa fa-futbol-o" aria-hidden="true"> </i> Активности</h1>
    <h5>Редактирование всех активностей фестиваля</h5>
@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
    <style>
        table.dataTable tbody td {
            vertical-align: middle;
        }
    </style>
@stop

@section('content')

    @if(session()->has('message_success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <b>Активность</b> {{ session()->get('message_success') }}
        </div>
    @endif

    @if(isset($message_success))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {!! $message_success !!}
        </div>
    @endif

    @if(isset($message_error))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {!! $message_error !!}
        </div>
    @endif

    @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {!! session('success') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @can('activities-edit')
        <div class="table-buttons">
            <a data-toggle="tooltip" title="Добавить новую" href="{{route('admin_activities_add')}}" class="btn btn-success">
                <i class="fa fa-plus" aria-hidden="true"></i> Добавить
            </a>
        </div>
    @endcan

    {{--<form id="activities-form" method="post" action="{{route('admin_activities_delete')}}">--}}
    {{--{{ csrf_field() }}--}}
    {{--<table id="example" class="table datatable-admin" style="width:100%">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th width="50px"></th>--}}
            {{--<th class="hidden-xs" style="width: 100px">Изоображение</th>--}}
            {{--<th><a href="?sort=name&dir={{--}}
                    {{--$order == 'name' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Название--}}
                    {{--@if($order == 'name')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th></th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}

            {{--@if(isset($all_activities))--}}
                {{--@foreach($all_activities as $activiti)--}}
                {{--<tr data-activiti-id="{{$activiti->id}}">--}}
                    {{--<td><input type="checkbox" name="for-delete[]" value="{{$activiti->id}}"></td>--}}
                    {{--<td class="hidden-xs" style="text-align: center; max-width: 100px">--}}
                        {{--<img height="60px" src="{{$activiti->main_photo_url ? URL::to('/').'/'.$activiti->main_photo_url : URL::to('/')."/images/main/activiti_default.jpg"}}">--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{ $activiti->name }}--}}
                    {{--</td>--}}
                    {{--<td>--}}

                        {{--@can('activities-edit')--}}
                            {{--<a data-toggle="tooltip" title="Редактировать" href="{{route('admin_activities_edit', $activiti->id)}}" class="btn btn-success">--}}
                                {{--<i class="fa fa-pencil" aria-hidden="true"></i>--}}
                            {{--</a>--}}
                        {{--@else--}}
                            {{--<a data-toggle="tooltip" title="Просмотреть" href="{{route('admin_activities_view', $activiti->id)}}" class="btn btn-success">--}}
                                {{--<i class="fa fa-eye" aria-hidden="true"></i>--}}
                            {{--</a>--}}
                        {{--@endcan--}}
                    {{--</td>--}}
                {{--</tr>--}}
                {{--@endforeach--}}
            {{--@endif--}}
        {{--</tbody>--}}
    {{--</table>--}}
    {{--</form>--}}

    {{--<div style="text-align: right">--}}
        {{--{{ $all_activities->links() }}--}}
    {{--</div>--}}

    <div class="form-block">
        <div class="box-body">
            <table id="activities" class="table table-bordered table-striped dt-responsive dataTable" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Изоображение</th>
                    <th>Название</th>
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
    <script src="{{ URL::asset('js/app.js') }}"></script>

    <script>
        var activityTable = $("#activities").DataTable({
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
            ajax: "{{route("all_activities")}}",
            fixedColumns:   {
                heightMatch: 'none'
            },
            columns: [
                { data: 'main_photo_url', width: "150px", orderable:false},
                { data: 'name'},
                { data: 'action', width: "150px", orderable:false}
            ]
        });
    </script>
@stop