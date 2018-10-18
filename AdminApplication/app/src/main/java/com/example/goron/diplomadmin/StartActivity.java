package com.example.goron.diplomadmin;

import android.app.ProgressDialog;
import android.content.Intent;
import android.content.res.Configuration;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.support.annotation.NonNull;
import android.support.design.widget.NavigationView;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.view.GravityCompat;
import android.support.v4.view.KeyEventDispatcher;
import android.support.v4.view.ViewPager;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.KeyEvent;
import android.view.MenuItem;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.SimpleCursorAdapter;
import android.widget.Toast;

import com.example.goron.diplomadmin.Fragments.AboutActivitiesFragment;
import com.example.goron.diplomadmin.Fragments.ActivitiesFragment;
import com.example.goron.diplomadmin.Fragments.ActivityFragment;
import com.example.goron.diplomadmin.Fragments.QueueFragment;
import com.example.goron.diplomadmin.Fragments.ScheduleFragment;
import com.example.goron.diplomadmin.Fragments.SettingFragment;
import com.example.goron.diplomadmin.Manager.DbManager;
import com.example.goron.diplomadmin.Manager.SerializableManager;
import com.example.goron.diplomadmin.Model.Activities;
import com.example.goron.diplomadmin.Model.Setting;

public class StartActivity extends AppCompatActivity {

    FrameLayout frameLayout;
    DrawerLayout drawerLayout;
    Toolbar toolbar;
    NavigationView navigationView;
    ViewPager viewpager;


    Setting setting;


    private static String FRAGMENT_INSTANCE_NAME = "ActivitiesFragment";


    FragmentTransaction fragmentTransaction;

    // Фрагмент с активностями
    ActivityFragment activityfragment;

    // Фрагмент с расписанием
    ScheduleFragment scheduleFragment;

    Fragment fragment;

    // Фрагмент с настройками
    SettingFragment settingFragment;



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_start);


        // Инициализируем элементы:
        frameLayout = findViewById(R.id.content_frame);
        drawerLayout = findViewById(R.id.drawer);
        toolbar = findViewById(R.id.toolBar);
        navigationView = findViewById(R.id.navView);
        viewpager = findViewById(R.id.viewpager);


        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setTitle(null);

        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawerLayout, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);


        drawerLayout.addDrawerListener(toggle);
        toggle.syncState();

        Bundle arguments = getIntent().getExtras();

        // определяем, было ли нажатие на уведомление или был обычный вход
        if (arguments == null || arguments.get("destination") == null){
            activityfragment= ActivityFragment.newInstance();
            showFragment(activityfragment, "menu");
        } else {
            String destination = arguments.get("destination").toString();
            Log.d("notificationsDebug", "destination - " + destination);
            switch (destination){
                case "schedule":
                    scheduleFragment = ScheduleFragment.newInstance();
                    showFragment(scheduleFragment, null);
                    break;
                case "queue":
                    QueueFragment queueFragment = QueueFragment.newInstance(
                            Integer.parseInt(arguments.get("activityId").toString()),
                            arguments.get("activityName").toString()
                    );
                    showFragment(queueFragment, null);
                    break;
            }
        }



        // Заполняем объект с настройками из файла
        setting = SerializableManager.readSerializableObject(getApplicationContext(), "Setting.ser");


        if (setting != null) {
            toolbar.setBackgroundResource(setting.getColorId());
        }

        navigationMenu();

        if (getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE ||
                                         getSupportFragmentManager().getBackStackEntryCount() > 1) {
            return;
        } else {
            Toast.makeText(getApplicationContext(), "Добро пожаловать", Toast.LENGTH_LONG).show();
            activityfragment = ActivityFragment.newInstance();
            showFragment(activityfragment, "menu");
        }


    }


    private void showFragment(Fragment fragment, String tag) {
        FragmentTransaction fragmentTransaction = getSupportFragmentManager().beginTransaction();
        if(tag == null)
            fragmentTransaction.replace(R.id.content_frame, fragment).addToBackStack(null).commit();
        else
            fragmentTransaction.replace(R.id.content_frame, fragment, tag).addToBackStack(null).commit();
    }//showFragment


    @Override
    public void onBackPressed() {
        int count = getSupportFragmentManager().getBackStackEntryCount();

            // Если открыто боковое меню - закрываем его, Если в стеке последний фрагмент закрываем активность иначе возвращаемся к предыдущему фрагменту
            if (drawerLayout.isDrawerOpen(GravityCompat.START))
                drawerLayout.closeDrawer(GravityCompat.START);
            else if (count == 1) {
                if(getSupportFragmentManager().findFragmentByTag("menu") == null){
                    getSupportFragmentManager().popBackStack();
                    activityfragment = ActivityFragment.newInstance();
                    showFragment(activityfragment, "menu");
                } else{
                    if(activityfragment != null) {
                        if (activityfragment.callDate != null) {
                            activityfragment.callDate.cancel();
                        }
                    }
                    moveTaskToBack(true);
                }
            } else {
                getSupportFragmentManager().popBackStack();
            }

    }//onBackPressed


    // Переход по NavigationView
    private void navigationMenu() {

        navigationView.setNavigationItemSelectedListener(new NavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem menuItem) {

                int id = menuItem.getItemId();
                Intent intent;
                switch (id) {

                    case R.id.activities:
                        ActivitiesFragment activitiesFragment = ActivitiesFragment.newInstance();

                        showFragment(activitiesFragment, "menu");

                        drawerLayout.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.schedule:
                          scheduleFragment = ScheduleFragment.newInstance();
                          showFragment(scheduleFragment, null);
                          drawerLayout.closeDrawer(GravityCompat.START);
                          break;

                    case R.id.exit:

                        DbManager dbManager = new DbManager(getApplicationContext());
                        dbManager.deleteUserData();

                        intent = new Intent(getApplicationContext(), MainActivity.class);
                        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                        intent.putExtra("CloseApp", true);
                        startActivity(intent);

                        break;
                }
                return false;
            }
        });
    }


}
