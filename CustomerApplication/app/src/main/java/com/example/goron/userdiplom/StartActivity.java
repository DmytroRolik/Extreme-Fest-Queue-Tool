package com.example.goron.userdiplom;

import android.content.Intent;
import android.content.res.Configuration;
import android.support.annotation.NonNull;
import android.support.design.widget.NavigationView;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.view.GravityCompat;
import android.support.v4.view.ViewPager;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.MenuItem;
import android.widget.FrameLayout;
import android.widget.Toast;

import com.example.goron.userdiplom.Fragments.ActivityFragment;
import com.example.goron.userdiplom.Fragments.MenuFragment;
import com.example.goron.userdiplom.Fragments.MyQueueFragment;
import com.example.goron.userdiplom.Fragments.ScheduleFragment;
import com.example.goron.userdiplom.Manager.DbManager;

public class StartActivity extends AppCompatActivity {

    FrameLayout frameLayout;
    DrawerLayout drawerLayout;
    Toolbar toolbar;
    NavigationView navigationView;
    ViewPager viewpager;

    //Фрагмент меню
    MenuFragment menuFragment;


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


        toggle.getDrawerArrowDrawable().setColor(getResources().getColor(R.color.white));


        drawerLayout.addDrawerListener(toggle);


        toggle.syncState();

        // Переход по NavigationView
        navigationMenu();


        if (getResources().getConfiguration().orientation
                == Configuration.ORIENTATION_LANDSCAPE || getSupportFragmentManager().getBackStackEntryCount() > 1) {
            // If the screen is now in landscape mode, we can show the
            // dialog in-line with the list so we don't need this activity.

            return;
        } else {
            Toast.makeText(getApplicationContext(), "Добро пожаловать", Toast.LENGTH_LONG).show();

            Bundle extras = getIntent().getExtras();

            if(extras == null || extras.get("destination") == null) {
                menuFragment = MenuFragment.newInstance();
                showFragment(menuFragment, "menu");
            } else {
                String destination = extras.get("destination").toString();
                Log.d("notificationsDebug", "destination - " + destination);

                switch (destination){
                    case "schedule":
                        ScheduleFragment scheduleFragment = ScheduleFragment.newInstance();
                        showFragment(scheduleFragment, null);
                        break;
                    case "queues":
                        MyQueueFragment myQueueFragment = MyQueueFragment.newInstance();
                        showFragment(myQueueFragment, null);
                        break;
                    default:
                        menuFragment = MenuFragment.newInstance();
                        showFragment(menuFragment, "menu");
                        break;
                }// switch
            }// if-else
        }// if-else


    }


    private void showFragment(Fragment fragment, String tag){
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
        if (drawerLayout.isDrawerOpen(GravityCompat.START))  drawerLayout.closeDrawer(GravityCompat.START);
        else if (count == 1) {
           // super.onBackPressed();
//            Log.d("FragmentClass",getSupportFragmentManager().findFragmentByTag("menu").toString());
            if(getSupportFragmentManager().findFragmentByTag("menu") == null){
                getSupportFragmentManager().popBackStack();
                menuFragment = MenuFragment.newInstance();
                showFragment(menuFragment, "menu");
            } else {
                moveTaskToBack(true);
            }

        }else {
            getSupportFragmentManager().popBackStack();
        }

    }


    // Переход по NavigationView
    private void navigationMenu() {

        navigationView.setNavigationItemSelectedListener(new NavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem menuItem) {

                int id = menuItem.getItemId();
                Intent intent;
                switch (id) {


                    case R.id.activitys:
                        showFragment(ActivityFragment.newInstance(), null);
                        drawerLayout.closeDrawer(GravityCompat.START);
                        break;




                    case R.id.schedule:
                        showFragment(ScheduleFragment.newInstance(), null);
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


                    case R.id.myqueues:
                        showFragment(MyQueueFragment.newInstance(), null);
                        drawerLayout.closeDrawer(GravityCompat.START);
                        break;
                }
                return false;
            }
        });
    }
}
