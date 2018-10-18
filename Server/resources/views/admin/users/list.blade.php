@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header"><i class="fa fa-user-o" aria-hidden="true"></i> Пользователи</h1>
    <h5>Редактирование пользователей сервиса</h5>
@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
@stop

@section('content')

    @if(isset($error_message))
        <div class="alert alert-success alert-dismissible" role="alert">
            {!! $message_success !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(isset($message_error))
        <div class="alert alert-error alert-dismissible" role="alert">
            {!! $message_error !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {!!  session()->get('success') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @can('users-edit')
        <div class="table-buttons">
            <a data-toggle="tooltip" title="Добавить нового" href="{{route('admin_users_add')}}" class="btn btn-success">
                <i class="fa fa-plus" aria-hidden="true"></i> Добавить
            </a>
            {{--<button data-toggle="tooltip" title="Удалить выбранные" class="btn btn-danger" onclick="document.getElementById('accounts-form').submit();">--}}
                {{--<i class="fa fa-trash" aria-hidden="true"></i>--}}
            {{--</button>--}}
        </div>
    @endcan

    {{--<form id="accounts-form" method="post" action="{{route('admin_users_delete')}}">--}}
    {{--{{ csrf_field() }}--}}
    {{--<table id="example" class="table datatable-admin" style="width:100%">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th width="50px"></th>--}}
            {{--<th class="hidden-xs">--}}
                {{--<a href="?sort=name&dir={{--}}
                    {{--$order == 'name' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Имя--}}
                    {{--@if($order == 'name')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "asc" : "desc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a>--}}
            {{--</th>--}}
            {{--<th class="hidden-xs"><a href="?sort=surname&dir={{--}}
                    {{--$order == 'surname' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Фамилия--}}
                    {{--@if($order == 'surname')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th><a href="?sort=login&dir={{--}}
                    {{--$order == 'login' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Логин--}}
                    {{--@if($order == 'login')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th><a href="?sort=number&dir={{--}}
                    {{--$order == 'number' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Номер браслета--}}
                    {{--@if($order == 'number')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th><a href="?sort=passport&dir={{--}}
                    {{--$order == 'passport' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Номер браслета--}}
                    {{--@if($order == 'passport')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th></th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}

            {{--@if(isset($all_users))--}}
                {{--@foreach($all_users as $user)--}}
                {{--<tr data-activiti-id="{{$user->id}}">--}}
                    {{--<td><input type="checkbox" name="for-delete[]" value="{{$user->id}}"></td>--}}
                    {{--<td class="hidden-xs">--}}
                        {{--{{$user->name}}--}}
                    {{--</td>--}}
                    {{--<td class="hidden-xs">--}}
                        {{--{{$user->surname}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{$user->login }}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{$user->number }}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{$user->passport }}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--@can('users-edit')--}}
                            {{--<a data-toggle="tooltip" title="Редактировать" href="{{route('admin_users_edit', $user->id)}}" class="btn btn-success rules-tooltip">--}}
                                {{--<i class="fa fa-pencil" aria-hidden="true"></i>--}}
                            {{--</a>--}}
                        {{--@else--}}
                            {{--<a data-toggle="tooltip" title="Просмотреть" href="{{route('admin_users_view', $user->id)}}" class="btn btn-success rules-tooltip">--}}
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

    <div class="form-block">
        <div class="box-body">
            <table id="users" class="table table-bordered table-striped dt-responsive dataTable" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Логин</th>
                    <th>Номер браслета</th>
                    <th>Номер паспорта</th>
                    <th>Действие</th>

                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    {{--<div style="text-align: right">--}}
        {{--{{ $all_users->links() }}--}}
    {{--</div>--}}

    <script>

    </script>
@stop

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>

    <script>
        var userTable = $("#users").DataTable({
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
            ajax: "{{ route("admin_users_all") }}",
            fixedColumns:   {
                heightMatch: 'none'
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'surname', name: 'surname'},
                { data: 'login', name: 'login'},
                { data: 'number', name: 'number'},
                { data: 'passport', name: 'passport'},
                { data: 'action', name: 'action'}
            ]
        });
    </script>
@stop