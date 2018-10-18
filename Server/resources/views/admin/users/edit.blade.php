@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header">Новый пользователь</h1>
    <h5>Введите данные для регистрации нового пользователя</h5>
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
                       type="text" class="form-control form-control-admin" value="{{old('name') != null ? old('name') : $user->name}}" @cannot('users-edit') readonly @endcan>
            </div>
            <div class="col-md-12" style="padding-top: 5px;">
                <label>Фамилия:</label>
                <input placeholder="Введите фамилию пользователя" name="surname"
                       type="text" class="form-control form-control-admin" value="{{old('surname') != null ? old('surname') : $user->surname}}" @cannot('users-edit') readonly @endcan>
            </div>
        </div>
        <div class="form-block clearfix" style="margin-top: 10px">
            <div class="col-md-12" style="padding-top: 5px;">
                <label>Номер браслета: </label>
                <input placeholder="Введите логин пользователя" name="number" required
                       type="text" class="form-control form-control-admin" value="{{old('number') != null ? old('number') : $user->number}}" @cannot('users-edit') readonly @endcan>
            </div>
            <div class="col-md-12" style="padding-top: 5px;">
                <label>Номер документа: </label>
                <input placeholder="Введите пароль" name="passport" required
                       type="text" class="form-control form-control-admin" value="{{old('password') != null ? old('password') : $user->passport}}" @cannot('users-edit') readonly @endcan>
            </div>
        </div>

        @can('users-edit')
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