package com.example.goron.userdiplom.Service;

import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.support.v4.app.NotificationCompat;
import android.util.Log;

import com.example.goron.userdiplom.Manager.DbManager;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.StartActivity;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

public class MessagingService extends FirebaseMessagingService {
    private RemoteMessage currentMessage;

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        if (remoteMessage.getData().size() > 0) {
            currentMessage = remoteMessage;
            handleMessage();
        }
    }

    // обработка полученного сообщения
    public void handleMessage() {
        int messageType = Integer.parseInt(currentMessage.getData().get("type"));
        int notificationId;

        switch (messageType) {
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

            // изменилось расписание
            case 1:
                notificationId = messageType;
                sendNotification(
                        notificationId,
                        createNotification(
                                currentMessage.getData().get("title"),
                                currentMessage.getData().get("body"),
                                createIntent(StartActivity.class, null,"schedule")
                        )
                );
                break;

            // подходит очередь
            case 200:
                notificationId = Integer.parseInt(currentMessage.getData().get("activityId")) + messageType;
                sendNotification(
                        notificationId,
                        createNotification(
                                currentMessage.getData().get("title"),
                                currentMessage.getData().get("body"),
                                createIntent(StartActivity.class, null, "queues")
                        )
                );
                break;

            // опоздал
            case 201:
                notificationId = Integer.parseInt(currentMessage.getData().get("activityId")) + messageType - 1;
                sendNotification(
                        notificationId,
                        createNotification(
                                currentMessage.getData().get("title"),
                                currentMessage.getData().get("body"),
                                createIntent(StartActivity.class, null, "queues")
                        )
                );
                break;

            default:
                break;
        }
    }

    // отправка уведомления
    private void sendNotification(int notificationId, NotificationCompat.Builder notificationBuilder) {
        NotificationManager manager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        manager.notify(notificationId, notificationBuilder.build());
    }

    // создание оповещения (можно и без него)
    // title - заголовок уведомления
    // body  - текст уведомления
    // notifyPendingIntent - для вызова активности/фрагмента при нажатии
    //                       null - если не требуется переход
    private NotificationCompat.Builder createNotification(String title, String body, PendingIntent notifyPendingIntent) {
        NotificationCompat.Builder builder;
        if (Build.VERSION.SDK_INT < 26) {
            builder = new NotificationCompat.Builder(this);
        } else {
            initChannels(this);
            builder = new NotificationCompat.Builder(this, "default");
        }

        builder.setSmallIcon(R.drawable.logodn120)
        .setContentTitle(title)
        .setContentText(body)
        .setAutoCancel(true)
        .setDefaults(Notification.DEFAULT_ALL);

        if (notifyPendingIntent != null)
            builder.setContentIntent(notifyPendingIntent);
        return builder;
    }

    // создание канала для уведомление
    // необходимо для версии API >= 26
    public void initChannels(Context context) {
        if (Build.VERSION.SDK_INT < 26) {
            return;
        }
        NotificationManager notificationManager =
                (NotificationManager) context.getSystemService(Context.NOTIFICATION_SERVICE);
        NotificationChannel defaultChannel = new NotificationChannel("default",
                "default chanel",
                NotificationManager.IMPORTANCE_DEFAULT);

        defaultChannel.setDescription("default chanel for our notifications");
        notificationManager.createNotificationChannel(defaultChannel);
    }

    // создание интента для перехода на нужную активность/фрагмент при нажатии на уведомление
    // targetClass   - класс активности/фрагмента, который будем вызывать
    // extraDataKeys - набор ключей дополнительных данных, содержащихся в сообщении
    //                 null - если дополнительные данные не требуются (пример: изменение в расписании)
    private PendingIntent createIntent(Class targetClass, String[] extraDataKeys, String destination) {
        // создание Intent для задания действия при нажатии на оповещение
        Intent notifyIntent = new Intent(this, targetClass);
        notifyIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);

        // добавляем дополнительные данные в Intent, если такие были переданы
        // наличие таковых данных определяется по типу сообщения
        if (extraDataKeys != null) {
            for (String key : extraDataKeys) {
                notifyIntent.putExtra(key, currentMessage.getData().get(key));
            }
        }

        if(destination != null){
            notifyIntent.putExtra("destination", destination);
        }

        // возвращаем PendingIntent, сформированный на базе notifyIntent
        // TODO: выяснить необходимость изменять requestCode
        return PendingIntent.getActivity(this, 0,
                notifyIntent, PendingIntent.FLAG_UPDATE_CURRENT);
    }

    @Override
    public void onNewToken(String s) {
        DbManager manager = new DbManager(getApplicationContext());
        manager.addFirebaseToken(s);
        Log.d("CurrentToken", "new token: " + s);
    }
}
