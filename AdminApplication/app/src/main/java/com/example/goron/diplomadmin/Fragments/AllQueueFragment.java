package com.example.goron.diplomadmin.Fragments;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewStub;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.example.goron.diplomadmin.Adapters.AdapterCardQueue;
import com.example.goron.diplomadmin.Adapters.AdapterQueue;
import com.example.goron.diplomadmin.Interface.Service;
import com.example.goron.diplomadmin.Manager.SerializableManager;
import com.example.goron.diplomadmin.Model.InfoQueue;
import com.example.goron.diplomadmin.Model.Setting;
import com.example.goron.diplomadmin.Model.UserInQueue;
import com.example.goron.diplomadmin.Model.Users;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.Service.ServiceGenerator;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


public class AllQueueFragment extends Fragment {


    Setting setting;

    InfoQueue infoQueue;

    RelativeLayout relativeTop, relativeBottom;



    TextView textNameSchedule, textAmountQueue;

    private AdapterQueue adapterQueue;

    private RecyclerView recycler;

    private int idShedule, idUser;
    private String userName, nameSchedule;

    public AllQueueFragment() {
        // Required empty public constructor
    }


    public static AllQueueFragment newInstance(int idShedule, String nameSchedule) {
        AllQueueFragment fragment = new AllQueueFragment();
        Bundle args = new Bundle();
        args.putInt("idShedule", idShedule);
        args.putString("nameSchedule", nameSchedule);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            idShedule = getArguments().getInt("idShedule");
            nameSchedule = getArguments().getString("nameSchedule");
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_all_queue, container, false);

        // Инициализируем элементы:
        recycler = view.findViewById(R.id.recycler);
        textNameSchedule = view.findViewById(R.id.textNameSchedule);
        textAmountQueue = view.findViewById(R.id.textAmountQueue);
        relativeBottom = view.findViewById(R.id.relativeBottom);
        relativeTop = view.findViewById(R.id.relativeTop);



        // Установить название активности
        textNameSchedule.setText(nameSchedule);


        recycler.setLayoutManager(new LinearLayoutManager(getContext()));





        // Длина очереди
        lengthQueue();

        //Установить настройки
        setSetting();


        getAllActivity();

        return view;
    }


    public void getAllActivity() {

        Call<UserInQueue> call = getLoginService().getAllUserInQueue(idShedule);

        call.enqueue(new Callback<UserInQueue>() {

            @Override
            public void onResponse(Call<UserInQueue> call, retrofit2.Response<UserInQueue> response) {

                if (response.isSuccessful()) {


                    // Создаем адаптер
                    adapterQueue = new AdapterQueue(response.body().getUsers());

                    // Устанавливаем адаптер
                    recycler.setAdapter(adapterQueue);


                } else {
                    Toast.makeText(getContext(), "error response", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<UserInQueue> call, Throwable t) {
                Toast.makeText(getContext(), t.toString(), Toast.LENGTH_LONG).show();
            }
        });


    }


    private Service getLoginService(){
        return  ServiceGenerator.createService(Service.class);
    }



    // Длина очереди
    private void lengthQueue(){

        Call<InfoQueue> queueInfo = getLoginService().queueInfo(idShedule);

        queueInfo.enqueue(new Callback<InfoQueue>() {
            @Override
            public void onResponse(Call<InfoQueue> call, Response<InfoQueue> response) {
                if(response.isSuccessful()){

                    infoQueue = response.body();
                    textAmountQueue.setText(String.valueOf(infoQueue.getInfo().getLength()));

                }else if(response.code() == 400){
                    Toast.makeText(getContext(), "Invalid params", Toast.LENGTH_LONG).show();
                }else{
                    Toast.makeText(getContext(), "Упс", Toast.LENGTH_LONG).show();
                }
            }//onResponse

            @Override
            public void onFailure(Call<InfoQueue> call, Throwable t) {
                Toast.makeText(getContext(), "Вообще не так!!!!!", Toast.LENGTH_LONG).show();
            }//onFailure
        });
    }//lengthQueue


    // Устанавливаем настройки
    private void setSetting() {
        // Заполняем объект с настройками из файла
        setting = SerializableManager.readSerializableObject(getContext(), "Setting.ser");

        if(setting == null){
            setting = new Setting(0);
        }

        relativeTop.setBackgroundResource(setting.getColorId());
        relativeBottom.setBackgroundResource(setting.getColorId());


        getActivity().findViewById(R.id.toolBar).setBackgroundResource(setting.getColorId());


    }//setSetting

}
