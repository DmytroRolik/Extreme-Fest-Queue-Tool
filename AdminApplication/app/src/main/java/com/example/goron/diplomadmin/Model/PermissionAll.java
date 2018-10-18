package com.example.goron.diplomadmin.Model;

// Cписок разрешений
// Полный список всех разрешений существующих в сервисе (название и идентификатор).
// (Поля: (успех, коллекция разрешений))

import java.util.List;

public class PermissionAll {

    private boolean success;
    private List<Permissions>  permissions;

    // Конструктор с параметрами
    public PermissionAll(boolean success, List<Permissions> permissions) {
        this.success = success;
        this.permissions = permissions;
    }//PermissionAll


    //Геттеры и сеттеры
    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public List<Permissions> getPermissions() {
        return permissions;
    }

    public void setPermissions(List<Permissions> permissions) {
        this.permissions = permissions;
    }




    // Класс разрешений (Поля: ид, имя)
    private class Permissions{

        private int id;
        private String name;

        // Конструктор с параметрами
        public Permissions(int id, String name) {
            this.id = id;
            this.name = name;
        }

        //Геттеры и сеттеры
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
    }
}
