package com.example.goron.diplomadmin.Model;


// Класс Информация об аккаунте  {Поля: успех, работник}

import java.io.Serializable;

public class AccountInformation implements Serializable {

    private boolean success;
    private Employee user;

    // Конструктор с параметрами
    public AccountInformation(boolean success, Employee user) {
        this.success = success;
        this.user = user;
    }//AccountInformation


    // Геттеры и сеттеры
    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public Employee getUser() {
        return user;
    }

    public void setUser(Employee user) {
        this.user = user;
    }
}
