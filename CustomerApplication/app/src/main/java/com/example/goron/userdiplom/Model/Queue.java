package com.example.goron.userdiplom.Model;

public class Queue {

    private boolean success;
    private int position;


    public Queue(int position) {
        this.position = position;
    }


    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public int getPosition() {
        return position;
    }

    public void setPosition(int position) {
        this.position = position;
    }
}
