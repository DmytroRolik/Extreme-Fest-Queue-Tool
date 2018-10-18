package com.example.goron.diplomadmin.Model;


import java.io.Serializable;
import java.util.List;

// Класс работник {Поля: имя, фамилия, ид роль, коллекция разрешение на активности(Ид), коллекция разрешение на сервис(Ид) }
public class Employee implements Serializable {


    private String name;
    private String surname;
    private int role_id;
    private List<Integer> permission_to_activities;
    private List<Integer> permission_to_service;


    // Конструктор с параметрами
    public Employee(String name, String surname, int role_id, List<Integer> permission_to_activities, List<Integer> permission_to_service) {
        this.name = name;
        this.surname = surname;
        this.role_id = role_id;
        this.permission_to_activities = permission_to_activities;
        this.permission_to_service = permission_to_service;
    }



    // Геттеры и сеттеры
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

    public int getRole_id() {
        return role_id;
    }

    public void setRole_id(int role_id) {
        this.role_id = role_id;
    }

    public List<Integer> getPermission_to_activities() {
        return permission_to_activities;
    }

    public void setPermission_to_activities(List<Integer> permission_to_activities) {
        this.permission_to_activities = permission_to_activities;
    }

    public List<Integer> getPermission_to_service() {
        return permission_to_service;
    }

    public void setPermission_to_service(List<Integer> permission_to_service) {
        this.permission_to_service = permission_to_service;
    }
}
