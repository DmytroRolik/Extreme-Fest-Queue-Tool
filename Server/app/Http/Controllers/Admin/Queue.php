<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Queue\QueueManager;
use App\Classes\Queue\QueueSQL;
use App\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class Queue extends Controller
{
    public function index(Request $request)
    {
        $orderBy = $request->input('sort') ? $request->input('sort') : 'date';
        $direction = $request->input('dir') ? $request->input('dir') : 'asc';

        $allActivities = DB::table('schedule')
            ->join('activities', 'schedule.activity_id', '=', 'activities.id')
            ->select(
                'schedule.id',
                'schedule.date',
                'schedule.start_time',
                'schedule.end_time',
                'activities.name'
            )
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        $data['allActivities'] = $allActivities;
        $data['order'] = $orderBy;
        $data['dir'] = $direction;

        return view('admin/queue/list', $data);
    }

    public function getQueueDashboard(Request $request, $activityId)
    {
        $scheduleItem = Schedule::find($activityId);
        $data['activityName'] = $scheduleItem->activity->name;
        $data['activityDate'] = $scheduleItem->date;
        $data['activityStartTime'] = substr($scheduleItem->start_time, 0, -3);
        $data['activityEndTime'] = substr($scheduleItem->end_time, 0, -3);

        $queueInfo = $scheduleItem->getInfo();
        $data['peopleCount'] = $queueInfo['length'];
        $data['averageTime'] = $queueInfo['averageTime'] != -1 ? $queueInfo['averageTime'] : "Нет данных";

        $data['users'] = $scheduleItem->getUsersInQueue();
        $data['activityId'] = $activityId;

        return view("admin/queue/dashboard", $data);
    }

    public function getAllQueuesAjax(Request $request)
    {
        $allQueues = DB::table('schedule')
            ->join('activities', 'schedule.activity_id', '=', 'activities.id')
            ->select(
                'schedule.id',
                'schedule.date',
                'schedule.start_time',
                'schedule.end_time',
                'activities.name'
            );

        return DataTables::of($allQueues)
            ->addColumn('length', function ($schedule) {
                $scheduleItem = Schedule::find($schedule->id);
                return $scheduleItem->getInfo()['length'];
            })
            ->addColumn('average_time', function ($schedule) {
                $scheduleItem = Schedule::find($schedule->id);

                $time = $scheduleItem->getInfo()['averageTime'];
                $time = $time != -1 ? $time : "Нет данных";

                return is_numeric($time) ? ceil($time / 60) : $time;
            })
            ->addColumn('action', function ($schedule) {
                $view = View::make('admin.queue.button_queue',
                    [
                        "id" => $schedule->id
                    ]
                );
                return $view->render();
            })
            ->make(true);
    }

    public function getQueueAjax(Request $request, $activityId)
    {
        $scheduleItem = Schedule::find($activityId);
        $entities = $scheduleItem->getUsersInQueue();

        return DataTables::of($entities)
            ->addColumn('action', function ($user) {
                $view = View::make('admin.queue.table_button',
                    [
                        "userId" => $user['id']
                    ]
                );
                return $view->render();
            })
            ->make(true);
    }

    public function deleteUserFromQueueAjax(Request $request, $activityId, $userId)
    {
        QueueManager::dequeue($userId, $activityId, QueueSQL::DELETED);
    }

    public function getQueueInfoAjax(Request $request, $activityId)
    {
        $scheduleItem = Schedule::find($activityId);
        $queueInfo = $scheduleItem->getInfo();

        return response()->json($queueInfo);
    }
}
