package com.example.goron.diplomadmin.Model;

import java.io.Serializable;

public class Login implements Serializable {

    private String name;
    private String password;
    private boolean check;

    public Login(String name, String password, boolean check) {
        this.name = name;
        this.password = password;
        this.check = check;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public boolean isCheck() {
        return check;
    }

    public void setCheck(boolean check) {
        this.check = check;
    }
}
