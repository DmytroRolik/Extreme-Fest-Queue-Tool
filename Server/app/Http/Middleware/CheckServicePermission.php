<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckServicePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $getAuthUser = Auth::user();

        switch (strtolower($permission)) {
            case "activitiesread":
                $isHasPermission = $getAuthUser->hasPermission(1);
                break;
            case "activitiesedit":
                $isHasPermission = $getAuthUser->hasPermission(2);
                break;
            case "scheduleread":
                $isHasPermission = $getAuthUser->hasPermission(3);
                break;
            case "scheduleedit":
                $isHasPermission = $getAuthUser->hasPermission(4);
                break;
            case "accountsread":
                $isHasPermission = $getAuthUser->hasPermission(5);
                break;
            case "accountsedit":
                $isHasPermission = $getAuthUser->hasPermission(6);
                break;
            case "usersread":
                $isHasPermission = $getAuthUser->hasPermission(7);
                break;
            case "usersedit":
                $isHasPermission = $getAuthUser->hasPermission(8);
            case "queueread":
                $isHasPermission = $getAuthUser->hasPermission(7);
                break;
            case "queueedit":
                $isHasPermission = $getAuthUser->hasPermission(8);
                break;
            default:
                $isHasPermission = false;
        }

        if (!$isHasPermission) {
            return response()->json([
                'success' => false,
                'message' => "This account does not have permission" .
                    " for use this resource"
            ])->setStatusCode(403);
        }

        return $next($request);
    }
}
