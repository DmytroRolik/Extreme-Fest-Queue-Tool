package com.example.goron.diplomadmin;

import android.support.design.widget.TabLayout;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;

import com.example.goron.diplomadmin.Manager.SerializableManager;
import com.example.goron.diplomadmin.Model.Setting;

public class SettingActivity extends AppCompatActivity {

    TabLayout tabTheme;
    Setting setting;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_setting);


        tabTheme = findViewById(R.id.tabTheme);


        // Если объект с настройками пуст, заполняем его из файла
        if(setting == null) {
            setting = SerializableManager.readSerializableObject(this, "Setting.ser");
        }

        if (setting == null){
            setting = new Setting();
        }


        TabLayout.Tab tab = tabTheme.getTabAt((setting.getColor()));
        tab.select();



        // Выбрать цвет фона и сохранить его в файл
        tabTheme.addOnTabSelectedListener(new TabLayout.BaseOnTabSelectedListener() {
            @Override
            public void onTabSelected(TabLayout.Tab tab) {
                setting.setColor(tab.getPosition());
                SerializableManager.saveSerializableObject(getApplicationContext(), setting, "Setting.ser");
            }

            @Override
            public void onTabUnselected(TabLayout.Tab tab) {}

            @Override
            public void onTabReselected(TabLayout.Tab tab) {}
        });

    }
}
