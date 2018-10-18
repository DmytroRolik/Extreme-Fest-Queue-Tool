package com.example.goron.diplomadmin.Fragments;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.design.widget.TabLayout;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.example.goron.diplomadmin.Manager.SerializableManager;
import com.example.goron.diplomadmin.Model.Setting;
import com.example.goron.diplomadmin.R;


public class SettingFragment extends Fragment {

    TabLayout tabTheme;
    Setting setting;

    public SettingFragment() {
        // Required empty public constructor
    }




    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_setting, container, false);


        tabTheme = view.findViewById(R.id.tabTheme);


        // Если объект с настройками пуст, заполняем его из файла
        if(setting == null) {
            setting = SerializableManager.readSerializableObject(getContext(), "Setting.ser");
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
                SerializableManager.saveSerializableObject(getContext(), setting, "Setting.ser");
            }

            @Override
            public void onTabUnselected(TabLayout.Tab tab) {}

            @Override
            public void onTabReselected(TabLayout.Tab tab) {}
        });

        return view;
    }




}
