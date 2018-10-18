<?php

namespace App\Http\Controllers\Api\v1;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function getSelfInfo(Request $request)
    {
        $user = Auth::user();

        $perToActivities = $user->activities_permissionsIds();
        $perToService = $user->permissionsIds();

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'surname' => $user->surname,
                'role_id' => $user->role_id,
                'permission_to_activities' => $perToActivities,
                'permission_to_service' => $perToService
            ]
        ]);
    }

    public function getServerPermissions()
    {
        $permission = Permission::Select('id', 'name')
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'permissions' => $permission
        ]);
    }
}
