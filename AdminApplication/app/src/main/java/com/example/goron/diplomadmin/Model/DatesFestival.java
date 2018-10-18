package com.example.goron.diplomadmin.Model;


// Класс Даты фестиваля {Поля: успех, коллекция дат}

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

public class DatesFestival {

    private boolean success;
    private List<Date> dates;

    // Конструктор с параметрами
    public DatesFestival(boolean success, List<Date> dates) {
        this.success = success;
        this.dates = dates;
    }//DatesFestival


    // Геттеры и сеттеры
    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public List<Date> getDates() {
        return dates;
    }

    public void setDates(List<Date> dates) {
        this.dates = dates;
    }


    public String[] getStringDate(){

        String[] dateArray = new String[dates.size()];

        for(int i = 0; i < dates.size(); i++){
            dateArray[i] = new SimpleDateFormat("yyyy-MM-dd").format(dates.get(i));
        }
        return  dateArray;
    }
}
