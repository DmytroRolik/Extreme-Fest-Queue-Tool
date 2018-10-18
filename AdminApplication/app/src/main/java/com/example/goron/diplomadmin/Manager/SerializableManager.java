package com.example.goron.diplomadmin.Manager;

import android.content.Context;

import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.util.List;

// Класс для сериализации записи файла, десириализации чтения объекта или коллекции объектов

public class SerializableManager {


    // Сериализации записи и сохранения в файл  колекции объектов
    public static <T extends Serializable> void saveSerializable(Context context, List<T> objectToSave, String fileName) {
        try {
            FileOutputStream fileOutputStream = context.openFileOutput(fileName, Context.MODE_PRIVATE);
            ObjectOutputStream objectOutputStream = new ObjectOutputStream(fileOutputStream);

            objectOutputStream.writeObject(objectToSave);

            objectOutputStream.close();
            fileOutputStream.close();

        } catch (IOException e) {
            e.printStackTrace();
        }
    }//saveSerializable


    // Десириализация чтение из файла  колекции объектов
    public static<T extends Serializable> List<T> readSerializable(Context context, String fileName) {
        List<T> listObjectToReturn = null;

        try {
            FileInputStream fileInputStream = context.openFileInput(fileName);
            ObjectInputStream objectInputStream = new ObjectInputStream(fileInputStream);
            listObjectToReturn = (List<T>) objectInputStream.readObject();

            objectInputStream.close();
            fileInputStream.close();

        } catch (IOException | ClassNotFoundException e) {
            e.printStackTrace();
        }

        return listObjectToReturn;
    }


    // Сериализации записи и сохранения в файл объекта
    public static <T extends Serializable> void saveSerializableObject(Context context, T objectToSave, String fileName) {
        try {
            FileOutputStream fileOutputStream = context.openFileOutput(fileName, Context.MODE_PRIVATE);
            ObjectOutputStream objectOutputStream = new ObjectOutputStream(fileOutputStream);

            objectOutputStream.writeObject(objectToSave);

            objectOutputStream.close();
            fileOutputStream.close();

        } catch (IOException e) {
            e.printStackTrace();
        }
    }//saveSerializableObject


    // Десириализация чтение из файла  объекта
    public static<T extends Serializable> T readSerializableObject(Context context, String fileName) {
        T ObjectToReturn = null;

        try {
            FileInputStream fileInputStream = context.openFileInput(fileName);
            ObjectInputStream objectInputStream = new ObjectInputStream(fileInputStream);
            ObjectToReturn = (T) objectInputStream.readObject();

            objectInputStream.close();
            fileInputStream.close();

        } catch (IOException | ClassNotFoundException e) {
            e.printStackTrace();
            return null;
        }

        return ObjectToReturn;
    }//readSerializableObject
}
