<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Role;
use App\Activity;
use App\Option;
use App\Permission;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->RoleSeeder();
        $this->UserSeeder();
        $this->OptionSeeder();
        $this->PermissionsSeeder();

        $this->call(TestDataSeeder::class);
        //$this->TestDateSeeder();
    }

    public function RoleSeeder()
    {

        // Главный администратор
        $role = new Role();
        $role->name = "main_admin";
        $role->save();

        // Администратор
        $role = new Role();
        $role->name = "admin";
        $role->save();

        // Пользователь
        $role = new Role();
        $role->name = "user";
        $role->save();
    }

    public function UserSeeder()
    {
        // Аккаунт главного администратора
        $user = new User();
        $user->name = "Администратор";
        $user->login = "admin";
        $user->role_id = 1;
        $user->password = Hash::make("admin");
        $user->save();
    }

    public function OptionSeeder()
    {
        $option = new Option();
        $option->name = 'dateStart';
        $option->value = '01.01.' . date("Y");
        $option->save();

        $option = new Option();
        $option->name = 'dateEnd';
        $option->value = '03.01.' . date("Y");
        $option->save();

        $option = new Option();
        $option->name = 'queueOffset';
        $option->value = '3';
        $option->save();

        $option = new Option();
        $option->name = 'queueAverageTimeExpire';
        $option->value = '1800';
        $option->save();

        $option = new Option();
        $option->name = 'maxLateCount';
        $option->value = '3';
        $option->save();
    }

    public function PermissionsSeeder()
    {
        $permission = new Permission();
        $permission->name = "Активности: просмотр";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Активности: редактирование";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Расписание: просмотр";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Расписание: редактирование";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Аккаунты: просмотр";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Аккаунты: редактирование";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Пользователи: просмотр";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Пользователи: редактирование";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Очередь: просмотр";
        $permission->save();

        $permission = new Permission();
        $permission->name = "Очередь: редактирование";
        $permission->save();
    }

    public function TestDateSeeder()
    {
        $activiti = new Activity();
        $activiti->name = "Гравитационный батут";
        $activiti->queue = true;
        $activiti->save();

        $activiti = new Activity();
        $activiti->name = "Троллей";
        $activiti->queue = true;
        $activiti->save();

        // Аккаунт тестового администратора
        $user = new User();
        $user->name = "Тестовый";
        $user->surname = "Администратор";
        $user->login = "admin_test";
        $user->role_id = 2;
        $user->password = Hash::make("admin_test");
        $user->save();

        //for($i = 0; $i < 2000; $i++) {
        // Аккаунт тестового пользователя
        $user = new User();
        $user->name = "Тестовый";
        $user->surname = "Пользователь";
        $user->login = "121214515";
        $user->number = "121214515";
        $user->passport = "BK1928371";
        $user->role_id = 3;
        $user->password = Hash::make("BK1928371");
        $user->save();
        //}
    }
}
