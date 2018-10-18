package com.example.goron.diplomadmin.Service;

import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.support.v4.app.NotificationCompat;
import android.util.Log;

import com.example.goron.diplomadmin.Database.DatabaseHelper;
import com.example.goron.diplomadmin.Manager.DbManager;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.StartActivity;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

public class MessagingService extends FirebaseMessagingService {
    private RemoteMessage currentMessage;

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        if(remoteMessage.getData().size() > 0){
            currentMessage = remoteMessage;
            handleMessage();
        }
    }// onMessageReceived

    // обработка полученного сообщения
    public void handleMessage(){
        if(currentMessage.getData().get("type") == null) return;

        int messageType = Integer.parseInt(currentMessage.getData().get("type"));
//        Log.d("notificationsDebug", "msgType - " + messageType);
        switch (messageType){
            // тип сообщения 0 для теста
            case 0:
                sendNotification(
                        0,
                        createNotification(
                                currentMessage.getData().get("title"),
                                currentMessage.getData().get("body"),
                                null
                        )
                );
                break;

            // тип сообщения 1 (для всех) произошло изменение в расписании
            case 1:
                sendNotification(
                        messageType,
                        createNotification(
                                currentMessage.getData().get("title"),
                                currentMessage.getData().get("body"),
                                createIntent(StartActivity.class, null,"schedule")
                        )
                );
                break;

            //администратор получил разрешение на работу с определенной очередью
            case 100:
                sendNotification(
                        messageType,
                        createNotification(
                                currentMessage.getData().get("title"),
                                currentMessage.getData().get("body"),
                                createIntent(
                                        StartActivity.class,
                                        new String[]{"activityId","activityName"},
                                        "queue"
                                        )
                        )
                );
                break;

            //у работника забрали полномочия работы с очередью
            case 101:
                sendNotification(
                        messageType,
                        createNotification(
                                currentMessage.getData().get("title"),
                                currentMessage.getData().get("body"),
                                createIntent(
                                        StartActivity.class,
                                        null,
                                        null
                                )
                        )
                );
                break;

            // если мы получаем оповещение, которое нам не пренозначено,
            // то ничего не делаем
            default:
                break;
        }// switch
    }// handleMessage

    // отправка уведомления
    private void sendNotification(int notificationId, NotificationCompat.Builder notificationBuilder){
        NotificationManager manager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        manager.notify(notificationId, notificationBuilder.build());
    }// sendNotification

    // создание оповещения (можно и без него)
    // title - заголовок уведомления
    // body  - текст уведомления
    // notifyPendingIntent - для вызова активности/фрагмента при нажатии
    //                       null - если не требуется переход
    // !!! если нет notifyPendingIntent, может перейти на главную активность TODO: проверить (нет)!!!
    private NotificationCompat.Builder createNotification(String title, String body, PendingIntent notifyPendingIntent){
        NotificationCompat.Builder builder = new NotificationCompat.Builder(this)
                .setSmallIcon(R.drawable.logodnvermin)
                .setContentTitle(title)
                .setContentText(body)
                .setAutoCancel(true)
                .setDefaults(Notification.DEFAULT_ALL);

        if(notifyPendingIntent != null)
            builder.setContentIntent(notifyPendingIntent);
        return builder;
    }// createNotification

    // создание интента для перехода на нужную активность/фрагмент при нажатии на уведомление
    // targetClass   - класс активности/фрагмента, который будем вызывать
    // extraDataKeys - набор ключей дополнительных данных, содержащихся в сообщении
    //                 null - если дополнительные данные не требуются (пример: изменение в расписании)
    private PendingIntent createIntent(Class targetClass, String[] extraDataKeys, String destination){
        // создание Intent для задания действия при нажатии на оповещение
        Intent notifyIntent = new Intent(this, targetClass);
        notifyIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);

        // добавляем дополнительные данные в Intent, если такие были переданы
        // наличие таковых данных определяется по типу сообщения
        if(extraDataKeys != null){
            for (String key:extraDataKeys) {
                notifyIntent.putExtra(key, currentMessage.getData().get(key));
            }
        }

        if(destination != null){
            notifyIntent.putExtra("destination", destination);
        }

        //kostyl, BIG KOSTYL
        notifyIntent.putExtra("name", "admin");
        notifyIntent.putExtra("password", "admin");

        // возвращаем PendingIntent, сформированный на базе notifyIntent
        // TODO: выяснить необходимость изменять requestCode
        return PendingIntent.getActivity(this, 0,
                notifyIntent, PendingIntent.FLAG_UPDATE_CURRENT);
    }// createIntent


    @Override
    public void onNewToken(String s) {
        DbManager manager = new DbManager(getApplicationContext());
        manager.addFirebaseToken(s);
        Log.d("CurrentToken", "new token: " + s);
    }
}
