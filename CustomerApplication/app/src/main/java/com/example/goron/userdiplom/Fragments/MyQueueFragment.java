package com.example.goron.userdiplom.Fragments;

import android.animation.Animator;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.animation.DynamicAnimation;
import android.support.animation.SpringAnimation;
import android.support.animation.SpringForce;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.DecelerateInterpolator;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.example.goron.userdiplom.Adapters.MyQueueAdapter;
import com.example.goron.userdiplom.Interface.Service;
import com.example.goron.userdiplom.MainActivity;
import com.example.goron.userdiplom.Model.Activities;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.Service.ServiceGenerator;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;


public class MyQueueFragment extends Fragment  implements SwipeRefreshLayout.OnRefreshListener {


    RecyclerView recyclerView;
    MyQueueAdapter myQueueAdapter;
    private Call<List<Activities>> callActivities;
    RelativeLayout relationLayout;
    SwipeRefreshLayout refresh;

    ProgressDialog mDialog;

    public MyQueueFragment() {
        // Required empty public constructor
    }


    public static MyQueueFragment newInstance() {
        MyQueueFragment fragment = new MyQueueFragment();
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
        View view = inflater.inflate(R.layout.fragment_my_queue, container, false);


        refresh = view.findViewById(R.id.refresh);
        refresh.setOnRefreshListener(this);
        recyclerView = view.findViewById(R.id.recycler);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));

        relationLayout = view.findViewById(R.id.relationLayout);

        if(mDialog == null) {
            mDialog = new ProgressDialog(getContext());
            mDialog.setMessage("Получение очередей...");
            mDialog.setCancelable(false);
            mDialog.show();
        }else if(!mDialog.isShowing()){
            mDialog.show();

        }




        getActivities("2018-09-09");


        ((TextView)getActivity().findViewById(R.id.textHeader)).setText("Моя очередь");

        return view;
    }


    public void getActivities(String date) {

        callActivities = getService().getActivities(date);

        callActivities.enqueue(new Callback<List<Activities>>() {
            @Override
            public void onResponse(Call<List<Activities>> call, retrofit2.Response<List<Activities>> response) {
                if (response.isSuccessful()) {

                    if(response.code() == 200) {
                        myQueueAdapter = new MyQueueAdapter(response.body(), getActivity(), mDialog, refresh);
                        recyclerView.setAdapter(myQueueAdapter);
                    }//if
                } else {
                    Toast.makeText(getActivity(), "error response, no access to resource?", Toast.LENGTH_LONG).show();
                }//if-else
            }

            @Override
            public void onFailure(Call<List<Activities>> callActivities, Throwable t) {
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
        getActivities("2018-09-09");
    }
}
