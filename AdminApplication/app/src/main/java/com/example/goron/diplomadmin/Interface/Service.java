package com.example.goron.diplomadmin.Interface;


import com.example.goron.diplomadmin.Model.AccountInformation;
import com.example.goron.diplomadmin.Model.Activities;
import com.example.goron.diplomadmin.Model.DatesFestival;
import com.example.goron.diplomadmin.Model.InfoQueue;
import com.example.goron.diplomadmin.Model.PermissionAll;
import com.example.goron.diplomadmin.Model.Schedule;
import com.example.goron.diplomadmin.Model.UserInQueue;
import com.example.goron.diplomadmin.Model.Users;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.PATCH;
import retrofit2.http.POST;
import retrofit2.http.Path;
import retrofit2.http.Query;


public interface Service {


    ////
    ////service
    ////


    // http://example.com/api/v1/service/service-permissions
    // Возвращает полный список всех разрешений существующих в сервисе (название и идентификатор).
    @GET("service/service")
    Call<PermissionAll> getAllPermission();


    // http://example.com/api/v1/users/
    // Еще не пробывал в работе (необходимо передавать поля name. surname, number, password ) необходимо из класса users исключить для передачи POST id , timestamp
    // Регистрирует нового пользователя в системе. Поле number является логином пользователя, поле passport его паролем
    @POST("service/users")
    Call<Void> registrationUser(@Body Users users);


    // http://example.com/api/v1/service/schedule?date=2018-05-02
    // Возвращает расписание для фестиваля, c подробной информацией о каждой активности на все дни, если не указан параметр date
    @GET("service/schedule")
    Call<List<Schedule>> getSchedule();


    // http://example.com/api/v1/service/dates
    // Возвращает массив дат фестиваля отсортированный по возрастанию
    @GET("service/dates")
    Call<DatesFestival> getDatesFestival();





    ////
    ////admin
    ////


    // http://example.com/api/v1/admin/self
    // Возвращает полную информацию о текущем аккаунте администратора, включая список доступных ему разрешений на активности и сервис.
    @GET("admin/self")
    Call<AccountInformation> getEmployeePermission();



    ////
    ////queue
    ////


    // http://example.com/api/v1/service/dates
    // Возвращает массив активностей
    @GET("queue/activities?date")
    Call<List<Activities>> getActivities(@Query("date") String date);


    // http://example.com/api/v1/queue/activities/1/users?count=1
    // Возвращает список пользователй, зарегестрированных в очереди и отсортированных по убыванию  (count количество человек)
    @GET("queue/activities/{activityId}/users?count=1")
    Call<UserInQueue> getUserInQueue(@Path("activityId") int activityId);


    // http://example.com/api/v1/queue/activities/1/users/5?delete_method=delete
    // Удаляет пользователя из очереди на активность, используя заданный статус заданный в параметре delete_method
    // (по умолчанию используется статус STEP_OUT, что обозначает успешное прохождение очереди пользователем)
    @DELETE("queue/activities/{activityId}/users/{userId}")
    Call<Void> deleteUserInQueue(@Path("activityId") int activityId, @Path("userId") int userId);


    // http://example.com/api/v1/queue/activities/1/users/5
    // Отмечает пользователя, как опоздавшего. Сдвигает опоздавшего пользователя на заданное в админ. панели число позиций.
    // В случае если очередной вызов данного метода делает число опозданий пользователя большим, чем максимально возможное (устанавливается администрацией)
    // пользователь удаляется из очереди со статусом EXCLUDED
    @PATCH("queue/activities/{activityId}/users/{userId}")
    Call<Void> latenessUser(@Path("activityId") int activityId, @Path("userId") int userId);



    // http://example.com/api/v1/queue/activities/1
    // Возвращает информацию об очереди на активность. Параметр length указывает какое количество людей находится в очереди на текущий момент.
    // Параметр averageTime показывает среднее время прохождения очереди одним человеком.
    // В случае если на активности 30 минут нет обновлений, время сбрасывается, и будет возвращено значение -1.
    // Минимальное количество пользователей, после которых будет сформировано среднее время - 3
    @GET("queue/activities/{activityId}")
    Call<InfoQueue> queueInfo(@Path("activityId") int activityId);


    // http://example.com/api/v1/queue/activities/1/users
    // Возвращает список пользователй, зарегестрированных в очереди и отсортированных по убыванию  (count количество человек)
    @GET("queue/activities/{activityId}/users")
    Call<UserInQueue> getAllUserInQueue(@Path("activityId") int activityId);







    ////
    //// ЕЩЕ НЕ ОПРОБЫВАЛ
    ////


    // http://example.com/api/v1/queue/activities/1/users/self
    // Возвращает позицию текущего аутентифицированного пользователя в очереди
    // @GET("/queue/{activityId}/users/self")
    // Call<Position> getPosition(@Path("activityId") int activityId)





}

