package com.example.goron.diplomadmin.Fragments;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.example.goron.diplomadmin.Adapters.AdapterActivities;
import com.example.goron.diplomadmin.Interface.Service;
import com.example.goron.diplomadmin.Model.Schedule;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.Service.ServiceGenerator;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;


public class ActivitiesFragment extends Fragment {
    RecyclerView recyclerView;
    AdapterActivities activitiesAdapter;

    private Call<List<Schedule>> callSchedule;

    public ActivitiesFragment() {
        // Required empty public constructor
    }


    public static ActivitiesFragment newInstance() {
        ActivitiesFragment fragment = new ActivitiesFragment();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setRetainInstance(true);
        if (getArguments() != null) {}
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_activities, container, false);

        // Инициализируем элементы:
        recyclerView = view.findViewById(R.id.recyclerActivities);
        recyclerView.setLayoutManager(new GridLayoutManager(getActivity(),1));
        getAllActivities();
        return view;
    }


    // Получить все активности
    private void getAllActivities() {

        callSchedule = getService().getSchedule();
        callSchedule.enqueue(new Callback<List<Schedule>>() {
            @Override
            public void onResponse(Call<List<Schedule>> call, retrofit2.Response<List<Schedule>> response) {
                if (response.isSuccessful()) {
                    if(response.code() == 200) {
                        activitiesAdapter = new AdapterActivities(response.body(), getActivity());
                        recyclerView.setAdapter(activitiesAdapter);
                    }
                } else {
                    Toast.makeText(getActivity(), "error response, no access to resource?", Toast.LENGTH_LONG).show();
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
    public void onSaveInstanceState(@NonNull Bundle outState) {
        super.onSaveInstanceState(outState);
    }



}
