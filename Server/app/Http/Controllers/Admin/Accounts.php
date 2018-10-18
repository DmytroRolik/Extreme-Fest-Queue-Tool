<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Console\Presets\React;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Account;
use App\Permission;
use App\Schedule;
use App\Users_to_permission;
use App\Users_to_activities_permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class Accounts extends Controller
{
    protected $message_add_success = "<strong>Новый аккаунт</strong> успешно создан";
    protected $message_edit_success = "<strong>Аккаунт</strong> успешно сохранён";

    public function index(Request $request)
    {
        $orderBy = $request->input('sort') ? $request->input('sort') : 'name';
        $direction = $request->input('dir') ? $request->input('dir') : 'desc';

        $data['all_accounts'] = Account::where("role_id", 2)
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        if ($request->message_success) {
            $data['message_success'] = $request->message_success;
        }

        $data['dir'] = $direction;
        $data['order'] = $orderBy;

        return view('admin/accounts/list', $data);
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $this->validation($request);

            $account = new Account();

            $account->name = $request->name;
            $account->role_id = 2;
            $account->surname = $request->surname;
            $account->login = $request->login;
            $account->password = Hash::make($request->password);
            $account->save();

            foreach ($request->service_permissions as $service_permission) {

                $permission = new Users_to_permission();
                $permission->user_id = $account->id;
                $permission->permission_id = $service_permission;
                $permission->save();
            }

            if (isset($request->activities_permissions)) {
                foreach ($request->activities_permissions as $activity_id) {

                    $permission = new Users_to_activities_permission();
                    $permission->user_id = $account->id;
                    $permission->schedule_id = $activity_id;
                    $permission->save();
                }
            }

            return redirect()->route("admin_accounts")->withSuccess($this->message_add_success);

        }// if method post

        $data['all_permission'] = Permission::orderBy('name', 'asc')->get();
        $data['schedule_list'] = $this->getScheduleWithQueue();

        return view('admin/accounts/add', $data);
    }

    public function delete(Request $request)
    {

        $for_delete = $request->input('for-delete');

        if ($for_delete) {

            foreach ($for_delete as $id) {
                $account = Account::find($id);
                if ($account) {
                    $account->delete();
                }
            }
        }

        return redirect()->route("admin_accounts");
    }

    public function edit(Request $request, $id)
    {

        if ($request->isMethod('post')) {
            $this->validation($request);

            $account = Account::find($id);

            $account->name = $request->name;
            $account->surname = $request->surname;
            $account->login = $request->login;
            $account->login = $request->login;

            if (isset($request->new_password)) {
                $account->password = Hash::make($request->new_password);
            }

            $account->deleteAllPermissons();
            $account->save();

            // Добавление разрешений на использование сервиса
            if ($request->service_permissions) {
                foreach ($request->service_permissions as $service_permission) {

                    $permission = new Users_to_permission();
                    $permission->user_id = $account->id;
                    $permission->permission_id = $service_permission;
                    $permission->save();
                }
            }

            // Добавление разрешений на работу на активностях
            if ($request->activities_permissions) {
                foreach ($request->activities_permissions as $activity_id) {

                    $permission = new Users_to_activities_permission();
                    $permission->user_id = $account->id;
                    $permission->schedule_id = $activity_id;
                    $permission->save();
                }
            }

            return redirect()->route('admin_accounts')->withSuccess($this->message_edit_success);
        }

        $data['all_permission'] = Permission::orderBy('name', 'asc')->get();
        $data['schedule_list'] = $this->getScheduleWithQueue();

        $data['account'] = Account::find($id);

        // Получение списка разрешений, сохранение их в массивы структуры index => id
        $data['service_permission'] = array_map(function ($val) {
            return $val['permission_id'];
        }, $data['account']->permissions()->toArray());
        $data['activities_permission'] = array_map(function ($val) {
            return $val['schedule_id'];
        }, $data['account']->activities_permissions()->toArray());

        return view('admin/accounts/edit', $data);
    }

    public function getAllAjax(Request $request)
    {
        $allAccounts = Account::where("role_id", 2);

        return DataTables::of($allAccounts)
            ->addColumn('permissions', function ($account) {
                $view = View::make('admin.accounts.permissions',
                    [
                        "account" => $account
                    ]
                );
                return $view->render();
            })
            ->addColumn('action', function ($account) {
                $view = View::make('admin.accounts.button_action',
                    [
                        "id" => $account->id
                    ]
                );
                return $view->render();
            })
            ->rawColumns(['permissions','action'])
            ->make(true);
    }

    // Возвращает список позиций в расписании, которые поддерживают электронную очередь
    protected function getScheduleWithQueue()
    {

        $dates = Schedule::getDaysArray();
        $result = [];

        foreach ($dates as $date) {

            $curDateStr = $date->format('Y-m-d');
            $schedule = Schedule::where('date', $curDateStr)->get();

            foreach ($schedule as $item) {

                if ($item->queue) {
                    $item->start_time = substr($item->start_time, 0, strlen($item->start_time) - 3);
                    $item->end_time = substr($item->end_time, 0, strlen($item->end_time) - 3);
                    $result[$date->format('d.m.Y')][] = $item;
                }
            }
        }

        return $result;

    }// getScheduleWithQueue

    protected function validation($request)
    {

        $this->validate($request, [
            'login'        => 'required|min:3',
            'password'     => 'required|min:3',
            'new_password' => 'nullable|min:3',
        ], [], ['login' => '"Логин"', 'password' => '"Пароль"']);
    }
}
