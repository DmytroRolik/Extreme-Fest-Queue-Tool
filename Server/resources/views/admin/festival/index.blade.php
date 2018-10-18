@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header"><i class="fa fa-info-circle" aria-hidden="true"> </i> Настройки</h1>
    <h5>Настройки фестиваля</h5>
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

    <form method="post">
        @csrf
        <div class="form-block clearfix" style="margin-bottom: 20px">
            <div class="container-fluid">
                <div class="col-sm-12">
                    <h5><strong><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Название и описание:</strong></h5>
                </div>

                <div class="col-sm-12">
                    <div class="admin-input-group">
                        <p>Название: </p>
                        <input class="form-control" type="text" value="Донбасс Экстрим Фест" placeholder="Введите название">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="admin-input-group">
                        <p>Введите описание:</p>
                        <textarea name="" id="" rows="5" class="form-control form-control-admin" style="resize: vertical" placeholder="Введите описание"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-block clearfix">
            <div class="container-fluid">
                <div class="col-sm-12">
                    <h5><strong><i class="fa fa-calendar-o" aria-hidden="true"></i> Выберите дату начала и окончания фестиваля:</strong></h5>
                </div>

                <div class="col-sm-6">
                    <div class="admin-input-group">
                        <p>Дата начала: </p>
                        <input class="form-control" type="text" id="date-start" name="date-start" value="{{ old('date-start') ? old('date-start') :  $dateStart }}">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-input-group">
                        <p>Дата окончания:</p>
                        <input  class="form-control" type="text" id="date-end" name="date-end" value="{{ old('date-end') ? old('date-end') : $dateEnd }}">
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: right">
            <div class="admin-input-group">
                <input type="submit" class="btn btn-success" value="Сохранить">
            </div>
        </div>
    </form>
@stop

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>


    <script>
        $(document).ready(function() {

            $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
            $( "#date-start" ).datepicker();
            $( "#date-end" ).datepicker();
        });

        // Руссификация календаря
        ( function( factory ) {
            if ( typeof define === "function" && define.amd ) {

                // AMD. Register as an anonymous module.
                define( [ "../widgets/datepicker" ], factory );
            } else {

                // Browser globals
                factory( jQuery.datepicker );
            }
        }( function( datepicker ) {

            datepicker.regional.ru = {
                closeText: "Закрыть",
                prevText: "&#x3C;Пред",
                nextText: "След&#x3E;",
                currentText: "Сегодня",
                monthNames: [ "Январь","Февраль","Март","Апрель","Май","Июнь",
                    "Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь" ],
                monthNamesShort: [ "Янв","Фев","Мар","Апр","Май","Июн",
                    "Июл","Авг","Сен","Окт","Ноя","Дек" ],
                dayNames: [ "воскресенье","понедельник","вторник","среда","четверг","пятница","суббота" ],
                dayNamesShort: [ "вск","пнд","втр","срд","чтв","птн","сбт" ],
                dayNamesMin: [ "Вс","Пн","Вт","Ср","Чт","Пт","Сб" ],
                weekHeader: "Нед",
                dateFormat: "dd.mm.yy",
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: "" };
            datepicker.setDefaults( datepicker.regional.ru );

            return datepicker.regional.ru;

        } ) );
    </script>
@stop