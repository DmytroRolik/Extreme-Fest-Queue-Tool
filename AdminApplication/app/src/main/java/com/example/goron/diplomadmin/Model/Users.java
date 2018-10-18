package com.example.goron.diplomadmin.Model;

import com.google.gson.annotations.SerializedName;

// Класс пользователь {Поля: ид, имя, фамилия, номер, паспорт, время}

public class Users {

    private int id;
    @SerializedName("name")
    private String name;
    @SerializedName("surname")
    private String surname;
    @SerializedName("number")
    private String number;
    @SerializedName("passport")
    private String passport;
    private long timestamp;

    // Конструктор с параметрами
    public Users(int id, String name, String surname, String number, String passport, long timestamp) {
        this.id = id;
        this.name = name;
        this.surname = surname;
        this.number = number;
        this.passport = passport;
        this.timestamp = timestamp;
    }//Users

    public Users(String name, String surname, String number, String passport) {

        this.name = name;
        this.surname = surname;
        this.number = number;
        this.passport = passport;

    }//Users


    // Геттеры и сеттреры
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getSurname() {
        return surname;
    }

    public void setSurname(String surname) {
        this.surname = surname;
    }

    public String getNumber() {
        return number;
    }

    public void setNumber(String number) {
        this.number = number;
    }

    public String getPassport() {
        return passport;
    }

    public void setPassport(String passport) {
        this.passport = passport;
    }

    public long getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(long timestamp) {
        this.timestamp = timestamp;
    }
}
