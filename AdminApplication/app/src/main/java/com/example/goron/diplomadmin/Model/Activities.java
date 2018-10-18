package com.example.goron.diplomadmin.Model;

import java.util.Date;

// Класс расписания {Поля: ид, дата, время начала, время окончания, имя}

public class Activities {


    private int id;
    private Date date;
    private String start_time;
    private String end_time;
    private String name;


    // Конструктор с параметрами
    public Activities(int id, Date date, String start_time, String end_time, String name) {
        this.id = id;
        this.date = date;
        this.start_time = start_time;
        this.end_time = end_time;
        this.name = name;
    }//Activities



    //Сеттеры и геттеры

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public String getStart_time() {
        return start_time;
    }

    public void setStart_time(String start_time) {
        this.start_time = start_time;
    }

    public String getEnd_time() {
        return end_time;
    }

    public void setEnd_time(String end_time) {
        this.end_time = end_time;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }
}
