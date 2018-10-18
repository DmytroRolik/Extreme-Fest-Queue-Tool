<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users_to_permission extends Model
{
    protected $fillable = ['permission_id', 'user_id'];

    public function permission_name(){
        return Permission::find($this->permission_id)->name;
    }
}
