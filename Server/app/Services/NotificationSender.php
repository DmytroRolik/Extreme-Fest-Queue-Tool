<?php

namespace App\Services;

// ключ доступа к апи для нашего сревера
// получается в консоли firebase
define( 'API_ACCESS_KEY',
    'AAAAJfbJLPc:APA91bEkascpto-m55tdDW63vvW3wFS82jS80QnraBkdNiTpkiGfdbJSlsF0ZIYEgEZuVLUybzzWYUnnfmcxn-g3y_V3yDmJgSvp2aRlEeKYeQNOVwYfnTI_KecQrHEpKP1uEXhdJRmi' );

class NotificationSender
{
    // создание сообщения, содержащего все необходимые данные
    // параметры:
    // $title   - заголовок(текст)
    // $msgText - текст сообщения(текст)
    // $msgId   - идентификатор типа сообщения (необходим для обработки на клиенте)
    // $data    - массив (ассоциативный) данных для обработки на клиенте, null - если не нужны
    public static function createMessage($title, $msgText, $msgType, $data){
        // NOTE: все поля помещаются в data, т.к. данные в 'notification'
        // при работе в бекграунде вызывают стандартный сервис по обработке уведомлений
        // а при получении data message на клиенте всегда вызывается обработчик onMessageReceived()

        // создаем массив из идентификатора сообщения (id) (необходим для обработки сообщения на клиенте)
        // самого сообщения (body) и заголовка(title)
        $curData = [
            'type' => $msgType,
            'body' => $msgText,
            'title' => $title
            ];
        // добавляем дополнительные данные из массива $data, если такие есть
        if($data != null) {
            $curData = array_merge($curData, $data);
        }

        // массив $fields необходим для формирования сообщения
        // он должен содержать все необходимые элементы
        // to  - уникальный токен каждого экземпляра приложения (получаем в функции занимающейся отправкой)
        // notification - уведомление (содержит в себе title и body)
        // data - массив данных, передаваемых в сообщении (любые данные в формате ключ => значение)
        $fields = array
        (
            'data'          => $curData
        );
        return $fields;
    }

    // отправка сообщения одному пользователю
    // параметры:
    // $msg     - подготовленное сообщение
    // $token   - токен пользователя, который получит сообщение
    // при необходимости можно добавить параметры
    public static function sendMsg($title, $msgText, $msgType, $data, $tokens){
        // урла для сервиса
        $url = 'https://fcm.googleapis.com/fcm/send';

        // добавляем токен адресата
        $curMsg = self::createMessage($title, $msgText, $msgType, $data);
        $curMsg += ['registration_ids' => $tokens];

        // создаем заголовки для отправки
        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        // подготовка curl для отправки
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $url );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $curMsg ) );

        // отправка сообщения
        // curl_exec возвращает true при успехе и false при неудаче
        // при необходимости можно обрабатывать результат отправки
        $result = curl_exec($ch );

        curl_close( $ch );
    }// sendMsgToUser
}