<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class CheckActivitiesPermission
 * @package App\Http\Middleware
 */
class CheckActivitiesPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->activityId == null ||
            !is_numeric($request->activityId)) {
            return response()->json([
                'success' => false,
                'message' => "This account does not have permission" .
                    "to use this resource"
            ])->setStatusCode(403);
        }

        $user = Auth::user();
        $permissions = $user->activities_permissionsIds();

        if (in_array($request->activityId, $permissions)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => "This account does not have permission" .
                " for use this resource"
        ])->setStatusCode(403);
    }
}
