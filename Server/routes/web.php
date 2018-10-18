<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\DB;

use App\User;
use App\Role;
use App\Activity;
use App\Activities_photo;
use Illuminate\Support\Facades\Redis;
use App\Classes\Queue\QueueRedis;
use App\Classes\Queue\QueueSQL;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/redis', function () {

    QueueRedis::reload();
    return 'Hi, redis';
});

Route::group(['middleware' => 'check_admin', 'prefix' => 'admin'],function (){
    Route::get('/', function () {

        return response()->redirectTo("/admin/activities");
    });

    // Активности
    Route::group(['prefix' => 'activities', 'middleware' => 'can:activities-view'],function (){
        Route::get('/','Admin\Activities@index')->name('admin_activities');

        Route::get('/ajax/all','Admin\Activities@getActivityAjax')->name('all_activities');

        Route::get('add','Admin\Activities@add')->middleware('can:activities-edit')->name('admin_activities_add');
        Route::post('add','Admin\Activities@add')->middleware('can:activities-edit')->name('admin_activities_add');

        Route::get('edit/{id}','Admin\Activities@edit')->name('admin_activities_edit');
        Route::get('view/{id}','Admin\Activities@edit')->name('admin_activities_view');
        Route::post('edit/{id}','Admin\Activities@edit')->middleware('can:activities-edit')->name('admin_activities_edit');

        Route::post('delete','Admin\Activities@delete')->middleware('can:activities-edit')->name('admin_activities_delete');
    });

    // Расписание
    Route::group(['prefix' => 'schedule', 'middleware' => 'can:schedule-view'],function () {
        Route::get('/', 'Admin\Schedules@index')->name('admin_schedule');
        Route::post('/', 'Admin\Schedules@index')->middleware('can:schedule-edit')->name('admin_schedule');
        Route::post('/can_delete', 'Admin\Schedules@canDelete')->middleware('can:schedule-edit')->name('admin_schedule_can_delete');
    });

    //Пользователи
    Route::group(['prefix' => 'users'], function (){
        Route::get('/', 'Admin\Users@index')->name('admin_users');
        Route::get('add', 'Admin\Users@add')->name('admin_users_add');
        Route::get('edit/{id}', 'Admin\Users@edit')->name('admin_users_edit');
        Route::get('view/{id}', 'Admin\Users@edit')->name('admin_users_view');

        Route::get('/ajax/all', 'Admin\Users@getAllAjax')->name('admin_users_all');

        Route::post('edit/{id}', 'Admin\Users@edit')->name('admin_users_edit');
        Route::post('add', 'Admin\Users@add')->name('admin_users_add');

        Route::post('delete','Admin\Users@delete')->middleware('can:users-edit')->name('admin_users_delete');
    });

    // Аккаунты
    Route::group(['prefix' => 'accounts', 'middleware' => 'can:accounts-view'],function(){
        Route::get('/', 'Admin\Accounts@index')->name('admin_accounts');
        Route::get('/add', 'Admin\Accounts@add')->middleware('can:accounts-edit')->name('admin_accounts_add');
        Route::get('/edit/{id}', 'Admin\Accounts@edit')->name('admin_accounts_edit');
        Route::get('/view/{id}', 'Admin\Accounts@edit')->name('admin_accounts_view');

        Route::get('/ajax/all', 'Admin\Accounts@getAllAjax')->name('admin_accounts_all');

        Route::post('/add', 'Admin\Accounts@add')->middleware('can:accounts-edit')->name('admin_accounts_add');
        Route::post('/edit/{id}', 'Admin\Accounts@edit')->middleware('can:accounts-edit')->name('admin_accounts_edit');
        Route::post('/delete', 'Admin\Accounts@delete')->middleware('can:accounts-edit')->name('admin_accounts_delete');
    });

    // Очереди
    Route::group(['prefix' => 'queue', 'middleware' => 'can:queue-view'],function(){
        Route::get('/', 'Admin\Queue@index')->name('admin_queue');
        Route::get('/{activityId}', 'Admin\Queue@getQueueDashboard')->name('admin_queue_dashboard');

        Route::get('/ajax/all', 'Admin\Queue@getAllQueuesAjax')->name('admin_get_queues');

        Route::get('/{activityId}/ajax/users', 'Admin\Queue@getQueueAjax')->name('admin_queue_ajax');
        Route::get('/{activityId}/ajax/info', 'Admin\Queue@getQueueInfoAjax')->name('admin_queueinfo_ajax');
        Route::post('/{activityId}/ajax/users/{userId}', 'Admin\Queue@deleteUserFromQueueAjax')
            ->name('admin_queue_user_delete');
    });

    // Фестиваль
    Route::get('festival','Admin\Festival@index')->name('admin_festival');
    Route::post('festival','Admin\Festival@index')->name('admin_festival');
});

Route::get('admin-login', function () {
    return view('admin/login');
})->name('admin_login');

Route::post('admin-login','Auth\LoginController@login')->name('admin-login');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
