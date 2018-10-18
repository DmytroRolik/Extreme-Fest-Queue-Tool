<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'login',
        'number',
        'passport',
        'is_active',
        'role_id',
        'firebase_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin()
    {
        return in_array($this->role_id, [1, 2]);
    }

    public function permissions()
    {
        return Users_to_permission::where('user_id', $this->id)->get();
    }

    public function activities_permissions()
    {
        return Users_to_activities_permission::where('user_id', $this->id)->get();
    }

    public function permissionsIds()
    {
        if ($this->role_id == 1) {
            return array_column(Permission::all()->toArray(), "id");
        }

        return array_map(function ($value) {
            return $value['permission_id'];
        }, Users_to_permission::where('user_id', $this->id)->get()->toArray());
    }

    public function activities_permissionsIds()
    {
        if ($this->role_id == 1) {
            return array_column(Schedule::all()->toArray(), "id");
        }

        return array_map(function ($value) {
            return $value['schedule_id'];
        }, Users_to_activities_permission::where('user_id', $this->id)->get()->toArray());
    }

    public function hasPermission($id)
    {
        $permissions = $this->permissionsIds();

        return in_array($id, $permissions);
    }

    public function findForPassport($identifier)
    {
        return $this->orWhere('email', $identifier)->orWhere('login', $identifier)->first();
    }
}
