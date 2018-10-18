package com.example.goron.diplomadmin.Manager;

import android.content.Context;
import android.database.Cursor;
import android.util.Log;

import com.example.goron.diplomadmin.Database.DatabaseHelper;
import com.example.goron.diplomadmin.Model.Schedule;

import java.io.IOException;
import java.sql.Date;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

public class DbManager {
    private DatabaseHelper helper;

    public DbManager(Context context) {
        this.helper = new DatabaseHelper(context);
    }// ctor

    // выполнить запрос для получения данных
    private Cursor getData(String query){
        try{
            helper.createDb();
            helper.openDb();

            return helper.getData(query);
        } catch (IOException ex){
            Log.d("myData", ex.getMessage());
            throw new Error("Oops, manager don't work");
        }
    }// getData

    // выполнить запрос без возврата данных
    private void executeQuery(String query){
        try{
            helper.createDb();
            helper.openDb();

            helper.execute(query);
        } catch (IOException ex){
            Log.d("myData", ex.getMessage());
            throw new Error("Oops, manager don't work");
        }
    }// executeQuery

    // получить данные для входа пользователя
    public HashMap<String,String> getUserData(){
        String query = "select * from "
                + DatabaseHelper.TABLE_USER_DATA + ";";

        Cursor raw = getData(query);
        HashMap<String, String> result = new HashMap<>();

        for (raw.moveToFirst(); !raw.isAfterLast(); raw.moveToNext()) {
            result.put("login", raw.getString(1));
            result.put("password", raw.getString(2));
        }// for

        helper.close();
        return result;
    }// getUserData

    // удалить данные о пользователе (при logout)
    public void deleteUserData(){
        String query = "delete from " + DatabaseHelper.TABLE_USER_DATA + ";";

        executeQuery(query);

        helper.close();
    }// deleteUserData

    // добавить данные о пользователе (при login)
    public void addUserData(String login, String password){
        String query = "insert into " + DatabaseHelper.TABLE_USER_DATA
                + "(name,password) values ('" + login + "', '" + password + "');";

        executeQuery(query);

        helper.close();
    }// addUserData

    // были ли изменения на сервере в данной активности/фрагменте
    public boolean isDataChanged(int controlId){
        String query = "select _id, classId, loadFromServer from "
                + DatabaseHelper.TABLE_CHANGES
                + " where classId = " + controlId + ";";

        Cursor raw = getData(query);
        raw.moveToFirst();

        int result = raw.getInt(2);

        helper.close();
        return result != 0;
    }// isDataChanged

    // изменить необходимость загрузки данных из сервера для активности/фрагмента
    public void editDataChanged(int contolId, boolean loadFromServer){
        String value = loadFromServer? "1" : "0";
        String query = "update " + DatabaseHelper.TABLE_CHANGES
                + " set loadFromServer = " + value
                + " where classId = " + contolId;

        executeQuery(query);

        helper.close();
    }// editDataChanged

    // получить расписание из БД
    public List<Schedule> getSchedule(){
        String query = "select * from "
                + DatabaseHelper.TABLE_SCHEDULE
                + ";";

        Cursor raw = getData(query);
        List<Schedule> result = new ArrayList<>();
        Schedule item;

        for (raw.moveToFirst(); !raw.isAfterLast(); raw.moveToNext()) {
            item = new Schedule(
                    raw.getInt(raw.getColumnIndex("activityId")),
                    Date.valueOf(raw.getString(raw.getColumnIndex("date"))),
                    raw.getString(raw.getColumnIndex("startsAt")),
                    raw.getString(raw.getColumnIndex("endsAt")),
                    raw.getString(raw.getColumnIndex("activityName")),
                    null,null,null
            );

            result.add(item);
        }// for

        helper.close();

        return result;
    }// getSchedule

    // изменить расписание
    public void changeSchedule(List<Schedule> newSchedule){
        deleteOldSchedule();
        addNewSchedule(newSchedule);

        helper.close();
    }// changeSchedule

    // удалить старое расписание
    private void deleteOldSchedule(){
        String query = "delete from " + DatabaseHelper.TABLE_SCHEDULE;

        executeQuery(query);
    }// deleteOldSchedule

    // добавить новое расписание
    private void addNewSchedule(List<Schedule> newSchedule){
        StringBuilder query = new StringBuilder("insert into " + DatabaseHelper.TABLE_SCHEDULE
                + " (activityName, activityId, startsAt, endsAt, date) values ");

        for (Schedule item : newSchedule) {
            if(newSchedule.indexOf(item) != 0){
                query.append(", ");
            }// if

            query.append("('");
            query.append(item.getName());
            query.append("', '");
            query.append(item.getId());
            query.append("', '");
            query.append(item.getStart_time());
            query.append("', '");
            query.append(item.getEnd_time());
            query.append("', '");
            query.append(item.getDate());
            query.append("')");
        }// for

        query.append(";");

        executeQuery(query.toString());
    }// addNewSchedule

    // добавить токен для Firebase
    public void addFirebaseToken(String token){
        String selectQuery = "select * from " + DatabaseHelper.TABLE_TOKENS + ";";
        Cursor tokens = getData(selectQuery);

        if(tokens.getCount() != 0){
            deleteFirebaseToken();
        }// if

        String addQuery = "insert into " + DatabaseHelper.TABLE_TOKENS
                + "(token) values ('" + token + "');";

        executeQuery(addQuery);

        helper.close();
    }// addFirebaseToken

    // удалить токен для Firebase
    private void deleteFirebaseToken(){
        String query = "delete from " + DatabaseHelper.TABLE_TOKENS + ";";

        executeQuery(query);
    }// deleteFirebaseToken

    // получить токен для Firebase
    public String getFirebaseToken(){
        String query = "select * from " + DatabaseHelper.TABLE_TOKENS + ";";

        Cursor raw = getData(query);
        raw.moveToFirst();

        String token = raw.getString(raw.getColumnIndex("token"));

        helper.close();

        return token;
    }// getFirebaseToken
}// DbManager
