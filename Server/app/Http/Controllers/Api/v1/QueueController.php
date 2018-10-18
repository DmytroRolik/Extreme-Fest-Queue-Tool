<?php

namespace App\Http\Controllers\Api\v1;

use App\Classes\Queue\QueueSQL;
use App\Queue;
use App\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Classes\Queue\QueueManager;
use App\Classes\Queue\QueueErrorCodes;
use Validator;
use App\Activity;

class QueueController extends Controller
{
    /**
     * Помещает в очередь текущего авторизованного юзера
     * @param Request $request
     * @param $activityId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function enqueueSelf(Request $request, $activityId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId],
            ['activityId' => 'required|int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $userAuthId = Auth::user()->id;
        $code = QueueManager::enqueue($userAuthId, $activityId);

        $response = QueueErrorCodes::getResponse($code);

        return $response;
    }

    /**
     * Удаляет из очереди текущего авторизованного юзера
     * @param Request $request
     * @param $activityId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function dequeueSelf(Request $request, $activityId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId],
            ['activityId' => 'required|int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $userId = Auth::user()->id;
        $code = QueueManager::dequeue($userId, $activityId, QueueSQL::EXIT);

        $response = QueueErrorCodes::getResponse($code);

        return $response;
    }

    /**
     * @param Request $request
     * @param $activityId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getSelfPosition(Request $request, $activityId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId],
            ['activityId' => 'required|int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $userId = Auth::user()->id;
        $position = QueueManager::getUserPosition($activityId, $userId);

        if ($position < 0) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$USER_NOT_IN_QUEUE);
        }

        return response()->json(['success' => true, 'position' => $position]);
    }

    /**
     * Удаляет из очереди указанного пользователя со статусом переданным в теле
     * запроса
     * @param Request $request
     * @param $activityId
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function dequeue(Request $request, $activityId, $userId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId, 'userId' => $userId],
            ['activityId' => 'required|int', 'userId' => 'required|int']
        );

        // Если статус выхода из очереди не задан, используется статус
        // "Покинул очередь пройдя активность"
        $deleteMethos = QueueSQL::STEP_OUT;
        $data = $request->all();
        if (isset($data['delete_method'])) {
            switch (strtolower($data['delete_method'])) {
                case "step_out":
                    $deleteMethos = QueueSQL::STEP_OUT;
                    break;
                case "exclude":
                    $deleteMethos = QueueSQL::EXCLUDED;
                    break;
                case "delete":
                    $deleteMethos = QueueSQL::DELETED;
                    break;
                default:
                    return QueueErrorCodes::getResponse(QueueErrorCodes::$BAD_REQUEST);
            }
        }

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $code = QueueManager::dequeue($userId, $activityId, $deleteMethos);

        $response = QueueErrorCodes::getResponse($code);

        return $response;
    }

    /**
     * @param Request $request
     * @param $activityId
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getUserPosition(Request $request, $activityId, $userId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId, 'userId' => $userId],
            ['activityId' => 'required|int', 'userId' => 'required|int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $position = QueueManager::getUserPosition($activityId, $userId);

        if ($position === null || $position == -1) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$NOT_FOUND);
        }

        return response()->json(['success' => true, 'position' => $position]);
    }

    /**
     * Возвращает все активности, которые поддерживают электронную очередь
     * @param Request $request
     * @return array
     */
    public function getActivities(Request $request)
    {
        $data = $request->all();
        $date = "";

        // Если передана дата, проверка ее корректности
        if (isset($data['date'])) {
            $validator = Validator::make(
                ['date' => $data['date']],
                ['date' => 'date|date_format:"Y-m-d"']
            );
            if ($validator->fails()) {
                return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
            }
            $date = $data['date'];
        }

        $result = QueueManager::getActivities($date);

        return response()->json($result);
    }

    /**
     * Возвращает информацию об очереди
     * @param Request $request
     * @param $activityId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getQueueInfo(Request $request, $activityId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId],
            ['activityId' => 'required|int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $result['info'] = QueueManager::getQueueInfo($activityId);

        if (!$result['info']) {
            return self::getResponse(QueueErrorCodes::$NOT_FOUND);
        }

        $result['success'] = true;

        return response()->json($result);
    }

    /**
     * Возвращает юзеров в очереди
     * @param Request $request
     * @param $activityId
     * @return array|\Illuminate\Http\JsonResponse|null
     */
    public function getQueueUsers(Request $request, $activityId)
    {
        $count = -1;
        if ($request->input('count')) {
            $count = $request->input('count');
        }

        $validator = Validator::make(
            ['activityId' => $activityId, 'count' => $count],
            ['activityId' => 'required|int|min:0', 'count' => 'int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $result = QueueManager::getUsersInQueue($activityId, $count);

        if ($result === null) {
            return self::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $responceData = [
            'success' => true,
            'users' => $result
        ];

        return response()->json($responceData);
    }

    public function lateUser(Request $request, $activityId, $userId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId, "userId" => $userId],
            ['activityId' => 'required|int', "userId" => 'required|int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $code = QueueManager::late($userId,$activityId);

        $response = QueueErrorCodes::getResponse($code);

        return $response;
    }

    public function enqueueUser(Request $request, $activityId, $userId)
    {
        $validator = Validator::make(
            ['activityId' => $activityId, "userId" => $userId],
            ['activityId' => 'required|int', "userId" => 'required|int']
        );

        if ($validator->fails()) {
            return QueueErrorCodes::getResponse(QueueErrorCodes::$INVALID_PARAMETERS);
        }

        $code = QueueManager::enqueue($userId, $activityId);

        $response = QueueErrorCodes::getResponse($code);

        return $response;
    }
}
