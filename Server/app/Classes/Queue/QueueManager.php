<?php
/**
 * Created by PhpStorm.
 * User: vsevolod
 * Date: 08/09/2018
 * Time: 12:50
 */

namespace App\Classes\Queue;

use App\Option;
use App\Schedule;
use App\User;
use App\Queue;
use App\Activity;
use App\Classes\Queue\QueueErrorCodes;
use http\Env\Request;

use App\Services\NotificationSender;

/**
 * Class QueueManager
 * @package App\Classes
 */
class QueueManager
{
    /**
     * @param int $userId
     * @param int $activityId
     * @return int
     */
    public static function enqueue(int $userId, int $activityId): int
    {
        if (!QueueSQL::isUserExist($activityId, $userId)) {
            return QueueErrorCodes::$USER_DOESNT_EXIST;
        }
        if (!QueueSQL::isActivityExist($activityId, $userId)) {
            return QueueErrorCodes::$ACTIVITY_DOESNT_EXIST;
        }
        if (QueueRedis::isUserInQueue($activityId, $userId)) {
            return QueueErrorCodes::$USER_ALREADY_IN_QUEUE;
        }

        $sqlRecord = Queue::create([
            'schedule_id' => $activityId,
            'user_id' => $userId,
            'status' => QueueSQL::STEP_IN
        ]);

        // Если не удалось сохранить в базу или редис, возвращается ошибка
        if ($sqlRecord) {
            $isSavedInRedis = QueueRedis::enqueue($activityId, $userId);
            if (!$isSavedInRedis) {
                $sqlRecord->delete();
                return QueueErrorCodes::$INTERNAL_ERROR;
            }
        } else {
            return QueueErrorCodes::$INTERNAL_ERROR;
        }

        return QueueErrorCodes::$SUCCESS;
    }

    public static function late(int $userId, int $activityId): int
    {
        while (QueueRedis::isLateBlock($activityId)) {
        }

        QueueRedis::blockForLate($activityId);

        if (!QueueSQL::isUserExist($activityId, $userId)) {
            QueueRedis::unblockForLate($activityId);
            return QueueErrorCodes::$USER_DOESNT_EXIST;
        }
        if (!QueueSQL::isActivityExist($activityId, $userId)) {
            QueueRedis::unblockForLate($activityId);
            return QueueErrorCodes::$ACTIVITY_DOESNT_EXIST;
        }
        if (!QueueRedis::isUserInQueue($activityId, $userId)) {
            QueueRedis::unblockForLate($activityId);
            return QueueErrorCodes::$USER_NOT_IN_QUEUE;
        }

        $totalLates = QueueSQL::getMovedCount($userId, $activityId);

        if ($totalLates > Option::getMaxLateCount()) {
            $sqlRecord = Queue::create([
                'schedule_id' => $activityId,
                'user_id' => $userId,
                'status' => QueueSQL::EXCLUDED
            ]);
            QueueRedis::dequeue($activityId, $userId);

            $activityName = Schedule::find($activityId)->activity->name;
            $curUserToken = User::find($userId)->firebase_token;
            NotificationSender::sendMsg(
                "$activityName",
                "Вы были исключены из очереди",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $curUserToken ]
            );
        } else {
            $sqlRecord = Queue::create([
                'schedule_id' => $activityId,
                'user_id' => $userId,
                'status' => QueueSQL::MOVED
            ]);
            QueueRedis::late($activityId, $userId, Option::getQueueOffset());

            $activityName = Schedule::find($activityId)->activity->name;
            $curUserToken = User::find($userId)->firebase_token;
            $curPosition = self::getUserPosition($activityId, $userId);

            NotificationSender::sendMsg(
                "$activityName",
                ($curPosition != -1 ? "Вы были перемещены в очереди. Ваша позиция $curPosition" : "Вы были исключены из очереди"),
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $curUserToken ]
            );
        }

        $queue = QueueRedis::getQueue($activityId);
        $activityName = Schedule::find($activityId)->activity->name;

        if(isset($queue[0])){
            $userToken = User::find($queue[0])->firebase_token;

            NotificationSender::sendMsg(
                "Queue tool",
                "Подошла ваша очередь на активность $activityName",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $userToken ]
            );
        }
        if(isset($queue[2])){
            $userToken = User::find($queue[2])->firebase_token;

            NotificationSender::sendMsg(
                "Queue tool",
                "Вы 3 в очереди на активность $activityName",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $userToken ]
            );
        }
        if(isset($queue[4])){
            $userToken = User::find($queue[4])->firebase_token;

            NotificationSender::sendMsg(
                "Queue tool",
                "Вы 5 в очереди на активность $activityName",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $userToken ]
            );
        }

        QueueRedis::unblockForLate($activityId);
        return QueueErrorCodes::$SUCCESS;
    }

    /**
     * Удаляет пользователя из очереди, используя указанный статус
     * @param int $userId
     * @param int $activityId
     * @param int $status
     * @return int
     */
    public static function dequeue(int $userId, int $activityId, int $status = QueueSQL::STEP_OUT)
    {
        if (!QueueSQL::isUserExist($activityId, $userId)) {
            return QueueErrorCodes::$USER_DOESNT_EXIST;
        }
        if (!QueueSQL::isActivityExist($activityId, $userId)) {
            return QueueErrorCodes::$ACTIVITY_DOESNT_EXIST;
        }
        if (!QueueRedis::isUserInQueue($activityId, $userId)) {
            return QueueErrorCodes::$USER_NOT_IN_QUEUE;
        }

        $sqlRecord = Queue::create([
            'schedule_id' => $activityId,
            'user_id' => $userId,
            'status' => $status
        ]);

        // Если не удалось сохранить в базу или редис, возвращается ошибка
        if ($sqlRecord) {
            $isSavedInRedis = QueueRedis::dequeue($activityId, $userId);
            if (!$isSavedInRedis) {
                $sqlRecord->delete();
                return QueueErrorCodes::$INTERNAL_ERROR;
            }
        }

        $queue = QueueRedis::getQueue($activityId);
        $activityName = Schedule::find($activityId)->activity->name;

        if($status == QueueSQL::EXCLUDED || $status == QueueSQL::DELETED){
            $curUserToken = User::find($userId)->firebase_token;

            NotificationSender::sendMsg(
                "$activityName",
                "Вы были исключены из очереди.",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $curUserToken ]
            );
        }
        if($status == QueueSQL::MOVED) {
            $curUserToken = User::find($userId)->firebase_token;
            $curPosition = self::getUserPosition($activityId, $userId);

            NotificationSender::sendMsg(
                "$activityName",
                "Вы были перемещены в очереди. Ваша позиция $curPosition",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $curUserToken ]
            );
        }

        if(isset($queue[0])){
            $userToken = User::find($queue[0])->firebase_token;

            NotificationSender::sendMsg(
                "$activityName",
                "Подошла ваша очередь на активность.",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $userToken ]
            );
        }
        if(isset($queue[2])){
            $userToken = User::find($queue[2])->firebase_token;

            NotificationSender::sendMsg(
                "$activityName",
                "Вы 3 в очереди на активность.",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $userToken ]
            );
        }
        if(isset($queue[4])){
            $userToken = User::find($queue[4])->firebase_token;

            NotificationSender::sendMsg(
                "$activityName",
                "Вы 5 в очереди на активность.",
                '200',
                ['activityId' => $activityId, "activityName" => $activityName],
                [ $userToken ]
            );
        }

        return QueueErrorCodes::$SUCCESS;
    }

    /**
     * Возвращает юзеров в очереди
     * @param int $activityId
     * @return array
     */
    public static function getUsersInQueue(int $activityId, int $count = -1): ?array
    {
        if (!QueueSQL::isActivityExist($activityId)) {
            return null;
        }

        $queue = QueueRedis::getQueue($activityId);
        if ($count != -1 && $count < count($queue)) {
            $queue = array_slice($queue, 0, $count);
        }

        $users = User::whereIn('id', $queue)->get()->toArray();
        $result = [];

        if (count($users) == count($queue)) {

            $position = 1;
            foreach ($users as $user) {
                $index = array_search($user['id'], $queue);
                $result[$index] = [
                    'id' => $user['id'],
                    'position' => $position++,
                    'name' => $user['name'],
                    'surname' => $user['surname'],
                    'number' => $user['number'],
                    'passport' => $user['passport'],
                ];
            }
            ksort($result);
        } else {
            return null;
        }

        return $result;
    }

    /**
     * Возвращает количество юзеров в очереди
     * @param int $activityId
     * @return int
     */
    public static function getQueueInfo(int $activityId): ?array
    {
        if (!QueueSQL::isActivityExist($activityId)) {
            return null;
        }

        $result['length'] = QueueRedis::getQueueLength($activityId);
        $result['averageTime'] = QueueRedis::getAverageTime($activityId);

        return $result;
    }

    /**
     * Возвращает позицию юзера в очереди
     * @param int $activityId
     * @param int $userId
     * @return int
     */
    public static function getUserPosition(int $activityId, int $userId): int
    {
        if (!QueueSQL::isUserExist($activityId, $userId)) {
            return -1;
        }
        if (!QueueSQL::isActivityExist($activityId, $userId)) {
            return -1;
        }
        if (!QueueRedis::isUserInQueue($activityId, $userId)) {
            return -1;
        }

        return QueueRedis::getUserPosition($activityId, $userId);
    }

    /**
     * Возвращает все активности из расписания
     * @param string $date
     * @return array
     */
    public static function getActivities(string $date = ""): array
    {
        return QueueSQL::getActivities($date);
    }
}