<?php

namespace App;

use App\Classes\Queue\QueueManager;
use Illuminate\Database\Eloquent\Model;
use App\Option;
use App\Users_to_activities_permission;

class Schedule extends Model
{
    protected $table = 'schedule';

    public function getUsersInQueue()
    {
        return QueueManager::getUsersInQueue($this->id);
    }

    public static function getDaysArray()
    {
        return Option::getDaysArray();
    }

    public function activity()
    {
        return $this->hasOne('App\Activity', 'id', 'activity_id');
    }

    public function getInfo()
    {
        $result = QueueManager::getQueueInfo($this->id);
        return $result;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($schedule_item) {

            $for_delete = Users_to_activities_permission::where('schedule_id', $schedule_item->id)->get();

            if ($for_delete) {
                foreach ($for_delete as $item) {
                    $item->delete();
                }
            }
        });
    }
}
