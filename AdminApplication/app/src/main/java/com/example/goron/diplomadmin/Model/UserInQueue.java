package com.example.goron.diplomadmin.Model;

// Класс пользователи в очереди {Поля: успех, коллекция пользователей}

import java.util.List;

public class UserInQueue {

    private boolean success;
    private List<Users> users;


    // Конструктор с параметрами
    public UserInQueue(boolean success, List<Users> users) {
        this.success = success;
        this.users = users;
    }//UserInQueue


    // Геттеры и сеттеры
    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public List<Users> getUsers() {
        return users;
    }

    public void setUsers(List<Users> users) {
        this.users = users;
    }

}
