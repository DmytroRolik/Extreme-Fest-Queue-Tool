package com.example.goron.diplomadmin.Database;

import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

public class DatabaseHelper extends SQLiteOpenHelper {
    private static String DB_PATH; // полный путь к базе данных
    private static String DB_NAME = "adminDataStorage.db";
    private static final int SCHEMA = 1; // версия базы данных

    public static final String TABLE_TOKENS = "tokens";
    public static final String TABLE_SCHEDULE = "schedule";
    public static final String TABLE_USER_DATA = "user_data";
    public static final String TABLE_CHANGES = "data_changes_check";
    public static final String TABLE_SETTINGS = "settings";

    private final Context myContext;

    private SQLiteDatabase myDb;

    private String getFullDbPath(){
        return DB_PATH + DB_NAME;
    }// getFullDbPath

    public Cursor getData(String query){
        return myDb.rawQuery(query, null);
    }// getData

    public void execute(String query){
        myDb.execSQL(query);
    }// execute

    public DatabaseHelper(Context context){
        super(context, DB_NAME, null, SCHEMA);
        DB_PATH = context.getDatabasePath(DB_NAME).getPath();
        this.myContext = context;
    }// ctor

    // если базы нет - копируем ее из папки assets
    public void createDb() throws IOException{
        boolean dbExist = checkDb();
        if(!dbExist){
            this.getReadableDatabase();
            this.close();
            try {
                copyDb();
            }
            catch (IOException myIOException){
                throw new Error("CopyDbFail");
            }// try-catch
        }// if
    }// createDb

    // проверка, существует ли БД
    private boolean checkDb(){
        File dbFile = new File(getFullDbPath());
        return dbFile.exists();
    }// checkDb

    //копируем бд из папки assets
    private void copyDb() throws IOException{
        InputStream inputStream = myContext.getAssets().open(DB_NAME);
        OutputStream outputStream = new FileOutputStream(getFullDbPath());
        byte[] buffer = new byte[1024];
        int length;

        while ((length = inputStream.read(buffer))>0){
            outputStream.write(buffer,0,length);
        }

        outputStream.flush();
        outputStream.close();
        inputStream.close();
    }// copyDb

    // открыть бд для выполнения запросов
    public boolean openDb() throws SQLException{
        myDb = SQLiteDatabase.openDatabase(getFullDbPath(), null, SQLiteDatabase.OPEN_READWRITE);

        return myDb != null;
    }// openDb

    // освобождение ресурсов
    @Override
    public synchronized void close() {
        if(myDb != null) myDb.close();
        super.close();
    }

    @Override
    public void onCreate(SQLiteDatabase sqLiteDatabase) {
    }

    @Override
    public void onUpgrade(SQLiteDatabase sqLiteDatabase, int i, int i1) {
    }
}// DatabaseHelper
