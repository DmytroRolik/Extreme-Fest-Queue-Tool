package com.example.goron.diplomadmin.Fragments;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ExpandableListView;
import android.widget.Toast;

import com.example.goron.diplomadmin.Adapters.AdapterActivity;
import com.example.goron.diplomadmin.Adapters.AdapterSchedule;
import com.example.goron.diplomadmin.Interface.Service;
import com.example.goron.diplomadmin.Model.Activities;
import com.example.goron.diplomadmin.Model.DatesFestival;
import com.example.goron.diplomadmin.Model.Schedule;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.Service.ServiceGenerator;

import java.sql.Date;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;


public class ScheduleFragment extends Fragment {


    ExpandableListView expandableListView;
    AdapterSchedule adapterSchedule;


    List<Schedule> shedulesList;
    DatesFestival datesFestival;

    public ScheduleFragment() {
        // Required empty public constructor
    }


    public static ScheduleFragment newInstance() {
        ScheduleFragment fragment = new ScheduleFragment();
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
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_schedule, container, false);

        // Инициализируем элементы:
        expandableListView = view.findViewById(R.id.exppandableList);

        getDatesFestival();
        return view;
    }



    private void getDatesFestival(){

        Call<DatesFestival> call = getService().getDatesFestival();

        call.enqueue(new Callback<DatesFestival>() {
            @Override
            public void onResponse(Call<DatesFestival> call, retrofit2.Response<DatesFestival> response) {
                if (response.isSuccessful()) {

                    if(response.code() == 200) {
                        datesFestival = response.body();
                        getSchedule();
                    }

                } else {
                    Toast.makeText(getActivity(), "error response, no access to resource?", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<DatesFestival> call, Throwable t) {
                Toast.makeText(getActivity(), t.toString(), Toast.LENGTH_LONG).show();
            }
        });
    }//getSchedule




    private void getSchedule(){

        Call<List<Schedule>> call = getService().getSchedule();

        call.enqueue(new Callback<List<Schedule>>() {
            @Override
            public void onResponse(Call<List<Schedule>> call, retrofit2.Response<List<Schedule>> response) {
                if (response.isSuccessful()) {

                    if(response.code() == 200) {

                        shedulesList = response.body();
                        adapterSchedule = new AdapterSchedule(getContext(),shedulesList, datesFestival);
                        expandableListView.setAdapter(adapterSchedule);

                        expandableListView.expandGroup(0);
                        expandableListView.expandGroup(1);
                        expandableListView.expandGroup(2);
                    }

                } else {
                    Toast.makeText(getActivity(), "error response, no access to resource?", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<List<Schedule>> call, Throwable t) {
                Toast.makeText(getActivity(), t.toString(), Toast.LENGTH_LONG).show();
            }
        });

    }

    private Service getService(){
        return ServiceGenerator.createService(Service.class);
    }
}
