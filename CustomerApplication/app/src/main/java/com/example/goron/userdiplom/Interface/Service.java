package com.example.goron.userdiplom.Interface;

import com.example.goron.userdiplom.Model.Activities;
import com.example.goron.userdiplom.Model.DatesFestival;
import com.example.goron.userdiplom.Model.InfoQueue;
import com.example.goron.userdiplom.Model.PostTokken;
import com.example.goron.userdiplom.Model.Queue;
import com.example.goron.userdiplom.Model.QueueAddorDelete;
import com.example.goron.userdiplom.Model.RequestTokken;
import com.example.goron.userdiplom.Model.Schedule;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface Service  {



    ////
    ////service
    ////


    @POST("service/token")
    Call<PostTokken> postToken(@Body RequestTokken requestTokken);



    // http://example.com/api/v1/service/dates
    // Возвращает массив дат фестиваля отсортированный по возрастанию
    @GET("service/dates")
    Call<DatesFestival> getDatesFestival();


    // http://example.com/api/v1/queue/activities/1/users/self
    // Информация о текущем пользователе в очереди
    // Возвращает позицию текущего аутентифицированного пользователя в очереди
    @GET("queue/activities/{activityId}/users/self")
    Call<Queue> getMuQueueInActivity(@Path("activityId") int activityId);


    // http://example.com/api/v1/queue/activities?date=2018-05-02
    // Получение списка всех активностей
    // Возвращает все активности с поддержкой очереди. Для того чтобы вернуть активности только на один день, необхоидмо задать необязательный параметр date
    @GET("queue/activities?date")
    Call<List<Activities>> getActivities(@Query("date") String date);


    // http://example.com/api/v1/queue/activities
    // Получение списка всех активностей
    // Возвращает все активности с поддержкой очереди. Для того чтобы вернуть активности только на один день, необхоидмо задать необязательный параметр date
    @GET("queue/activities")
    Call<List<Activities>> getAllActivities();



    // http://example.com/api/v1/queue/activities/1/users/self
    // Удаление текущего пользователя из очереди
    // Удаляет текущего аутентифицированного пользователя из очереди на заданную активность (Используется статус EXIT)
    @DELETE("queue/activities/{activityId}/users/self")
    Call<QueueAddorDelete> deleteMeFromQueue(@Path("activityId") int activityId);


    // http://example.com/api/v1/queue/activities/1/users/self
    // Помещение текущего пользователя в очередь
    // Помещает текущего аутентифицированного пользователя в очередь на заданную активность
    @POST("queue/activities/{activityId}/users/self")
    Call<QueueAddorDelete> addMeFromQueue(@Path("activityId") int activityId);


    // http://example.com/api/v1/service/schedule?date=2018-05-02
    // Возвращает расписание для фестиваля, c подробной информацией о каждой активности на все дни, если не указан параметр date
    @GET("service/schedule")
    Call<List<Schedule>> getSchedule();


    // http://example.com/api/v1/queue/activities/1
    // Информация об очереди
    // Возвращает информацию об очереди на активность. Параметр length указывает какое количество людей находится в очереди на текущий момент.
    // Параметр averageTime показывает среднее время прохождения очереди одним человеком.
    // В случае если на активности 30 минут нет обновлений, время сбрасывается, и будет возвращено значение -1.
    // Минимальное количество пользователей, после которых будет сформировано среднее время - 3
    @GET("queue/activities/{activityId}")
    Call<InfoQueue> getInfoQueue(@Path("activityId") int activityId);
}
