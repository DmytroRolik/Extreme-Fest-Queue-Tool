package com.example.goron.userdiplom.Fragments;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.res.Configuration;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.example.goron.userdiplom.Adapters.ActivitiesAdapter;
import com.example.goron.userdiplom.Interface.Service;
import com.example.goron.userdiplom.Model.Activities;
import com.example.goron.userdiplom.Model.Schedule;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.Service.ServiceGenerator;


import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;


public class ActivityFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener  {

    private static final int LOCAL_DB_ID = 2;

    RecyclerView recyclerView;
    ActivitiesAdapter activitiesAdapter;
    ProgressDialog mDialog;

    SwipeRefreshLayout refresh;

    private Call<List<Schedule>> callSchedule;

    public ActivityFragment() {
        // Required empty public constructor
    }


    public static ActivityFragment newInstance() {
        ActivityFragment fragment = new ActivityFragment();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {}
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        if(mDialog == null) {
            mDialog = new ProgressDialog(getContext());
            mDialog.setMessage("Загрузка активностей...");
            mDialog.setCancelable(false);
            mDialog.show();
        }else if(!mDialog.isShowing()){
            mDialog.show();

        }



        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_activity, container, false);


        refresh = view.findViewById(R.id.refresh);
        refresh.setOnRefreshListener(this);
        recyclerView = view.findViewById(R.id.recycler);


        if(getResources().getConfiguration().orientation == Configuration.ORIENTATION_PORTRAIT){
            recyclerView.setLayoutManager(new GridLayoutManager(getActivity(),1));
        }else if (getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE){
            recyclerView.setLayoutManager(new GridLayoutManager(getActivity(),2));
        }




        TextView header =  getActivity().findViewById(R.id.textHeader);
        header.setText("Активити");


        getAllActivities();





        return view;
    }//onCreateView

    private void getAllActivities() {

        callSchedule = getService().getSchedule();

        callSchedule.enqueue(new Callback<List<Schedule>>() {
            @Override
            public void onResponse(Call<List<Schedule>> call, retrofit2.Response<List<Schedule>> response) {
                if (response.isSuccessful()) {

                    if(response.code() == 200) {
                        activitiesAdapter = new ActivitiesAdapter(response.body(), getActivity(), mDialog);
                        recyclerView.setAdapter(activitiesAdapter);

                        if(refresh.isRefreshing()){
                            refresh.setRefreshing(false);
                        }
                    }

                } else {
                    Toast.makeText(getActivity(), "Отсутствие доступа к ресурсу", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<List<Schedule>> callActivities, Throwable t) {
                Toast.makeText(getActivity(), t.toString(), Toast.LENGTH_LONG).show();
            }
        });



    }


    // Получить сервис для работы с сервером
    private Service getService(){
        return ServiceGenerator.createService(Service.class);
    }


    @Override
    public void onDestroy() {
        super.onDestroy();
        if(mDialog != null && mDialog.isShowing()) {
            mDialog.cancel();
        }
    }


    @Override
    public void onRefresh() {

        getAllActivities();

    }
}
