<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Users_to_permission;

class Account extends Model
{
    protected $table = 'users';

    public static function getAllAccounts(){

        return Account::where('role_id', 2)->get();
    }

    public function permissions(){

        return Users_to_permission::where('user_id', $this->id)->get();
    }

    public function activities_permissions(){

        return Users_to_activities_permission::where('user_id', $this->id)->get();
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($account) {

            foreach($account->permissions() as $permission){
                $permission->delete();
            }

            foreach($account->activities_permissions() as $permission){
                $permission->delete();
            }
        });
    }

    public function deleteAllPermissons(){

        foreach($this->permissions() as $permission){
            $permission->delete();
        }

        foreach($this->activities_permissions() as $permission){
            $permission->delete();
        }
    }
}
