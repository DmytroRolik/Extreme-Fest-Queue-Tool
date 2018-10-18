<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Activity;
use App\Schedule;

class Users_to_activities_permission extends Model
{
    protected $fillable = ['user_id', 'schedule_id'];

    public function activity_name()
    {
        $schedule = Schedule::find($this->schedule_id);

        return Activity::find($schedule->activity_id)->name . " " . $schedule->date;
    }
}
