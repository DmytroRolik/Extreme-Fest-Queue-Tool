<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model{

    protected $table = 'activities';
    protected $fillable = ['name', 'description', 'queue', 'queue_buffer'];

    public function photos(){

        return $this->hasMany('App\Activities_photo');
    }
}
