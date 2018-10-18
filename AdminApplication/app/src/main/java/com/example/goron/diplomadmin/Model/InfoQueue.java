package com.example.goron.diplomadmin.Model;

import java.util.List;

// Класс Информация об очереди {Поля: успех, класс с двумя полями: длина очереди и среднее время}

public class InfoQueue {

    boolean success;
    Info info;

    public InfoQueue(boolean success, Info info) {
        this.success = success;
        this.info = info;
    }

    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public Info getInfo() {
        return info;
    }

    public void setInfo(Info info) {
        this.info = info;
    }

    public class Info{

        private int length;
        private int averageTime;

        public Info(int length, int averageTime) {
            this.length = length;
            this.averageTime = averageTime;
        }

        public int getLength() {
            return length;
        }

        public void setLength(int length) {
            this.length = length;
        }

        public int getAverageTime() {
            return averageTime;
        }

        public void setAverageTime(int averageTime) {
            this.averageTime = averageTime;
        }
    }

}
