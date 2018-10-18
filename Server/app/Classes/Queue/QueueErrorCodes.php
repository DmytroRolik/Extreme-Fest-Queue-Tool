<?php
/**
 * Created by PhpStorm.
 * User: vsevolod
 * Date: 08/09/2018
 * Time: 13:40
 */

namespace App\Classes\Queue;

class QueueErrorCodes
{
    public static $BAD_REQUEST = 0;
    public static $INTERNAL_ERROR = 1;
    public static $SUCCESS = 2;
    public static $USER_ALREADY_IN_QUEUE = 3;
    public static $USER_EXCLUDED = 4;
    public static $USER_NOT_IN_QUEUE = 5;
    public static $INVALID_PARAMETERS = 6;
    public static $NOT_FOUND = 7;
    public static $ALREDY_EXIST = 8;
    public static $USER_DOESNT_EXIST = 9;
    public static $ACTIVITY_DOESNT_EXIST = 10;

    /**
     * Возвращает ответ соответствующий переданному статусу ответов
     * @param int $code
     * @return \Illuminate\Http\JsonResponse|null
     */
    public static function getResponse(int $code)
    {
        $response = null;
        switch ($code) {
            case QueueErrorCodes::$SUCCESS:
                $response = response()->json([
                    "code" => QueueErrorCodes::$SUCCESS,
                    "success" => true,
                    "message" => "Operation success",
                ])->setStatusCode(200);
                break;
            case QueueErrorCodes::$USER_ALREADY_IN_QUEUE:
                $response = response()->json([
                    "code" => QueueErrorCodes::$USER_ALREADY_IN_QUEUE,
                    "success" => false,
                    "message" => "User already in queue",
                ])->setStatusCode(409);
                break;
            case QueueErrorCodes::$USER_NOT_IN_QUEUE:
                $response = response()->json([
                    "code" => QueueErrorCodes::$USER_NOT_IN_QUEUE,
                    "success" => false,
                    "message" => "User is not in queue",
                ])->setStatusCode(404);
                break;
            case QueueErrorCodes::$INVALID_PARAMETERS:
                $response = response()->json([
                    "code" => QueueErrorCodes::$INVALID_PARAMETERS,
                    "success" => false,
                    "message" => "Invalid parameters",
                ])->setStatusCode(400);
                break;
            case QueueErrorCodes::$NOT_FOUND:
                $response = response()->json([
                    "code" => QueueErrorCodes::$NOT_FOUND,
                    "success" => false,
                    "message" => "Resource not found. Check params",
                ])->setStatusCode(404);
                break;
            case QueueErrorCodes::$ALREDY_EXIST:
                $response = response()->json([
                    "code" => QueueErrorCodes::$ALREDY_EXIST,
                    "success" => false,
                    "message" => "Resource alredy exist",
                ])->setStatusCode(409);
                break;
            case QueueErrorCodes::$USER_DOESNT_EXIST:
                $response = response()->json([
                    "code" => QueueErrorCodes::$USER_DOESNT_EXIST,
                    "success" => false,
                    "message" => "User does'nt exist",
                ])->setStatusCode(404);
                break;
            case QueueErrorCodes::$ACTIVITY_DOESNT_EXIST:
                $response = response()->json([
                    "code" => QueueErrorCodes::$ACTIVITY_DOESNT_EXIST,
                    "success" => false,
                    "message" => "Activity does'nt exist",
                ])->setStatusCode(404);
                break;
            default:
                $response = response()->json([
                    "code" => QueueErrorCodes::$BAD_REQUEST,
                    "success" => false,
                    "message" => "Bad request. Check params, method and api url",
                ])->setStatusCode(400);
                break;
        }
        return $response;
    }
}