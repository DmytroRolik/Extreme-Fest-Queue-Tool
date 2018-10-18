<?php

namespace App\Http\Controllers\Api\v1;

use App\Classes\Queue\QueueErrorCodes;
use App\Option;
use App\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Validator;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function registerNewUser(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:2|max:60',
                'surname' => 'required|string|min:2|max:60',
                'number' => 'required|string|min:1|max:60',
                'passport' => 'required|string|min:2|max:60',
            ]
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        try {
            $newUser = User::create([
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'number' => $request->input('number'),
                'passport' => $request->input('passport'),
                'login' => $request->input('number'),
                'password' => Hash::make($request->input('passport')),
                'role_id' => 3,
                'firebase_token' => ' '
            ]);
        } catch (\Exception $e) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$ALREDY_EXIST);
        }

        if ($newUser) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * @param Request $request
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchedule(Request $request, $date = '')
    {
        if (!empty($date)) {
            $allSchedule = Schedule::where("date", $date)->get();
        } else {
            $allSchedule = Schedule::all();
        }

        $result = [];
        foreach ($allSchedule as $schedule) {
            $activity = $schedule->activity;
            $photosModels = $activity->photos;

            $photos = [];
            foreach ($photosModels as $photo) {
                //$photos[] = URL::to('/') . "/" . $photo->url;
                $photos[] = $photo->url;
            }

            $result[] = [
                'id' => $schedule->id,
                'date' => $schedule->date,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'name' => $activity->name,
                'description' => $activity->description,
//                'main_photo' => $activity->main_photo_url ? URL::to('/') . "/" . $activity->main_photo_url : null,
                'main_photo' => $activity->main_photo_url ? $activity->main_photo_url : null,
                'photos' => $photos
            ];
        }

        return response()->json($result);
    }

    public function addToken(Request $request)
    {
        $postData = $request->all();
        if(isset($postData['token'])) {
            $user = Auth::user();
            $user->firebase_token = $postData['token'];
            $user->save();
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDates(Request $request)
    {
        $dates = Option::getDaysArray();

        $result = [];
        foreach ($dates as $date){
            $result[] = $date->format('Y-m-d');
        }

        return response()->json([
            'success' => true,
            'dates' => $result
        ]);
    }
}
