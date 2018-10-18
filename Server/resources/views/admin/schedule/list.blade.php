@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header"><i class="fa fa-calendar-o" aria-hidden="true"> </i> Расписание</h1>
    <h5>Редактирование расписания фестиваля</h5>
@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
@stop

@section('content')

    <!-- Модальное окно -->
    <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Добавить новую позицию в расписание</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="invalid-acitvity-id" class="container-fluid" style="display: none">
                    <div class="alert alert-danger" role="alert">
                        Выбранна некорректная активность
                    </div>
                </div>

                <div id="invalid-acitvity-time" class="container-fluid" style="display: none">
                    <div class="alert alert-danger" role="alert">
                        Время начала должно быть меньше, чем время окончания активности
                    </div>
                </div>

                <div class="modal-body">

                    <div class="admin-input-group">
                        <label>Активность:</label>
                        <input id="new-schedule-activity-name" type="text" list="activities" placeholder="Название активности" class="form-control">
                        <input id="new-schedule-activity-id" type="hidden" list="activities">

                        <datalist id="activities">
                            @foreach($activities as $activity)
                                <option data-id="{{$activity->id}}" value="{{$activity->name}}">
                            @endforeach
                        </datalist>
                    </div>

                    <div class="admin-input-group">
                        <label>Время начала:</label>
                        <input id="new-schedule-activity-start" type="time" class="form-control">
                    </div>

                    <div class="admin-input-group">
                        <label>Время окончания:</label>
                        <input id="new-schedule-activity-end" type="time" class="form-control">
                    </div>

                    <input id="date-to-add" type="hidden">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button id="insert-activity" type="button" class="btn btn-primary">Добавить</button>
                </div>
            </div>
        </div>
    </div>

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
    <ul class="nav nav-tabs nav-tabs-schedule">

        @foreach($dates as $key => $date)

            <li data-date="{{$date->format('d-m-Y')}}" class="{{ $key == 0 ? 'active' : '' }}">
                <a data-toggle="tab" href="#{{$date->format('d-m-Y')}}">
                    {{$date->format('d.m.Y')}}
                </a>
            </li>

        @endforeach
    </ul>

    <div class="tab-content tab-content-schedule">


        @csrf
        @foreach($dates as $key => $date)
            <div id="{{$date->format('d-m-Y')}}" class="tab-pane fade {{ $key == 0? 'in active' : '' }}">
                <div class="form-block" style="box-shadow: 2px 3px 10px rgba(0,0,0,0.3)">
                    <div class="container-fluid">
                        <div class="col-sm-12 col-md-12">
                            <h3>Расписание на {{$date->format('d.m.Y')}}</h3>
                            <table class="table schedule-table">
                                <thead>
                                    <tr>
                                        <th class="col-sm-3"> Активность</th>
                                        <th class="col-sm-2"> Время начала</th>
                                        <th class="col-sm-2"> Время окончания</th>
                                        <th class="col-sm-1" style="text-align: center"> Работает</th>
                                        <th class="col-sm-1" style="text-align: center"> Очередь</th>
                                        <th class="col-sm-1"></th>
                                    </tr>
                                </thead>
                                <tbody class="schedule-table-body">

                                @if(isset($schedule_list[$date->format('d-m-Y')]))
                                    @foreach($schedule_list[$date->format('d-m-Y')] as $index => $schedule_item)
                                        <tr>
                                            <td class="col-sm-3">{{$schedule_item['activity_name']}}</td>
                                            <td class="col-sm-2">
                                                <input  name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][start_time]' class="form-control" type="time" value="{{$schedule_item['start_time']}}" @cannot('schedule-edit') readonly @endcan>
                                            </td>
                                            <td class="col-sm-2">
                                                <input name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][end_time]' class="form-control" type="time" value="{{$schedule_item['end_time']}}" @cannot('schedule-edit') readonly @endcan>
                                            </td>
                                            <td class="col-sm-1"style="text-align: center">
                                                <input name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][is_working]' type="checkbox" {{ $schedule_item['is_working'] ? 'checked' : '' }}  @cannot('schedule-edit') onclick="return false;" @endcan>
                                            </td>
                                            <td class="col-sm-1"style="text-align: center">
                                                <input class="queue-chekbox" data-item="{{$date->format('d-m-Y')}}{{$index}}" name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][queue]' type="checkbox" {{ $schedule_item['queue'] ? 'checked' : '' }}  @cannot('schedule-edit') onclick="return false;" @endcan>
                                            </td>
                                            <td class="col-sm-1">
                                                <input name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][sort_position]' type="hidden" class="sort-position" value="{{$schedule_item['sort_position']}}">

                                                <input name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][activity_id]' type="hidden" value="{{$schedule_item['activity_id']}}">
                                                <input name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][date]' type="hidden" value="{{$date->format('d-m-Y')}}">
                                                <input name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][schedule_id]' type="hidden" value="{{$schedule_item['schedule_id']}}">
                                                <input class="is-deleted" name='schedule[{{$date->format('d-m-Y')}}][{{$index}}][id_deleted]' type="hidden">

                                                @can('schedule-edit')
                                                    <button data-schedule-id="{{$schedule_item['schedule_id']}}" type="button" class="close remove-schedule" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>

                            @can('schedule-edit')
                                <div data-toggle="modal" data-target="#add-modal" class="schedule-add-container add-new-schedule">
                                    <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                                </div>
                            @endcan

                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @can('schedule-edit')
            <div style="text-align: right; padding-top: 30px">
                <input type="submit" class="btn btn-success" value="Сохранить"></input>
            </div>
        @endcan

    </div>
    </form>

@stop

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>

    <script>

        // Добавить позицию в выбранный день
        $('#insert-activity').click(function () {

            var dateStart = new Date("0000-00-00T") +  $('#new-schedule-activity-start').val();
            var dateEnd = new Date("0000-00-00T") +  $('#new-schedule-activity-end').val();

            var dateValidation = dateStart <= dateEnd;

            // Если была выбрана корректная активность
            if($('#new-schedule-activity-id').val() && dateValidation) {

                var date = $("#date-to-add").val();
                var activityName = $("#new-schedule-activity-name").val();
                var activityId = $("#new-schedule-activity-id").val();
                var activityStart = $("#new-schedule-activity-start").val();
                var activityEnd = $("#new-schedule-activity-end").val();
                var timeStamp = getTimestamp();

                var itemInserted = "<tr>\n" +
                    "          <td class='col-sm-3'>" + activityName + "</td>\n" +
                    "          <td class='col-sm-2'><input class=\"form-control\" type=\"time\" name='schedule[" + date + "]["+ timeStamp +"][start_time]' value=" + activityStart + "></td>\n" +
                    "          <td class='col-sm-2'><input class=\"form-control\" type=\"time\" name='schedule[" + date + "]["+ timeStamp + "][end_time]' value=" + activityEnd +"></td>\n" +
                    "          <td class='col-sm-1'style=\"text-align: center\"><input type=\"checkbox\" name='schedule[" + date + "]["+ timeStamp + "][is_working]' checked></td>\n" +
                    "          <td class='col-sm-1'style=\"text-align: center\"><input type=\"checkbox\" name='schedule[" + date + "]["+ timeStamp + "][queue]' data-item='" + date + timeStamp + "' class='queue-chekbox'></td>\n" +
                    "          <td class='col-sm-1'>\n" +
                    "<input type=\"hidden\" class=\"sort-position\" value='" + (parseInt(getLastSortPosition(date)) + 1 ) + "'  name='schedule[" + date + "]["+ timeStamp + "][sort_position]'>" +
                    "<input type=\"hidden\" value='" + activityId + "' name='schedule[" + date + "]["+ timeStamp + "][activity_id]'>" +
                    "<input type=\"hidden\" value='" + date + "' name='schedule[" + date + "]["+ timeStamp + "][date]'>" +
                    "<input type='hidden' name='schedule[" + date + "][" + timeStamp + "][schedule_id]' >" +
                    "              <button type=\"button\" class=\"close remove-schedule\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
                    "                  <span aria-hidden=\"true\">&times;</span>\n" +
                    "              </button>\n" +
                    "          </td>\n" +
                    "      </tr>";

                $('#' + date + " .schedule-table-body").append(itemInserted);
                $("#add-modal").modal('hide');

                //addClickRemoveEventListner();
            }else if(!dateValidation){

                $("#invalid-acitvity-time").show();

            }else{
                $('#invalid-acitvity-id').show();
            }
        });

        // Показать модальное для добавления
        $('.add-new-schedule').click(function () {

            var ActiveDate = $('.nav-tabs-schedule .active').data('date');
            $("#date-to-add").val(ActiveDate);

            $("#new-schedule-activity-name").val('');
            $("#new-schedule-activity-id").val('');
            $("#new-schedule-activity-start").val('');
            $("#new-schedule-activity-end").val('');
            $('#invalid-acitvity-id').hide();
            $('#invalid-acitvity-time').hide();
        });

        @can('schedule-edit')
            $( ".schedule-table-body" ).sortable({
            cursor: "move",
            revert: true,
            placeholder: "sortable-placeholder",
            start: function( event, ui ) {
                //alert();
                var table = $(ui.item).parent();
                $(table).find('tr td:nth-child(2),tr td:nth-child(3),tr td:nth-child(4),tr td:nth-child(5)').each(function () {

                     //$(this).width($(this).width());
                });
            },

            stop: function( event, ui ) {

                var table = $(ui.item).parent();

                var index = 0;
                $(table).find('tr').each(function () {
                   $(this).find('.sort-position').val(index++);
                });

                $(table).find('tr td:nth-child(3),tr td:nth-child(4),tr td:nth-child(5),tr td:nth-child(6)').each(function () {

                    //$(this).show();
                });
            }
        });
        @endcan

        function getLastSortPosition(date){

            var max = -1;

            $("#" + date + " .sort-position").each(function () {
                if($(this).val() > max){
                    max = $(this).val();
                }
            });
            return max;
        }
        
        function getTimestamp() {
            return Math.round(+new Date()/1000);
        }

    </script>

    <script>

        $("#new-schedule-activity-name").on('input', function () {
            var val = this.value;
            if($('#activities').find('option').filter(function(){
                return this.value.toUpperCase() === val.toUpperCase();
            }).length) {
                $('#new-schedule-activity-id').val($("#activities option[value='" + val +"']").data('id'));
            }else{
                $('#new-schedule-activity-id').val('');
            }
        });

    </script>

    <script>

        $('body').on('click', '.remove-schedule', function () {

            // Если запись уже в БД, а не локально
            var button = $(this);
            var row = $(this).parent().parent();
            var isSaved  = row.find('.is-deleted').length;
            var idToDelete = $(this).data('schedule-id');

            if(isSaved){

                $.ajax({
                type: "POST",
                url: '{{route("admin_schedule_can_delete")}}',
                data: {'schedule_id': idToDelete, _token: '{{csrf_token()}}'},

                success: function (result) {

                    if(result['can_delete']){

                        row.hide();
                        row.find('.is-deleted').val('true');

                    }else{
                        alert('Удаление запрещено')
                    }
                },

                dataType: "json"
                });

            }else{
                row.remove();
            }
        });

    </script>

    <script>
        @can('schedule-edit')
        $('body').on('click', '.queue-chekbox', function () {
            var item = $(this).data('item');
            if($(this).prop( "checked" )) {
                $("#" + item).prop('readonly', false);
            }else{
                $("#" + item).prop('readonly', true);
            }
        });
        @endcan
    </script>
@stop