<?php

namespace App\Classes\Queue;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * Class QueueRedis
 * @package App\Classes\Queue
 */
class QueueRedis
{
    public static function enqueue(int $activityId, int $userId)
    {
        try {
            // Очередь загружается из бд, предпологается что инфа сначала будет
            // записана туда, поэтому дальнейшая обработка при отсутствующей в редисе
            // очереди вестись не будет
            if (!self::isQueueExist($activityId)) {
                $isExist = self::loadQueue($activityId);
                return $isExist;
            }

            if (self::isUserInQueue($activityId, $userId)) {

                return false;
            }

            Redis::rpush("activity:$activityId", $userId);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        return true;
    }

    public static function dequeue(int $activityId, int $userId = -1)
    {
        try {
            // Очередь загружается из бд, предпологается что инфа сначала будет
            // записана туда, поэтому дальнейшая обработка при отсутствующей в редисе
            // очереди вестись не будет
            if (!self::isQueueExist($activityId)) {
                $isExist = self::loadQueue($activityId);
                return $isExist;
            }

            self::dequeueUser($activityId, $userId);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        return true;
    }

    protected static function dequeueUser(int $activityId, int $userId)
    {
        self::fixTime($activityId);

        if ($userId != -1) {
            Redis::lrem("activity:$activityId", 0, $userId);
        } else {
            Redis::lPop("activity:$activityId");
        }
    }

    public static function late(int $activityId, int $userId, int $offset = 3)
    {
        // Очередь загружается из бд, предпологается что инфа сначала будет
        // записана туда, поэтому дальнейшая обработка при отсутствующей в редисе
        // очереди вестись не будет
        if (!self::isQueueExist($activityId)) {
            $isExist = self::loadQueue($activityId);
            return $isExist;
        }

        $offset = $offset - 1;

        $result = self::lateUser($activityId, $userId, $offset);

        return $result;
    }

    protected static function lateUser(int $activityId, int $userId, int $offset = 3)
    {
        try {

            $position = self::getUserPosition($activityId, $userId);

            if ($position == -1) {
                return false;
            }

            $length = self::getQueueLength($activityId);

            if ($position + $offset < $length) {
                $newPosition = $position + $offset;
            } else {
                $newPosition = $position;
            }

            Redis::lrem("activity:$activityId", 0, $userId);
            Redis::linsert(
                "activity:$activityId",
                "AFTER",
                Redis::lindex("activity:$activityId", $newPosition),
                $userId
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        return true;
    }

    public static function getNext(int $activityId)
    {
        if (!self::isQueueExist($activityId)) {
            return false;
            return false;
        }

        if (self::getQueueLength($activityId) == 0) {
            return false;
        }

        return Redis::lindex("activity:$activityId", 0);
    }

    public static function getUserPosition(int $activityId, int $userId)
    {
        if (!self::isQueueExist($activityId)) {
            return false;
        }

        $queue = self::getQueue($activityId);

        $position = array_search($userId, $queue);
        if ($position !== false) {
            return $position;
        } else {
            return -1;
        }
    }

    public static function getQueueLength(int $activityId)
    {
        return Redis::llen("activity:$activityId");
    }

    public static function isQueueExist(int $activityId)
    {
        return Redis::get("activity:$activityId:load");
    }

    public static function isUserInQueue(int $activityId, $userId)
    {
        $position = self::getUserPosition($activityId, $userId);

        return $position !== -1;
    }

    public static function getQueue(int $activityId)
    {
        if (!self::isQueueExist($activityId)) {
            self::loadQueue($activityId);
        }

        return Redis::lrange("activity:$activityId", 0, -1);
    }

    public static function reload()
    {
        Redis::flushAll();

        // Все активности с включенной в данный момент очередью
        $activities = QueueSQL::getActivities();
        foreach ($activities as $activity) {
            self::loadQueue($activity['id']);
        }
    }

    public static function loadQueue($activityId)
    {
        Redis::append("activity:$activityId:load", true);

        $schedules = QueueSQL::getUsersInQueue($activityId);

        if (count($schedules) == 0) {
            return false;
        }

        Redis::del("activity:$activityId");
        Redis::del("activity:$activityId:times");

        foreach ($schedules as $schedule) {
            Redis::rPush("activity:$activityId", $schedule['user_id']);
        }

        return true;
    }

    public static function fixTime($activityId)
    {
        Redis::rPush("activity:$activityId:times", time());
        Redis::expire("activity:$activityId:times", 1800);
    }

    public static function getAverageTime($activityId)
    {
        $timeStamps = Redis::lrange("activity:$activityId:times", 0, -1);

        if (count($timeStamps) < 3) {
            return -1;
        }

        $times = [];
        for ($i = 1; $i < count($timeStamps); $i++) {
            // Разница с предыдущим временем
            $times[] = $timeStamps[$i] - $timeStamps[$i - 1];
        }

        $result = intval(round(array_sum($times) / count($times)));

        return $result;
    }

    public static function blockForLate($id)
    {
        Redis::set("activity-late-block:$id", true);
    }

    public static function unblockForLate($id)
    {
        Redis::set("activity-late-block:$id", false);
    }

    public static function isLateBlock($id)
    {
        return Redis::get("activity-late-block:$id");
    }
}