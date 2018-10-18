<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Schema;

use Laravel\Passport\Passport;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        // Фикс БД
        Schema::defaultStringLength(191);

        Passport::routes();

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $this->menuConfig($event);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    // Конфигурация меню админ-панели
    private function menuConfig(BuildingMenu $event){

        //$event->menu->add('MAIN NAVIGATION');

//        $event->menu->add([
//            'text'    => 'Активности',
//            'icon'    => 'share',
//            'submenu' => [
//                [
//                    'text' => 'Level One',
//                    'url'  => 'admin/page1',
//                ],
//                [
//                    'text'    => 'Level One',
//                    'url'     => 'admin/page2',
//                ]
//            ]]);

        $event->menu->add('ФЕСТИВАЛЬ');
        $event->menu->add([
            'text'    => 'Активности',
            'icon'    => 'futbol-o',
            'url'  => route('admin_activities'),
            'can' => 'activities-view'
        ]);
        $event->menu->add([
            'text'    => 'Расписание',
            'icon'    => 'calendar-o',
            'url'  => 'admin/schedule',
            'can' => "schedule-view"
        ]);
        $event->menu->add([
            'text'    => 'Очереди',
            'icon'    => 'ticket',
            'url'  => 'admin/queue'
        ]);
        $event->menu->add([
            'text'    => 'Пользователи',
            'icon'    => 'user-o',
            'url'  => 'admin/users'
        ]);
        $event->menu->add([
            'text'    => 'Аккаунты',
            'icon'    => 'users',
            'url'  => 'admin/accounts',
            'can' => "accounts-view"

        ]);
        $event->menu->add([
            'text'    => 'Настройки',
            'icon'    => 'cog',
            'url'  => 'admin/festival'
        ]);

//        $event->menu->add('СЕРВИС');

//        $event->menu->add([
//            'text'    => 'Настройки',
//            'icon'    => 'cog',
//            'url'  => 'admin/page5'
//        ]);
//        $event->menu->add([
//            'text'    => 'Статистика',
//            'icon'    => 'bar-chart',
//            'url'  => 'admin/page6'
//        ]);
    }

}
