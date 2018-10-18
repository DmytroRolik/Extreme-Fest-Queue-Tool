package com.example.goron.userdiplom.Model;

public class PostTokken {

    private boolean success;


    public PostTokken(boolean success) {
        this.success = success;
    }

    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }
}
