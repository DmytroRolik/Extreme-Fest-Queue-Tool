<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class Users extends Controller
{
    protected $message_edit_success = "<strong>Данные пользователя</strong> успешно сохранены";
    protected $message_add_success = "<strong>Новый пользователь</strong> успешно создан";

    public function index(Request $request)
    {

        $orderBy = $request->input('sort') ? $request->input('sort') : 'name';
        $direction = $request->input('dir') ? $request->input('dir') : 'desc';

        $data['all_users'] = User::where('role_id', 3)
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        $data['dir'] = $direction;
        $data['order'] = $orderBy;

        return view('admin/users/list', $data);
    }

    public function getAllAjax(Request $request)
    {
        $users = User::where("role_id", "3");

        return DataTables::of($users)
            ->addColumn('action', function ($user) {
                $view = View::make('admin.users.button_action',
                    [
                        "id" => $user->id
                    ]
                );
                return $view->render();
            })
            ->make(true);
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post')) {

            $user = new User();
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->passport = $request->passport;
            $user->password = Hash::make($request->passport);
            $user->number = $request->number;
            $user->login = $request->number;
            $user->role_id = 3;
            $user->firebase_token = ' ';
            $user->save();

            return redirect(route('admin_users'))->withSuccess($this->message_add_success);
        }

        return view('admin/users/add');
    }

    public function edit(Request $request, $id)
    {

        if ($request->isMethod('post')) {

            $user = User::find($id);
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->passport = $request->passport;
            $user->number = $request->number;
            $user->save();

            return redirect(route('admin_users'))->withSuccess($this->message_edit_success);
        }

        $data['user'] = User::find($id);

        return view('admin/users/edit', $data);
    }

    protected function validation($request)
    {

        $this->validate($request, [
            'passport' => 'required',
            'number'   => 'required'
        ], [], ['passport' => '"Номер документа"', 'number' => '"Номер браслета"']);
    }
}
