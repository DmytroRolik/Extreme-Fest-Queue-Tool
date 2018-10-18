<?php

namespace App\Http\Controllers\Admin;

use App\Activities_photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Option;
use App\Activity;
use App\Schedule;

class Schedules extends Controller{

    public function index(Request $request){

        if($request->isMethod('post')){

            $this->validation($request);

            $schedule = $request->schedule;
            foreach ($schedule as $schedule_date){
                foreach ($schedule_date as $item){

                    if(isset($item['id_deleted']) && !empty($item['id_deleted'])){
                        $this->deleteSchedule($item['schedule_id']);
                    }else if(empty($item['schedule_id'])) {
                        $this->createNewSchedule($item);
                    }else{
                        $this->updateSchedule($item);
                    }
                }
            }// foreach

            return redirect(route('admin_schedule'));
        }

        $data['dates'] = Option::getDaysArray();
        $data['activities'] = Activity::all();
        $data['schedule_list'] = $this->getSchedule();

        return view('admin/schedule/list', $data);
    }

    public function canDelete(Request $request){

        // TODO Сделать проверку на возможность удаления
        return ['can_delete' => true];

//        if($request->schedule_id){
//
//            $for_delete = Schedule::find($request->schedule_id);
//
//            if($for_delete)
//                $for_delete->delete();
//
//
//            return ['is_deleting' => true];
//        }
//
//        return ['is_deleting' => false];
    }

    protected function getSchedule(){

        $schedule_list_raw = Schedule::all();
        $schedule_list_result = array();

        $index = 0;
        foreach ($schedule_list_raw as $schedule){

            $date = date_format(date_create_from_format ('Y-m-d', $schedule['date']), "d-m-Y");

            $schedule_list_result[$date][$index]['schedule_id'] = $schedule->id;
            $schedule_list_result[$date][$index]['activity_id'] = $schedule->activity_id;
            $schedule_list_result[$date][$index]['activity_name'] = Activity::find($schedule->activity_id)->name;
            $schedule_list_result[$date][$index]['start_time'] = substr($schedule->start_time, 0, strlen($schedule->start_time)-3);
            $schedule_list_result[$date][$index]['end_time'] = substr($schedule->end_time, 0, strlen($schedule->end_time)-3);
            $schedule_list_result[$date][$index]['is_working'] = $schedule->is_working;
            $schedule_list_result[$date][$index]['sort_position'] = $schedule->sort_position;
            $schedule_list_result[$date][$index]['queue'] = $schedule->queue;

            usort($schedule_list_result[$date], function($a, $b) {

                return $a["sort_position"] > $b["sort_position"];
            });

            $index++;
        }

        return $schedule_list_result;
    }

    protected function createNewSchedule($item){

        $schedule = new Schedule();
        $schedule->activity_id = $item['activity_id'];
        $schedule->start_time = $item['start_time'];
        $schedule->end_time = $item['end_time'];
        $schedule->date = date_format(date_create_from_format('d-m-Y', $item['date']), "Y-m-d");
        $schedule->is_working = isset($item['is_working']);
        $schedule->sort_position = $item['sort_position'];
        $schedule->queue = isset($item['queue']);
        $schedule->save();
    }

    protected function deleteSchedule($id){

        Schedule::find($id)->delete();
    }

    protected function updateSchedule($item){

        $schedule = Schedule::find($item['schedule_id']);
        $schedule->activity_id = $item['activity_id'];
        $schedule->start_time = $item['start_time'];
        $schedule->end_time = $item['end_time'];
        $schedule->date = date_format(date_create_from_format('d-m-Y', $item['date']), "Y-m-d");
        $schedule->is_working = isset($item['is_working']);
        $schedule->sort_position = $item['sort_position'];
        $schedule->queue = isset($item['queue']);
        $schedule->save();
    }

    protected function validation($request){

        $this->validate($request, [
            'schedule.*.*.start_time' => 'required|before_or_equal:schedule.*.*.end_time',
            'schedule.*.*.end_time' => 'required|after_or_equal:schedule.*.*.start_time',
        ],[], ['schedule.*.*.start_time' => '"Время начала"', 'schedule.*.*.end_time' => '"Времени окончания"']);
    }
}
