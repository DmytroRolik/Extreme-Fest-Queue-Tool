<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Activity;
use App\Activities_photo;
use App\Schedule;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class Activities extends Controller
{
    protected $message_add_success = "<strong>Новая активность</strong> успешно добавлена";
    protected $message_edit_success = "<strong>Активность</strong> успешно сохранена";

    public function index(Request $request){

        $orderBy = $request->input('sort') ? $request->input('sort') : 'name';
        $direction = $request->input('dir')  ? $request->input('dir') : 'desc';

        $all_activities = Activity::orderBy($orderBy, $direction)
            ->paginate(10);

        $data['all_activities'] = $all_activities;
        $data['dir'] = $direction;
        $data['order'] = $orderBy;

        if($request->message_success)
            $data['message_success'] = $request->message_success;
        if($request->message_error)
            $data['message_error'] = $request->message_error;

        return view("admin/activities/list", $data);
    }

    public function getActivityAjax(Request $request){

        $entities = Activity::where('id', ">", "0");

        return DataTables::of($entities)
            ->addColumn('main_photo_url', function ($activity){

                if($activity->main_photo_url){
                    $photoUrl = URL::to('/').'/'.$activity->main_photo_url;
                }else{
                    $photoUrl = URL::to('/')."/images/main/activiti_default.jpg";
                }

                return "<div style='text-align: center'><img height='50' src='". $photoUrl ."'/></div>";
            })
            ->addColumn('action', function ($activity){
                $view = View::make('admin.activities.button_action',
                    [
                        "id" => $activity->id
                    ]
                );
                return $view->render();
            })
            ->rawColumns(['main_photo_url', 'action'])
            ->make(true);
    }

    public function add(Request $request){

        if($request->isMethod('post')) {
            $this->validateData($request);
            $activiti = Activity::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                //'queue' => $request->has('chb-queue'),
                //'queue_buffer' => $request->input('queue-buffer'),
            ]);

            $new_activiti_id = $activiti->id;

            // Основное фото
            if($request->file('photo-main')) {
                $path = $request->file('photo-main')->move("images/app/activities", "main_" . $new_activiti_id . ".jpg");
                $activiti->main_photo_url = $path;
                $activiti->save();
            }

            // Дополнительные фотографии
            if($request->has('additional-photo')){
                $additional_photos_upload = $request->file('additional-photo');

                foreach($additional_photos_upload as $file){

                    $last_id = Activities_photo::orderBy('id', 'desc')->first();
                    $last_id = $last_id ? $last_id->id : 0;

                    $path = $file->move("images/app/activities", $new_activiti_id."_additional".$last_id.".jpg");

                    $new_photo = new Activities_photo();
                    $new_photo->url = $path;
                    $new_photo->activiti_id = $new_activiti_id;
                    $new_photo->save();
                }
            }

            return redirect()->route('admin_activities')->withSuccess($this->message_add_success);
        }

        return view("admin/activities/add");
    }

    public function edit(Request $request, $id){

        if($request->isMethod('post')) {
            $this->validateData($request);

            $activiti = Activity::find($id);

            // Активность поддерживает элетктронную очередь
//            if($request->input('chb-queue')){
//                $activiti->queue = true;
//            }else{
//                $activiti->queue = false;
//            }

            // Если в запросе есть файл, сохраняем его в БД и на жесткий диск
            if( $request->hasFile('photo-main') ) {

                $path = $request->file('photo-main')->move("images/app/activities", "main_".$id.".jpg");
                $activiti->main_photo_url = $path;

            }
            elseif($request->input('photo-main-deleted')){

                $this->deleteFile($activiti->main_photo_url);
                $activiti->main_photo_url = '';
            }

            // Если есть дополнительные фотографии
            if($request->has('additional-photo')){
                $additional_photos_upload = $request->file('additional-photo');
                $additional_photos_activity = $activiti->photos;

                foreach($additional_photos_upload as $old_path => $file){

                    // Если файл с таким же именем уже существует
                    if(file_exists($old_path)){

                        $this->deleteFile($old_path);
                        $file_name = pathinfo($old_path)['filename'];
                        $path = $file->move("images/app/activities/", $file_name.".jpg");
                    }else{
                        $last_id = Activities_photo::orderBy('id', 'desc')->first();
                        $last_id = $last_id ? $last_id->id : 0;

                        $path = $file->move("images/app/activities", $id."_additional".$last_id.".jpg");

                        $new_photo = new Activities_photo();
                        $new_photo->url = $path;
                        $new_photo->activity_id = $id;
                        $new_photo->save();
                    }
                }
            }

            // Удаление дополнительных фотографий
            if($request->has('additional-photo-deleted')){

                foreach($request->input('additional-photo-deleted') as $path => $isDeleted){
                    if($isDeleted){

                        $this->deleteFile($path);
                        $photo = Activities_photo::where('url', $path);

                        if($photo)
                            $photo->delete();
                    }
                }// foreach
            }

            $activiti->name = $request->input('name');
            $activiti->description = $request->input('description');
            //$activiti->queue_buffer =  $request->input('queue-buffer');
            $activiti->save();

            return redirect(route("admin_activities"))->withSuccess($this->message_edit_success);
        }

        $activiti = Activity::find($id);
        $additiona_photos = $activiti->photos;

        $data['activiti'] = $activiti;
        $data['additiona_photos'] = $additiona_photos;

        return view("admin/activities/edit", $data);
    }

    public function delete(Request $request){

        if($request->input('for-delete')){

            $delete_ids = $request->input('for-delete');
            foreach ($delete_ids as $delete_id) {

                if($this->canDelete($delete_id)) {

                    $additional_photos = Activities_photo::where('activity_id', $delete_id)->get();

                    foreach ($additional_photos as $photo) {
                        $this->deleteFile($photo);
                        $photo->delete();
                    }

                    Activity::find($delete_id)->delete();
                }else{
                    $errors[] = Activity::find($delete_id)->name;
                }
            }// foreach
        }

        if(isset($errors)){

            $error_message = "<strong>Не удалось удалить</strong> ". (count($errors) > 1 ? "активности " : "активность ");

            $index = 0;
            foreach($errors as $error){
                $error_message .= "'".$error."'".($index++ >= count($errors) ? ' ' : ', ');
            }

            $error_message .= "так как ". (count($errors) > 1 ? "они уже добавлены" : "она уже добавлена") . " в расписание.";
            return redirect()->route("admin_activities")->withErrors($error_message);
        }

        return redirect()->route("admin_activities")->with("message_success", "успешно удалена");
    }

    public function validateData(Request $request){

        $this->validate($request, [
            'name' => 'required|min:2'
        ],[], ['name' => '"Название"']);
    }

    public function deleteFile($path){
        if(file_exists($path))
            unlink($path);
    }

    // Возвращает true, если ссылка на активность отсутствует в расписании, и активность может быть удалена
    public function canDelete($activity_id){
        return !boolval(count(Schedule::where('activity_id', $activity_id)->get()));
    }

}
