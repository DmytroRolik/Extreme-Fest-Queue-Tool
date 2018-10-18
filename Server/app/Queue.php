<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['user_id', 'status', 'schedule_id', 'created_at', 'updated_at'];
    protected $table = 'queue';
}
