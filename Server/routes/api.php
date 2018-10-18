<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        'prefix' => 'v1',
        'namespace' => 'Api\v1',
        'middleware' => ['basicAuth']
    ],
    function () {

        Route::group(['prefix' => 'service'], function (){

            Route::post("/token", "MainController@addToken");

            // Возвращает список разрешений для сервиса
            Route::get("/permissions", "AdminController@getServerPermissions")
                ->middleware('check_admin');

            Route::get("/dates", "MainController@getDates");

            Route::group(['prefix' => 'users'], function (){
                Route::post("/", "MainController@registerNewUser")
                    ->middleware('check_admin')
                    ->middleware('check_service_permission:usersEdit');
            });

            Route::group(['prefix' => 'schedule'], function (){
                Route::get("/", "MainController@getSchedule");
            });
        });

        Route::group(['prefix' => 'admin'], function (){
            // Возвращает инфморацию о залогиненом администраторе
            Route::get("/self", "AdminController@getSelfInfo")
                ->middleware('check_admin');
        });

        Route::group(['prefix' => 'queue'], function (){
            Route::group(['prefix' => 'activities'], function (){
                Route::group(['prefix' => '{activityId}'],function (){

                    // Возвращает информацию об очереди
                    Route::get('/', "QueueController@getQueueInfo");

                    Route::group(['prefix' => 'users'],function () {

                        // Возвращает всех людей в очереди
                        Route::get('/', "QueueController@getQueueUsers")
                            ->middleware('check_admin');

                        Route::group(['prefix' => 'self'], function (){
                            // Возвращает позицию в очереди
                            Route::get('/', "QueueController@getSelfPosition");
                            // Ставит в очередь авторизовавшегося пользователя
                            Route::post('/', "QueueController@enqueueSelf");
                            // Удаляет из очереди авторизованного пользователя
                            Route::delete('/', "QueueController@dequeueSelf");
                        });

                        Route::group(['prefix' => "{userId}"], function (){
                            // Удаляет из очереди пользователя userId, если есть права администратора
                            Route::delete('/', "QueueController@dequeue")
                                ->middleware('check_admin')
                                ->middleware('check_activity_permission');
                            // Возвращает позицию пользователя в очереди
                            Route::get('/', 'QueueController@getUserPosition');
                            // Ставит в очередь пользователя {userId}
                            Route::put('/', 'QueueController@enqueueUser')
                                ->middleware('check_admin')
                                ->middleware('check_activity_permission');
                            // Отмечает юзера как опоздавшего
                            Route::patch('/', 'QueueController@lateUser')
                                ->middleware('check_admin')
                                ->middleware('check_activity_permission');
                        });
                    });
                });

                // Все активности с включенной электронной очередью
                Route::get('/', "QueueController@getActivities");
            });
        });
    }
);
