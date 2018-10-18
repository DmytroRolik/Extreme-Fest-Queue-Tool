package com.example.goron.diplomadmin.Model;

import java.io.Serializable;
import java.sql.Time;
import java.util.Date;
import java.util.List;

// Класс расписание для фестиваля, c подробной информацией о активности (Поля: ид, дата, время начала, время окончания, имя, описание, главное фото, коллекция фото)

public class Schedule implements Serializable {

    private int id;
    private Date date;
    private String start_time;
    private String end_time;
    private String name;
    private String description;
    private String main_photo;
    private List<String> photos;



    // Конструктор с параметрами
    public Schedule(int id,  Date date, String start_time, String end_time, String name, String description, String main_photo, List<String> photos) {
        this.id = id;
        this.date = date;
        this.start_time = start_time;
        this.end_time = end_time;
        this.name = name;
        this.description = description;
        this.main_photo = main_photo;
        this.photos = photos;
    }

    // Геттеры и сеттеры
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

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

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getMain_photo() {
        return main_photo;
    }

    public void setMain_photo(String main_photo) {
        this.main_photo = main_photo;
    }

    public List<String> getPhotos() {
        return photos;
    }

    public void setPhotos(List<String> photos) {
        this.photos = photos;
    }


}
