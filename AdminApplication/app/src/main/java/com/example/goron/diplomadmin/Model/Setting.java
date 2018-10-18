package com.example.goron.diplomadmin.Model;

import com.example.goron.diplomadmin.R;

import java.io.Serializable;

public class Setting implements Serializable {

    //private int animation;
    private int color = 0;

    public Setting() {
    }

    public Setting(int color) {
        this.color = color;
    }


    public int getColor() {
        return color;
    }

    public void setColor(int color) {
        this.color = color;
    }



    public int getColorId(){

        switch (color){

            case 0: return R.color.mainGreen;
            case 1: return R.color.mainBlue;
            case 2: return R.color.mainBlack;
        }
        return R.color.mainGreen;
    }


    public boolean blackWhite(){
        if(color == 0)return true;
        return false;
    }


    public int getItemTopLayout(){

        switch (color){

            case 0: return R.drawable.top_layout_card_green;
            case 1: return R.drawable.top_layout_card_blue;
            case 2: return R.drawable.top_layout_card_black;
        }
        return R.drawable.top_layout_card_green;
    }
}
