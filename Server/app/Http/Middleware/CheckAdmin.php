<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){

        $user = Auth::user();

        // Вход в админ. панель возможен только в случае когда роль юзера Main_admin (1) или Admin(2)
        if($user != null && in_array($user->role_id,[1,2])){
            return $next($request);
        }

        return redirect(route('admin_login'));
    }
}
