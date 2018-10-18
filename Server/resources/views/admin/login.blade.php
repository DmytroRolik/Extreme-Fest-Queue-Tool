@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/auth.css') }}">
    @yield('css')

    <style>
        body{
            background-color: rgba(0,0,0,0.0) !important;
        }

    </style>
@stop

@section('body_class', 'login-page')

@section('body')
    <div class="login-box">

        <!-- /.login-logo -->
        <div class="login-box-body" style="box-shadow: 2px 2px 10px rgba(0,0,0,0.5)">
            <div class="login-logo">
                <a href="{{ url(config('adminlte.dashboard_url', 'home')) }}">
                    <img width="150px" src={{asset('images/main/logo.png')}} alt="Logo">
                </a>
            </div>
            <p class="login-box-msg">Войдите, чтобы начать</p>
            <form method="POST" action="{{ route('admin-login') }}" >
                {!! csrf_field() !!}

                <div class="form-group has-feedback {{ $errors->has('login') ? 'has-error' : '' }}">
                    <input type="text" name="login" class="form-control" value="{{ old('login') }}"
                           placeholder="Имя пользователя">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('login') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                    <input type="password" name="password" class="form-control"
                           placeholder="Пароль">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> Запомнить меня
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit"
                                class="btn btn-primary btn-block btn-flat">Войти</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <div class="auth-links">
                <a href="{{ url(config('adminlte.password_reset_url', 'password/reset')) }}"
                   class="text-center">Забыли пароль?</a>
                <br>
            </div>
        </div>
        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    @yield('js')
@stop
