<?php

namespace App\Classes\Queue;

use App\Schedule;
use App\User;
use App\Queue;
use App\Activity;
use App\Classes\Queue\QueueErrorCodes;
use Illuminate\Support\Facades\URL;

/**
 * Class QueueSQL
 * @package App\Classes\Queue
 */
class QueueSQL
{
    /**
     * Встал в очередь
     * @var int
     */
    public const STEP_IN = 1;
    /**
     * Вышел из очереди (прошел активность)
     * @var int
     */
    public const STEP_OUT = 2;
    /**
     * Перемещен (опоздал)
     * @var int
     */
    public const MOVED = 3;
    /**
     * Исключен
     * @var int
     */
    public const EXCLUDED = 4;
    /**
     * Вышел самостоятельно
     * @var int
     */
    public const EXIT = 5;
    /**
     * Удален администратором
     * @var int
     */
    public const DELETED = 6;

    /**
     * Возвращает код, соответствующий результату опреации
     * @param int $userId
     * @param int $activityId
     * @return bool
     */
    public static function enqueue(int $userId, int $activityId): int
    {
        // Проверка существования ресурсов
        if (!self::isActivityExist($activityId)
            || !self::isUserExist($userId)) {
            return QueueErrorCodes::$BAD_REQUEST;
        }

        if (!self::isUserInQueue($userId, $activityId)) {
            Queue::create([
                'schedule_id' => $activityId,
                'user_id' => $userId,
                'status' => self::STEP_IN
            ]);
            return QueueErrorCodes::$SUCCESS;
        }
        return QueueErrorCodes::$USER_ALREADY_IN_QUEUE;
    }

    /**
     * Удаляет пользователя из очереди, используя указанный статус
     * @param int $userId
     * @param int $activityId
     * @param int $status
     * @return int
     */
    public static function dequeue(int $userId, int $activityId, int $status = 2)
    {
        // Проверка на то, что статус соответствуюет статусу удаления
        if (!in_array(
            $status,
            [self::STEP_OUT, self::EXIT, self::DELETED, self::EXCLUDED]
        )) {
            return QueueErrorCodes::$BAD_REQUEST;
        }

        // Проверка существования ресурсов
        if (!self::isActivityExist($activityId)
            || !self::isUserExist($userId)) {
            return QueueErrorCodes::$BAD_REQUEST;
        }

        if (self::isUserInQueue($userId, $activityId)) {
            Queue::create([
                'schedule_id' => $activityId,
                'user_id' => $userId,
                'status' => $status
            ]);
            return QueueErrorCodes::$SUCCESS;
        }
        return QueueErrorCodes::$USER_NOT_IN_QUEUE;
    }

    /**
     * Возвращает юзеров в очереди
     * @param int $activityId
     * @return array
     */
    public static function getUsersInQueue(int $activityId, int $count = -1): ?array
    {
        if (!self::isActivityExist($activityId)) {
            return null;
        }

        $users = $users = Queue::select('user_id', 'id', 'schedule_id', 'status')
            ->where('schedule_id', $activityId)->get()->toArray();

        $res = self::make_queue($users, 3);

        return $res;
    }

    protected static function make_queue($arr, $offset)
    {
        $result = array();

        foreach ($arr as $entry) {
            //dd($arr);
            if ($entry['status'] == 1) {
                // если только встал в очередь, добавялем в конец результирующего массива
                array_push($result, $entry);
            } else {
                if ($entry['status'] == 3) {
                    // если опоздал, то смещаем на $offset вниз
                    $result = self::move_entry($result, $entry, $offset);
                } else {
                    // во всех остальных случаях - удаляем из очереди
                    $result = self::remove_entry($result, $entry);
                }
            }
            // если статус =2 можно пользоваться array_shift()
            // извлекает первый элемент массива и сокращая размер на один элемент
            // !! возвращает извлеченное значение, а не массив !!
        }

        return $result;
    }

    // сдвинуть запись вниз на $offset позиций ($offset может предаваться только тут, при необходимости)
    protected static function move_entry($arr, $entry, $offset)
    {
        // т.к. опоздать может только первый в оччереди,
        // для начала массива берем offset=1
        $start = array_slice($arr, 1, $offset);

        // конец массива
        $end = array_slice($arr, $offset + 1);

        // склеиваем (новое начало, смещенная запись, конец массива)
        return array_merge($start, [$entry], $end);
    }

    protected static function remove_entry($arr, $entry)
    {
        $index = self::search_by_user_id($entry['user_id'], $arr);

        // TODO: обработка если индекс -1
        // в реальности вероятность ее появления мала, но... но...

        // массив до удаляемой записи
        $start = array_slice($arr, 0, $index);

        // массив после удаляемой записи
        $end = array_slice($arr, $index + 1);

        // массив до + массив после = удалено?
        // хз, насколько это хорошо или привильно, но пока так
        return array_merge($start, $end);
    }

    protected static function search_by_user_id($user_id, $arr)
    {
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i]['user_id'] == $user_id) {
                return $i;
            }
        }

        return -1;
    }

    /**
     * Возвращает количество юзеров в очереди
     * @param int $activityId
     * @return int
     */
    public static function getQueueLength(int $activityId): int
    {
        if (!self::isActivityExist($activityId)) {
            return -1;
        }

        // В общем случае, необходимо стараться совмещвать получение пользователей и
        // и их числа, а не использовать два разных метода
        return count(self::getUsersInQueue($activityId));
    }

    /**
     * Возвращает позицию юзера в очереди
     * @param int $activityId
     * @param int $userId
     * @return int
     */
    public static function getUserPosition(int $activityId, int $userId): ?array
    {
        if (!self::isActivityExist($activityId) || !self::isUserExist($userId)) {
            return null;
        }

        $users = self::getUsersInQueue($activityId);

        if (count($users) == 0) {
            return null;
        }

        $position = -1;
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['id'] == $userId) {
                $position = $i + 1;
                break;
            }
        }

        return ['position' => $position, 'total' => count($users)];
    }

    public static function getActivities(string $date = ""): ?array
    {
        // Выборка всех записей из расписания, активности которых поддерживают
        // электронную очередь и привязаны к переданной дате, если таковая имеется
        $allSchedule = Schedule::whereIn('activity_id', function ($q) {
            $q->select('id')
                ->from('activities')
                ->where('queue', true);
        });
        if (!empty($date)) {
            $allSchedule = $allSchedule->where("date", $date);
        }
        $allSchedule = $allSchedule->get();

        $result = [];
        foreach ($allSchedule as $schedule) {
            $activity = $schedule->activity;
            $result[] = [
                'id' => $schedule->id,
                'date' => $schedule->date,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'name' => $activity->name,
                'main_photo' => $activity->main_photo_url ? URL::to('/') . $activity->main_photo_url : null
            ];
        }

        return $result;
    }

    public static function isUserInQueue(int $userId, int $activityId): bool
    {
        $lastStatus = self::getUserStatus($userId, $activityId);
        return ($lastStatus == self::STEP_IN || $lastStatus == self::MOVED);
    }

    public static function getUserStatus(int $userId, int $activityId): int
    {
        // TODO Все это критичиски нуждается в оптимизации!
        // Последняя запись в таблице очереди с выбранным юзером на выбранную активность
        $lastRecord = Queue::where("schedule_id", $activityId)
            ->where("user_id", $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Если запись есть, проверяем ее статус
        if ($lastRecord) {
            return $lastRecord->status;
        }

        return 0;
    }

    public static function getUserHistoryStatus(int $userId, int $activityId, $offset = 0)
    {
        $records = Queue::where("schedule_id", $activityId)
            ->where("user_id", $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Если запись есть, проверяем ее статус
        if ($records) {
            if (isset($records[$offset])) {
                return [
                    "status" => $records[$offset]->status,
                    "time" => $records[$offset]->created_at
                ];
            }
        }

        return null;
    }

    public static function getUserStatusWithTime(int $userId, int $activityId): ?array
    {
        // TODO Все это критичиски нуждается в оптимизации!
        // Последняя запись в таблице очереди с выбранным юзером на выбранную активность
        $lastRecord = Queue::where("schedule_id", $activityId)
            ->where("user_id", $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Если запись есть, проверяем ее статус
        if ($lastRecord) {
            return [
                "status" => $lastRecord->status,
                "time" => $lastRecord->created_at
            ];
        }

        return null;
    }

    public static function isActivityExist(int $activityId): bool
    {
        $activitySchedule = Schedule::where("id", $activityId)
            ->where('queue', true)
            ->first();

        return boolval($activitySchedule);
    }

    public static function isUserExist(int $userId): bool
    {
        return User::find($userId) != null;
    }

    public static function getMovedCount(int $userId,int $activityId): int
    {
        $records = Queue::select(['id','user_id', 'schedule_id', 'status'])
            ->where("schedule_id", $activityId)
            ->where("user_id", $userId)
            ->orderBy('created_at','desc')
            ->get()
            ->toArray();
  
        $count = self::countLates($records);

        return $count;
    }

    public static function countLates($arr)
    {
        $status_index = 3;
        $late_status = 3;
        $first_entry_index = self::searchFirstByStatus($arr, 1);
        $counter = 0;

        for ($i = 0; $i < $first_entry_index; $i++) {
            if ($arr[$i]['status'] == $late_status) {
                $counter++;
            }
        }

        return $counter;
    }

    public static function searchFirstByStatus($arr, $status)
    {
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i]['status'] == $status) {
                return $i;
            }
        }

        return -1;
    }
}