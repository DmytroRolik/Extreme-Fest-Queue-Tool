@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header">Новая активность</h1>
    <h5>Введите данные для новой активности</h5>
@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
@stop

@section('content')
    
        <form method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-block clearfix">
                <div class="">
                    <div class="col-md-12" style="padding-bottom: 10px">
                        <div>
                            <div>
                                <label>Фотография:</label>
                            </div>
                            <div class="photo-place enabled" data-photo-name="photo-main">
                                <div class="photo-holder">
                                    <i class="fa fa-plus"></i>
                                    <i class="fa fa-trash"></i>
                                </div>
                            </div>
                            <input id="photo-main" name="photo-main" accept="image/jpeg"
                                   class="input-file-hidden input-photo" type="file">
                            <input id="photo-main-deleted" name="photo-main-deleted" type="hidden">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label>Название:</label>
                        <input placeholder="Введите название активности" name="name"
                               type="text" class="form-control form-control-admin" required>
                    </div>

                    <div class="col-md-12">
                        <label>Описание:</label>
                        <textarea placeholder="Введите общее описание для пользователей" rows="5"
                                  style="resize: vertical" name="description" class="form-control form-control-admin"></textarea>
                    </div>

                    {{--<div class="form-check col-sm-12" style="padding-top: 10px">--}}
                        {{--<input id="queue" class="form-check-input" style="position: relative; top:2px" type="checkbox" name="chb-queue">--}}
                        {{--<label class="form-check-label" for="queue">--}}
                            {{--Электронная очередь--}}
                        {{--</label>--}}
                    {{--</div>--}}

                    {{--<div class="form-check col-sm-12" style="padding-top: 10px; display: none">--}}
                        {{--<label>Буфер очереди:</label>--}}
                        {{--<input placeholder="Максимальное количество людей в очереди" name="queue-buffer"--}}
                               {{--type="number" class="form-control form-control-admin">--}}
                    {{--</div>--}}
                </div>
            </div>

            <div class="form-block clearfix" style="margin: 10px 0">
                <div class="col-md-12">
                    <label>Дополнительные фотографии:</label>

                    <div class="photo-container">

                        <div class="photo-place enabled" data-photo-name="additional-photo1">
                            <div class="photo-holder">
                                <i class="fa fa-plus"></i>
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                        <input id="additional-photo1" name="additional-photo[]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo1-deleted" name="additional-photo-deleted[]" type="hidden">

                        <div class="photo-place enabled" data-photo-name="additional-photo2">
                            <div class="photo-holder">
                                <i class="fa fa-plus"></i>
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                        <input id="additional-photo2" name="additional-photo[]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo1-deleted" name="additional-photo-deleted[]" type="hidden">

                        <div class="photo-place enabled" data-photo-name="additional-photo3">
                            <div class="photo-holder">
                                <i class="fa fa-plus"></i>
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                        <input id="additional-photo3" name="additional-photo[]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo1-deleted" name="additional-photo-deleted[]" type="hidden">

                        <div class="photo-place enabled" data-photo-name="additional-photo4">
                            <div class="photo-holder">
                                <i class="fa fa-plus"></i>
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                        <input id="additional-photo4" name="additional-photo[]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo1-deleted" name="additional-photo-deleted[]" type="hidden">

                        <div class="photo-place enabled" data-photo-name="additional-photo5">
                            <div class="photo-holder">
                                <i class="fa fa-plus"></i>
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                        <input id="additional-photo5" name="additional-photo[]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo1-deleted" name="additional-photo-deleted[]" type="hidden">

                        <div class="photo-place enabled" data-photo-name="additional-photo6">
                            <div class="photo-holder">
                                <i class="fa fa-plus"></i>
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                        <input id="additional-photo6" name="additional-photo[]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo1-deleted" name="additional-photo-deleted[]" type="hidden">

                    </div>

                </div>
            </div>

            <div style="text-align: right">
                <input type="submit" class="btn btn-success" value="Сохранить">
            </div>
        </form>


@stop

@section('js')
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>

    <script>
        $(document).ready(function () {

            $('input[name=chb-queue]').on('change', function () {

                if($('input[name=chb-queue]').is(':checked')){
                    $('input[name=queue-buffer]').parent().show();
                }else{
                    $('input[name=queue-buffer]').parent().hide();
                }
            });
        });
    </script>
@stop