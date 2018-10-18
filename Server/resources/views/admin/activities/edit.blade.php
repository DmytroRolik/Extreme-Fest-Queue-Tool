@extends('adminlte::page')

@section('content_header')
    <h1 class="admin-page__header">{{ $activiti->name }}</h1>
    <h5>Редактирование</h5>
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
    
        <form method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-block clearfix">
                <div c>
                    <div class="col-md-12" style="padding-bottom: 10px">
                        <div>
                            <div>
                                <label>Фотография:</label>
                            </div>
                            <div class="photo-place enabled {{ $activiti->main_photo_url ? 'photo-place-filled' : ''}}" data-photo-name="photo-main">
                                <div class="photo-holder">
                                    <i class="fa fa-plus" style="{{ $activiti->main_photo_url ? 'display:none' : ''}}"></i>
                                    <i class="fa fa-trash"></i>
                                    @if($activiti->main_photo_url)
                                        <img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='{{URL::to('/').'/'.$activiti->main_photo_url}}'>
                                    @endif
                                </div>
                            </div>
                            <input id="photo-main" name="photo-main" accept="image/jpeg"
                                   class="input-file-hidden input-photo" type="file">
                            <input id="photo-main-deleted" name="photo-main-deleted" type="hidden">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label>Название:</label>
                        <input placeholder="Введите название активности" name="name" value="{{$activiti->name}}"
                               type="text" class="form-control form-control-admin" required @cannot('activities-edit') readonly @endcan>
                    </div>

                    <div class="col-md-12">
                        <label>Описание:</label>
                        <textarea placeholder="Введите общее описание для пользователей" rows="5"
                                  style="resize: vertical" name="description" class="form-control form-control-admin" @cannot('activities-edit') readonly @endcan>{{$activiti->description}}</textarea>
                    </div>

                    {{--<div class="form-check col-sm-12" style="padding-top: 10px">--}}
                        {{--<input id="queue" class="form-check-input" style="position: relative; top:2px" type="checkbox" name="chb-queue" {{ $activiti->queue ? "checked" : "" }} @cannot('activities-edit') onclick="return false;" @endcan>--}}
                        {{--<label class="form-check-label" for="queue">--}}
                            {{--Электронная очередь--}}
                        {{--</label>--}}
                    {{--</div>--}}

                    {{--<div class="form-check col-sm-12" style="padding-top: 10px; {{$activiti->queue ? "" : "display:none;"}}">--}}
                        {{--<label>Буфер очереди:</label>--}}
                        {{--<input placeholder="Максимальное количество людей в очереди" name="queue-buffer" value="{{$activiti->queue_buffer}}"--}}
                               {{--type="number" class="form-control form-control-admin">--}}
                    {{--</div>--}}
                </div>
            </div>

            <div class="form-block clearfix" style="margin: 10px 0">
                <div class="col-md-12">
                    <label>Дополнительные фотографии:</label>

                    <div class="photo-container">

                        <?php $path_photo_0 =  isset($additiona_photos[0]->url) ? $additiona_photos[0]->url : '' ?>
                        <div class="photo-place enabled {{ $path_photo_0 ? 'photo-place-filled' : ''}}" data-photo-name="additional-photo1">
                            <div class="photo-holder">
                                <i class="fa fa-plus" style="{{ $path_photo_0 ? 'display:none' : ''}}"></i>
                                <i class="fa fa-trash"></i>
                                @if($path_photo_0)
                                    <img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='{{$path_photo_0 ? URL::to('/').'/'.$path_photo_0 : ''}}'>
                                @endif
                            </div>
                        </div>
                        <input id="additional-photo1" name="additional-photo[{{$path_photo_0}}]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo1-deleted" name="additional-photo-deleted[{{$path_photo_0}}]" type="hidden">

                        <?php $path_photo_1 =  isset($additiona_photos[1]->url) ? $additiona_photos[1]->url : '' ?>
                        <div class="photo-place enabled {{ $path_photo_1 ? 'photo-place-filled' : ''}}" data-photo-name="additional-photo2">
                            <div class="photo-holder">
                                <i class="fa fa-plus" style="{{ $path_photo_1 ? 'display:none' : ''}}"></i>
                                <i class="fa fa-trash"></i>
                                @if($path_photo_1)
                                    <img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='{{ $path_photo_1 ? URL::to('/').'/'.$path_photo_1 : ''}}'>
                                @endif
                            </div>
                        </div>
                        <input id="additional-photo2" name="additional-photo[{{$path_photo_1}}]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo2-deleted" name="additional-photo-deleted[{{$path_photo_1}}]" type="hidden">

                        <?php $path_photo_2 =  isset($additiona_photos[2]->url) ? $additiona_photos[2]->url : '' ?>
                        <div class="photo-place enabled {{ $path_photo_2 ? 'photo-place-filled' : ''}}" data-photo-name="additional-photo3">
                            <div class="photo-holder">
                                <i class="fa fa-plus" style="{{ $path_photo_2 ? 'display:none' : ''}}"></i>
                                <i class="fa fa-trash"></i>
                                @if($path_photo_2)
                                    <img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='{{$path_photo_2 ? URL::to('/').'/'.$path_photo_2 : ''}}'>
                                @endif
                            </div>
                        </div>
                        <input id="additional-photo3" name="additional-photo[{{$path_photo_2}}]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo3-deleted" name="additional-photo-deleted[{{$path_photo_2}}]" type="hidden">

                        <?php $path_photo_3 =  isset($additiona_photos[3]->url) ? $additiona_photos[3]->url : '' ?>
                        <div class="photo-place enabled {{ $path_photo_3 ? 'photo-place-filled' : ''}}" data-photo-name="additional-photo4">
                            <div class="photo-holder">
                                <i class="fa fa-plus" style="{{ $path_photo_3 ? 'display:none' : ''}}"></i>
                                <i class="fa fa-trash"></i>
                                @if($path_photo_3)
                                    <img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='{{$path_photo_3 ? URL::to('/').'/'.$path_photo_3 : ''}}'>
                                @endif
                            </div>
                        </div>
                        <input id="additional-photo4" name="additional-photo[{{$path_photo_3}}]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo4-deleted" name="additional-photo-deleted[{{$path_photo_3}}]" type="hidden">

                        <?php $path_photo_4 =  isset($additiona_photos[4]->url) ? $additiona_photos[4]->url : '' ?>
                        <div class="photo-place enabled {{ $path_photo_4 ? 'photo-place-filled' : ''}}" data-photo-name="additional-photo5">
                            <div class="photo-holder">
                                <i class="fa fa-plus" style="{{ $path_photo_4 ? 'display:none' : ''}}"></i>
                                <i class="fa fa-trash"></i>
                                @if($path_photo_4)
                                    <img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='{{$path_photo_4 ? URL::to('/').'/'.$path_photo_4 : ''}}'>
                                @endif
                            </div>
                        </div>
                        <input id="additional-photo5" name="additional-photo[{{$path_photo_4}}]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo5-deleted" name="additional-photo-deleted[{{$path_photo_4}}]" type="hidden">

                        <?php $path_photo_5 =  isset($additiona_photos[5]->url) ? $additiona_photos[5]->url : '' ?>
                        <div class="photo-place enabled {{ $path_photo_5 ? 'photo-place-filled' : ''}}" data-photo-name="additional-photo6">
                            <div class="photo-holder">
                                <i class="fa fa-plus" style="{{ $path_photo_5 ? 'display:none' : ''}}"></i>
                                <i class="fa fa-trash"></i>
                                @if($path_photo_5)
                                    <img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='{{$path_photo_5 ? URL::to('/').'/'.$path_photo_5 : ''}}'>
                                @endif
                            </div>
                        </div>
                        <input id="additional-photo6" name="additional-photo[{{$path_photo_5}}]" accept="image/jpeg"
                               class="input-file-hidden input-photo" type="file">
                        <input id="additional-photo6-deleted" name="additional-photo-deleted[{{$path_photo_5}}]" type="hidden">

                    </div>

                </div>
            </div>

            @can('activities-edit')
                <div style="text-align: right">
                    <input type="submit" class="btn btn-success" value="Сохранить">
                </div>
            @endcan
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