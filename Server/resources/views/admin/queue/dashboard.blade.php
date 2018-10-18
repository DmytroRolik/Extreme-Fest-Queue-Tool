@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header"><i class="fa fa-ticket" aria-hidden="true"> </i> Активность «<b>{{$activityName}}</b>»</h1>
    <h5></h5>
    <p>
        Дата: <b>{{$activityDate}}</b><br/>
        Время работы: <b>{{$activityStartTime}} - {{$activityEndTime}}</b>
    </p>
@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
@stop

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><span id="user-count">-</span> людей</h3>

                    <p>В очереди:</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><span id="time">-</span> минут(ы)</h3>

                    <p>Среднее время ожидания:</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <table id="example" class="table table-bordered table-striped dt-responsive dataTable" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Позиция</th>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Номер браслета</th>
                    <th>Паспорт</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>
    <script>
        var usersTable = $("#example").DataTable({
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
            // processing: true,
            serverSide: true,
            responsive: true,
            lengthMenu: [[10, 25, 50, 100, 150], [10, 25, 50, 100, 150]],
            // order: [[ 0, "desc" ]],
            stateSave: true,
            ajax: {{$activityId}} + "/ajax/users/",
            columns: [
                { data: 'position'},
                { data: 'name'},
                { data: 'surname'},
                { data: 'number'},
                { data: 'passport'},
                { data: 'action'}
            ]
        });

        $("body").on("click", ".btn-delete-user", function () {
            var userId = $(this).data('user-id');
            $.ajax({
                type:'POST',
                url: {{$activityId}} + "/ajax/users/" +  userId,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success:function(data){
                    udateAll();
                }
            });
        });

        setInterval(function() {
            udateAll();
        }, 2000);

        function udateAll() {
            usersTable.ajax.reload(null, false);
            updateTimeAndCount();
        }

        function updateTimeAndCount() {
            $.ajax({
                type:'GET',
                url: {{$activityId}} + "/ajax/info",
                success:function(data){
                    $("#user-count").text(data['length']);
                    console.log(data['averageTime']);

                    if(data['averageTime'] > 0){
                        $mins = Math.ceil(data['averageTime']/60);
                        $("#time").text($mins);
                    }
                }
            });
        }

    </script>

@stop