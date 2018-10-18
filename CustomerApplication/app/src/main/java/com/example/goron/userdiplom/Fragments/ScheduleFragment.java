package com.example.goron.userdiplom.Fragments;

import android.app.ProgressDialog;
import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ExpandableListView;
import android.widget.Toast;

import com.example.goron.userdiplom.Adapters.AdapterSchedule;
import com.example.goron.userdiplom.Interface.Service;
import com.example.goron.userdiplom.Manager.SerializableManager;
import com.example.goron.userdiplom.Model.DatesFestival;
import com.example.goron.userdiplom.Model.Schedule;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.Service.ServiceGenerator;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;


public class ScheduleFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener  {

    private static final int LOCAL_DB_ID = 3;

    ExpandableListView expandableListView;
    AdapterSchedule adapterSchedule;

    private final  String FileNameDatesFestival = "DatesFestival.ser";

    ProgressDialog mDialog;

    List<Schedule> shedulesList;
    DatesFestival datesFestival;
    SwipeRefreshLayout refresh;

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


            mDialog = new ProgressDialog(getContext());
            mDialog.setMessage("Загрузка расписания...");
            mDialog.setCancelable(false);
            mDialog.show();

        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_schedule, container, false);

        refresh = view.findViewById(R.id.refresh);

        refresh.setOnRefreshListener(this);

        expandableListView = view.findViewById(R.id.exppandableList);
        getDatesFestival();
        return view;
    }



    private void getDatesFestival(){
        datesFestival =  SerializableManager.readSerializableObject(getContext(), FileNameDatesFestival);
        getSchedule();
    }//getDatesFestival




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

                        mDialog.cancel();

                        refresh.setRefreshing(false);

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

    }//getSchedule

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
        getDatesFestival();
    }
}
