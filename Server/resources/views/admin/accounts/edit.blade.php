@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header">Новый аккаунт</h1>
    <h5>Введите данные для нового аккаунта</h5>
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

    <form method="post" style="max-width: 1700px">
        @csrf

        <div class="form-block clearfix">
            <div class="col-md-12">
                <label>Имя:</label>
                <input placeholder="Введите имя пользователя" name="name"
                       type="text" class="form-control form-control-admin" value="{{old('name') != null ? old('name') : $account->name}}" @cannot('accounts-edit') readonly @endcan>
            </div>
            <div class="col-md-12" style="padding-top: 5px;">
                <label>Фамилия:</label>
                <input placeholder="Введите фамилию пользователя" name="surname"
                       type="text" class="form-control form-control-admin" value="{{old('surname') != null ? old('surname') : $account->surname}}" @cannot('accounts-edit') readonly @endcan>
            </div>
        </div>
        <div class="form-block clearfix" style="margin-top: 10px">
            <div class="col-md-12" style="padding-top: 5px;">
                <label>Логин:</label>
                <input placeholder="Введите логин пользователя" name="login"
                       type="text" class="form-control form-control-admin" value="{{old('login') != null ? old('login') : $account->login}}" @cannot('accounts-edit') readonly @endcan>
            </div>

            @can('accounts-edit')
                <div class="col-md-12" style="padding-top: 5px;">
                    <label>Пароль:</label>
                    <input placeholder="Введите новый пароль" name="new_password"
                           type="password" class="form-control form-control-admin" value="{{old('new_password')}}">
                    <input placeholder="Введите новый пароль" name="password"
                           type="hidden" class="form-control form-control-admin" value="{{$account->password}}">
                </div>
            @endcan
        </div>

        <div class="form-block clearfix" style="margin-top: 10px">

            <div class="col-sm-6">
                <label>Права сервиса:</label>

                @foreach($all_permission as $permission)
                    <div>
                        <label style="font-weight: normal">
                            <input name="service_permissions[]" type="checkbox" value="{{$permission->id}}" @if(in_array($permission->id, (old('service_permissions') != null ? old('service_permissions') : $service_permission))) checked @endif
                            @cannot('accounts-edit') onclick="return false;" @endcan>
                            {{$permission->name}}
                        </label>
                    </div>
                @endforeach

            </div>

            <div class="col-sm-6" style="">
                <label>Права на очередь:</label>

                @foreach($schedule_list as $date => $items)

                    <div>
                        <p style="border-bottom: 1px solid rgba(0,0,0,0.2); max-width: 300px">{{$date}}</p>

                        @foreach($items as $item)

                            <div>
                                <label style="font-weight: normal">
                                    <input name="activities_permissions[]" type="checkbox" value="{{$item->id}}" @if(in_array($item->id, (old('activities_permissions') != null ? old('activities_permissions') : $activities_permission) )) checked @endif
                                    @cannot('accounts-edit') onclick="return false;" @endcan>
                                    {{$item->activity->name}} ({{$item->start_time." - $item->end_time"}})
                                </label>
                            </div>

                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        @can('accounts-edit')
            <div style="text-align: right; padding-top: 20px">
                <input type="submit" class="btn btn-success" value="Сохранить">
            </div>
        @endcan
    </form>

@stop

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>
@stop