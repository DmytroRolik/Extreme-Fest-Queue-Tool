<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Activity;
use App\Schedule;
use App\Option;
use App\Queue;
use App\Users_to_activities_permission;
use App\Users_to_permission;

/**
 * Скалолазание 09.09.2018 ID:1
 *  В очереди:
 *      - Гость5 ID:10 - Первый в очереди - ПЕРЕМЕЩЕН!
 *      - Гость6 ID:11
 *      - Гость7 ID:12
 *      - Гость8 ID:14
 *  Неявились:
 *      - Гость3 ID:8
 *  Удален администратором:
 *      - Гость8 ID:13
 *  Успешно прошли:
 *      - Гость1 ID:6
 *      - Гость2 ID:7
 *      - Гость4 ID:9
 *
 * Class TestData
 */
class TestDataSeeder extends Seeder
{
    // Дни феста
    protected $dayFirst = "2018-09-09";
    protected $daySecond = "2018-09-10";
    protected $dayThird = "2018-09-11";

    // ID активностей
    protected $activities = [
        "Гравитационный батут" => 1,
        "Троллей" => 2,
        "Скалолазание" => 3,
        "Слеклайн" => 4,
        "Страйкбол" => 5,
        "Парапланы" => 6,
    ];

    protected $schedules = [
        'day1' => [
            "Скалолазание" => 1,
            "Троллей" => 2,
            "Страйкбол" => 3,
        ],
        'day2' => [
            "Скалолазание" => 4,
            "Слеклайн" => 5,
            "Парапланы" => 6,
            "Гравитационный батут" => 7,
        ],
        'day3' => [
            "Скалолазание" => 8,
            "Троллей" => 9,
            "Слеклайн" => 10
        ]
    ];

    protected $permissions = [
        "Активности: просмотр" => 1,
        "Активности: редактирование" => 2,
        "Расписание: просмотр" => 3,
        "Расписание: редактирование" => 4,
        "Аккаунты: просмотр" => 5,
        "Аккаунты: редактирование" => 6,
        "Пользователи: просмотр" => 7,
        "Пользователи: редактирование" => 8,
    ];

    protected $users = [];

    protected $queueStatuses = [
        "step_in" => 1,
        "step_out" => 2,
        "moved" => 3,
        "excluded" => 4,
        "exit" => 5,
        "deleted" => 6
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedActivities();
        $this->seedBaseSettings();
        $this->seedSchedule();
        $this->seedTestAdministrator();
        $this->seedUsers();
        $this->seedQueue();
        //$this->seedQueueMany();
        //$this->seedManyUsers();
    }

    public function seedBaseSettings()
    {
        $dataStart = Option::where('name', 'dateStart')->first();
        $dateEnd = Option::where('name', 'dateEnd')->first();

        $dataStart->value = "09.09.2018";
        $dateEnd->value = "11.09.2018";

        $dataStart->save();
        $dateEnd->save();
    }

    // Тестовые аккаунт администратора
    public function seedTestAdministrator()
    {
        // Admin 1
        $user = new User();
        $user->name = "Хранитель";
        $user->surname = "скалолазания";
        $user->login = "admin1";
        $user->role_id = 2;
        $user->password = Hash::make("admin1");
        $user->save();

        Users_to_activities_permission::create([
            'user_id' => $user->id,
            'schedule_id' => $this->schedules['day1']['Скалолазание']
        ]);
        Users_to_activities_permission::create([
            'user_id' => $user->id,
            'schedule_id' => $this->schedules['day2']['Скалолазание']
        ]);
        Users_to_activities_permission::create([
            'user_id' => $user->id,
            'schedule_id' => $this->schedules['day3']['Скалолазание']
        ]);

        $this->addPermision($user->id, 1);
        $this->addPermision($user->id, 3);
        $this->addPermision($user->id, 7);

        // Admin 4
        $user = new User();
        $user->name = "Хранитель";
        $user->surname = "страйкбола";
        $user->login = "admin4";
        $user->role_id = 2;
        $user->password = Hash::make("admin4");
        $user->save();

        Users_to_activities_permission::create([
            'user_id' => $user->id,
            'schedule_id' => $this->schedules['day1']['Страйкбол']
        ]);
        $this->addPermision($user->id, 1);
        $this->addPermision($user->id, 3);
        $this->addPermision($user->id, 7);

        // Admin 2
        $user = new User();
        $user->name = "Хранитель";
        $user->surname = "всего";
        $user->login = "admin2";
        $user->role_id = 2;
        $user->password = Hash::make("admin2");
        $user->save();

        foreach ($this->schedules['day1'] as $schedule) {
            Users_to_activities_permission::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule
            ]);
        }
        foreach ($this->schedules['day2'] as $schedule) {
            if ($schedule == 6) {
                continue;
            }
            Users_to_activities_permission::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule
            ]);
        }
        foreach ($this->schedules['day3'] as $schedule) {
            Users_to_activities_permission::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule
            ]);
        }

        $this->addPermision($user->id, 1);
        $this->addPermision($user->id, 2);
        $this->addPermision($user->id, 3);
        $this->addPermision($user->id, 4);
        $this->addPermision($user->id, 5);
        $this->addPermision($user->id, 6);
        $this->addPermision($user->id, 7);
        $this->addPermision($user->id, 8);

        // Admin 3
        $user = new User();
        $user->name = "Девочка";
        $user->surname = "на входе";
        $user->login = "admin3";
        $user->role_id = 2;
        $user->password = Hash::make("admin3");
        $user->save();

        $this->addPermision($user->id, 1);
        $this->addPermision($user->id, 2);
        $this->addPermision($user->id, 7);
        $this->addPermision($user->id, 8);
    }

    // Тестовые активности
    public function seedActivities()
    {
        $activiti = new Activity();
        $activiti->name = "Гравитационный батут";
        //$activiti->queue = true;
        $activiti->save();

        $activiti = new Activity();
        $activiti->name = "Троллей";
        //$activiti->queue = true;
        $activiti->save();

        $activiti = new Activity();
        $activiti->name = "Скалолазание";
        //$activiti->queue = true;
        $activiti->save();

        $activiti = new Activity();
        $activiti->name = "Слеклайн";
        //$activiti->queue = true;
        $activiti->save();

        $activiti = new Activity();
        $activiti->name = "Страйкбол";
        //$activiti->queue = true;
        $activiti->save();

        $activiti = new Activity();
        $activiti->name = "Парапланы";
        //$activiti->queue = false;
        $activiti->save();
    }

    // Заполнение расписания
    public function seedSchedule()
    {
        // FIRST DAY
        $schedule = new Schedule();
        $schedule->date = $this->dayFirst;
        $schedule->activity_id = $this->activities['Скалолазание'];
        $schedule->start_time = "10:00:00";
        $schedule->end_time = "17:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 1;
        $schedule->queue = true;
        $schedule->save();

        $schedule = new Schedule();
        $schedule->date = $this->dayFirst;
        $schedule->activity_id = $this->activities['Троллей'];
        $schedule->start_time = "11:00:00";
        $schedule->end_time = "18:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 2;
        $schedule->queue = true;

        $schedule->save();

        $schedule = new Schedule();
        $schedule->date = $this->dayFirst;
        $schedule->activity_id = $this->activities['Страйкбол'];
        $schedule->start_time = "14:00:00";
        $schedule->end_time = "17:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 3;
        $schedule->queue = true;
        $schedule->save();

        // SECOND DAY
        $schedule = new Schedule();
        $schedule->date = $this->daySecond;
        $schedule->activity_id = $this->activities['Скалолазание'];
        $schedule->start_time = "9:00:00";
        $schedule->end_time = "19:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 1;
        $schedule->queue = true;
        $schedule->save();

        $schedule = new Schedule();
        $schedule->date = $this->daySecond;
        $schedule->activity_id = $this->activities['Слеклайн'];
        $schedule->start_time = "10:00:00";
        $schedule->end_time = "15:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 2;
        $schedule->queue = true;
        $schedule->save();

        $schedule = new Schedule();
        $schedule->date = $this->daySecond;
        $schedule->activity_id = $this->activities['Парапланы'];
        $schedule->start_time = "15:00:00";
        $schedule->end_time = "19:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 3;
        $schedule->queue = true;
        $schedule->save();

        $schedule = new Schedule();
        $schedule->date = $this->daySecond;
        $schedule->activity_id = $this->activities['Гравитационный батут'];
        $schedule->start_time = "9:00:00";
        $schedule->end_time = "16:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 4;
        $schedule->queue = true;
        $schedule->save();

        // THIRD DAY
        $schedule = new Schedule();
        $schedule->date = $this->dayThird;
        $schedule->activity_id = $this->activities['Скалолазание'];
        $schedule->start_time = "10:00:00";
        $schedule->end_time = "14:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 1;
        $schedule->queue = true;
        $schedule->save();

        $schedule = new Schedule();
        $schedule->date = $this->dayThird;
        $schedule->activity_id = $this->activities['Троллей'];
        $schedule->start_time = "10:00:00";
        $schedule->end_time = "14:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 2;
        $schedule->queue = true;
        $schedule->save();

        $schedule = new Schedule();
        $schedule->date = $this->dayThird;
        $schedule->activity_id = $this->activities['Слеклайн'];
        $schedule->start_time = "9:00:00";
        $schedule->end_time = "14:00:00";
        $schedule->is_working = true;
        $schedule->sort_position = 3;
        $schedule->queue = true;
        $schedule->save();
    }

    // Заполнить юзеров
    public function seedUsers()
    {
        $this->users[] = User::create([
            "name" => "Гость1",
            "surname" => "фестиваля",
            "number" => 111,
            "passport" => "BK 89023215",
            "login" => "111",
            "role_id" => 3,
            "password" => Hash::make("user1")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость2",
            "surname" => "фестиваля",
            "number" => 112,
            "passport" => "BK 0120941",
            "login" => "112",
            "role_id" => 3,
            "password" => Hash::make("user2")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость3",
            "surname" => "фестиваля",
            "number" => 113,
            "passport" => "BK 01249585",
            "login" => "113",
            "role_id" => 3,
            "password" => Hash::make("user3")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость4",
            "surname" => "фестиваля",
            "number" => 114,
            "passport" => "BK 920941",
            "login" => "114",
            "role_id" => 3,
            "password" => Hash::make("user4")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость5",
            "surname" => "фестиваля",
            "number" => 115,
            "passport" => "BK 9088602",
            "login" => "115",
            "role_id" => 3,
            "password" => Hash::make("user5")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость6",
            "surname" => "фестиваля",
            "number" => 116,
            "passport" => "BK 0129924",
            "login" => "116",
            "role_id" => 3,
            "password" => Hash::make("user6")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость7",
            "surname" => "фестиваля",
            "number" => 117,
            "passport" => "BK 9303941",
            "login" => "117",
            "role_id" => 3,
            "password" => Hash::make("user7")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость8",
            "surname" => "фестиваля",
            "number" => 118,
            "passport" => "BK 12312455",
            "login" => "118",
            "role_id" => 3,
            "password" => Hash::make("user8")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость9",
            "surname" => "фестиваля",
            "number" => 119,
            "passport" => "BK 1243673",
            "login" => "119",
            "role_id" => 3,
            "password" => Hash::make("user9")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость10",
            "surname" => "фестиваля",
            "number" => 1110,
            "passport" => "BK 1213523",
            "login" => "1110",
            "role_id" => 3,
            "password" => Hash::make("user10")
        ])->id;

        $this->users[] = User::create([
            "name" => "Гость11",
            "surname" => "фестиваля",
            "number" => 1111,
            "passport" => "BK 3561346",
            "login" => "1111",
            "role_id" => 3,
            "password" => Hash::make("user11")
        ])->id;
    }

    // Создать тестовую очередь
    public function seedQueue()
    {
        // DAY 1 скалолазание
        Queue::create([
            'user_id' => $this->users[0],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:05:00',
            'created_at' => '2018-09-09 10:05:00'
        ]);
        Queue::create([
            'user_id' => $this->users[1],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:06:00',
            'created_at' => '2018-09-09 10:06:00'
        ]);
        Queue::create([
            'user_id' => $this->users[0],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_out'],
            'updated_at' => '2018-09-09 10:12:00',
            'created_at' => '2018-09-09 10:12:00'
        ]);
        Queue::create([
            'user_id' => $this->users[2],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:12:30',
            'created_at' => '2018-09-09 10:12:30'
        ]);
        Queue::create([
            'user_id' => $this->users[3],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:13:20',
            'created_at' => '2018-09-09 10:13:20'
        ]);
        Queue::create([
            'user_id' => $this->users[1],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_out'],
            'updated_at' => '2018-09-09 10:21:10',
            'created_at' => '2018-09-09 10:21:10'
        ]);
        Queue::create([
            'user_id' => $this->users[2],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['moved'],
            'updated_at' => '2018-09-09 10:22:10',
            'created_at' => '2018-09-09 10:22:10'
        ]);
        Queue::create([
            'user_id' => $this->users[3],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_out'],
            'updated_at' => '2018-09-09 10:30:00',
            'created_at' => '2018-09-09 10:30:00'
        ]);
        Queue::create([
            'user_id' => $this->users[4],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:31:00',
            'created_at' => '2018-09-09 10:31:00'
        ]);
        Queue::create([
            'user_id' => $this->users[2],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['excluded'],
            'updated_at' => '2018-09-09 10:33:10',
            'created_at' => '2018-09-09 10:33:10'
        ]);
        Queue::create([
            'user_id' => $this->users[5],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:33:50',
            'created_at' => '2018-09-09 10:33:50'
        ]);
        Queue::create([
            'user_id' => $this->users[6],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:34:05',
            'created_at' => '2018-09-09 10:34:05'
        ]);
        Queue::create([
            'user_id' => $this->users[7],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:35:05',
            'created_at' => '2018-09-09 10:35:05'
        ]);
        Queue::create([
            'user_id' => $this->users[7],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['deleted'],
            'updated_at' => '2018-09-09 10:35:10',
            'created_at' => '2018-09-09 10:35:10'
        ]);
        Queue::create([
            'user_id' => $this->users[8],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:36:10',
            'created_at' => '2018-09-09 10:36:10'
        ]);
        // UPDATE
        Queue::create([
            'user_id' => $this->users[4],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['moved'],
            'updated_at' => '2018-09-09 10:37:00',
            'created_at' => '2018-09-09 10:37:00'
        ]);
        Queue::create([
            'user_id' => $this->users[5],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['moved'],
            'updated_at' => '2018-09-09 10:38:00',
            'created_at' => '2018-09-09 10:38:00'
        ]);
        Queue::create([
            'user_id' => $this->users[9],
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $this->queueStatuses['step_in'],
            'updated_at' => '2018-09-09 10:38:10',
            'created_at' => '2018-09-09 10:38:10'
        ]);

    }

    public function seedQueueMany()
    {
        $date = new DateTime();
        for ($i = 0; $i < 10000; $i++) {

            $date->modify("+1 minutes");
            $this->addQueue($this->users[0],$this->queueStatuses['step_in'], $date->format('Y-m-d H:i:s'));
            $date->modify("+1 minutes");
            $this->addQueue($this->users[1],$this->queueStatuses['step_in'], $date->format('Y-m-d H:i:s'));
            $date->modify("+1 minutes");
            $this->addQueue($this->users[2],$this->queueStatuses['step_in'], $date->format('Y-m-d H:i:s'));
            $date->modify("+1 minutes");
            $this->addQueue($this->users[0],$this->queueStatuses['step_out'], $date->format('Y-m-d H:i:s'));
            $date->modify("+1 minutes");
            $this->addQueue($this->users[2],$this->queueStatuses['step_out'], $date->format('Y-m-d H:i:s'));
        }
    }

    public function addQueue($userId, $status, $time){

        Queue::create([
            'user_id' => $userId,
            'schedule_id' => $this->schedules['day1']['Скалолазание'],
            'status' => $status,
            'updated_at' => '2018-09-09 10:05:00',
            'created_at' => '2018-09-09 10:05:00'
        ]);
    }

    public function seedManyUsers()
    {
        for ($i = 0; $i < 1000; $i++) {
            User::create([
                "name" => "Гость$i",
                "surname" => "фестиваля",
                "number" => 111,
                "passport" => "BK 632$i",
                "login" => "777$i",
                "role_id" => 3,
                "password" => Hash::make("user$i")
            ])->id;
        }
    }

    // Добавляет разрешениею юзеру
    protected function addPermision($userId, $permissionId)
    {
        Users_to_permission::create([
            'user_id' => $userId,
            'permission_id' => $permissionId
        ]);
    }
}
