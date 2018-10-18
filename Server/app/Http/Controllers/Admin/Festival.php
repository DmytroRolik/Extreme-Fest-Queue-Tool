<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Option;
use App\Schedule;
use Validator;

class Festival extends Controller
{
    public function index(Request $request){

        if($request->isMethod('post')){

            $this->validateData($request);

            if($this->checkScheduleExist()){
                $this->transferringSchedule($request->input('date-start'), $request->input('date-end'));
            }

            Option::setDateStart($request->input('date-start'));
            Option::setDateEnd($request->input('date-end'));
        }

        $data['dateStart'] = Option::getDateStart();
        $data['dateEnd'] = Option::getDateEnd();

        return view("admin/festival/index", $data);
    }

    public function validateData(Request $request){

        $this->validate($request, [
               'date-start' => 'required|date|date_format:d.m.Y|before_or_equal:date-end',
               'date-end' => 'required|date|date_format:d.m.Y|after_or_equal:date-start',
        ],[], ['date-start' => '"Дата начала"', 'date-end' => '"Дата окончания"']);
    }

    public function checkScheduleExist(){

        $schedules = Schedule::all();
        return boolval(count($schedules));
    }

    public function transferringSchedule($newDateStart, $newDateEnd){

        $daysOld = Option::getDaysArray();
        $daysNew = $this->getDaysArray($newDateStart, $newDateEnd);

        for($i = 0; $i < count($daysNew); $i++){

            // Если в старом расписании мнеьше дней, тогда происходит "обрезание"
            if(!isset($daysOld[$i]))
                break;

            // Получение списка позиций, на текущую дату. Смена даты на новую.
            $schedule = Schedule::where('date', $daysOld[$i]->format('Y-m-d'))->get();

            foreach ($schedule as $item) {

                $item->date = $daysNew[$i]->format('Y-m-d');
                $item->save();
            }

        }// for

        // Удаление расписания, которое не влазит в новые период
        if(count($daysNew) < count($daysOld)){

            $toDeleteCount = count($daysOld) - count($daysNew);

            for($i = 0; $i <= $toDeleteCount - 1; $i++){
                $toDelete = Schedule::where('date', $daysOld[count($daysOld) - 1 - $i]->format('Y-m-d'));
                if($toDelete)
                    $toDelete->delete();
            }
        }
    }

    protected function getDaysArray($startDate, $endDate){

        $dateStartString = $startDate;
        $dateEndString = $endDate;

        $dateStart = date_create_from_format ('d.m.Y', $dateStartString);
        $dateEnd = date_create_from_format ('d.m.Y', $dateEndString);

        while ($dateStart->getTimestamp() <= $dateEnd->getTimestamp()) {

            $result[] = clone $dateStart;
            date_add($dateStart, date_interval_create_from_date_string('1 days'));
        }

        return $result;
    }
}
