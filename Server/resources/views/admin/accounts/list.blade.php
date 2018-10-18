@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header"><i class="fa fa-users" aria-hidden="true"> </i> Аккаунты</h1>
    <h5>Редактирование аккаунтов администраторов</h5>
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
                    <li>{{ $error }}</li>
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

    @can('accounts-edit')
        <div class="table-buttons">
            <a data-toggle="tooltip" title="Добавить новый" href="{{route('admin_accounts_add')}}" class="btn btn-success">
                <i class="fa fa-plus" aria-hidden="true"></i> Добавить
            </a>
        </div>
    @endcan

    {{--<form id="accounts-form" method="post" action="{{route('admin_accounts_delete')}}">--}}
    {{--{{ csrf_field() }}--}}
    {{--<table id="example" class="table datatable-admin" style="width:100%">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th width="50px"></th>--}}
            {{--<th class="hidden-xs"> <a href="?sort=name&dir={{--}}
                    {{--$order == 'name' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Имя--}}
                    {{--@if($order == 'name')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th class="hidden-xs"> <a href="?sort=surname&dir={{--}}
                    {{--$order == 'surname' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Фамилия--}}
                    {{--@if($order == 'surname')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th> <a href="?sort=login&dir={{--}}
                    {{--$order == 'login' ? ($dir == "asc" ? "desc" : "asc") : ''--}}
                {{--}}" style="color: black">Логин--}}
                    {{--@if($order == 'login')--}}
                        {{--<i class="fa fa-sort-{{$dir == "asc" ? "desc" : "asc"}} fa-1x" aria-hidden="true"></i>--}}
                    {{--@endif--}}
                {{--</a></th>--}}
            {{--<th>Права доступа</th>--}}
            {{--<th></th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}

            {{--@if(isset($all_accounts))--}}
                {{--@foreach($all_accounts as $account)--}}
                {{--<tr data-activiti-id="{{$account->id}}">--}}
                    {{--<td><input type="checkbox" name="for-delete[]" value="{{$account->id}}"></td>--}}
                    {{--<td class="hidden-xs">--}}
                        {{--{{$account->name}}--}}
                    {{--</td>--}}
                    {{--<td class="hidden-xs">--}}
                        {{--{{$account->surname}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{ $account->login }}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--<a style="text-decoration: underline; cursor: default" class="rules-tooltip">--}}

                        {{--@if(count($account->permissions()->toArray()) || count($account->activities_permissions()->toArray()))--}}
                            {{--<div class="rules-tooltip-text">--}}

                                {{--@foreach($account->permissions() as $permissions)--}}
                                    {{--{{$permissions->permission_name()}}<br/>--}}
                                {{--@endforeach--}}

                                {{--@foreach($account->activities_permissions() as $permissions)--}}
                                    {{--{{$permissions->activity_name()}}<br/>--}}
                                {{--@endforeach--}}

                            {{--</div>--}}
                        {{--@endif--}}
                            {{--Права--}}
                        {{--</a>--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--@can('accounts-edit')--}}
                            {{--<a data-toggle="tooltip" title="Редактировать" href="{{route('admin_accounts_edit', $account->id)}}" class="btn btn-success rules-tooltip">--}}
                                {{--<i class="fa fa-pencil" aria-hidden="true"></i>--}}
                            {{--</a>--}}
                        {{--@else--}}
                            {{--<a data-toggle="tooltip" title="Просмотреть" href="{{route('admin_accounts_view', $account->id)}}" class="btn btn-success rules-tooltip">--}}
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
            <table id="accounts" class="table table-bordered table-striped dt-responsive dataTable" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Логин</th>
                    <th>Права</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    {{--<div style="text-align: right">--}}
        {{--{{ $all_accounts->links() }}--}}
    {{--</div>--}}

    <script>

    </script>
@stop

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>

    <script>
        var accountsTable = $("#accounts").DataTable({
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
            ajax: "{{ route("admin_accounts_all") }}",
            fixedColumns:   {
                heightMatch: 'none'
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'surname', name: 'surname'},
                { data: 'login', name: 'login'},
                { data: 'permissions', name: 'permissions'},
                { data: 'action', name: 'action', orderable:false, width: "120px"}
            ]
        });
    </script>
@stop