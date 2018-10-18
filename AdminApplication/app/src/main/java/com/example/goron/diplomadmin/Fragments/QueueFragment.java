package com.example.goron.diplomadmin.Fragments;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.design.widget.FloatingActionButton;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewStub;
import android.widget.Button;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.daprlabs.cardstack.SwipeDeck;
import com.example.goron.diplomadmin.Adapters.AdapterCardQueue;
import com.example.goron.diplomadmin.Interface.Service;
import com.example.goron.diplomadmin.Manager.SerializableManager;
import com.example.goron.diplomadmin.Model.InfoQueue;
import com.example.goron.diplomadmin.Model.Setting;
import com.example.goron.diplomadmin.Model.UserInQueue;
import com.example.goron.diplomadmin.Model.Users;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.Service.ServiceGenerator;

import java.util.Timer;
import java.util.TimerTask;

import link.fls.swipestack.SwipeStack;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


public class QueueFragment extends Fragment {


    // Адаптер для SwipeStack card
    private AdapterCardQueue adapterCardQueue;

    // SwipeDeck
    private SwipeDeck swipeDeck;

    private int idShedule, idUser;
    private String userName, nameSchedule;

    TextView empty;
    InfoQueue infoQueue;
    Setting setting;
    RelativeLayout relativeTop, relativeBottom;
    FloatingActionButton showQueue;


    TextView textNameSchedule, textAmountQueue;

    public QueueFragment() {
        // Required empty public constructor
    }



    public static QueueFragment newInstance(int idShedule, String nameSchedule) {
        QueueFragment fragment = new QueueFragment();
        Bundle args = new Bundle();
        args.putInt("idShedule", idShedule);
        args.putString("nameSchedule", nameSchedule);
        fragment.setArguments(args);
        return fragment;
    }


    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
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
        View view = inflater.inflate(R.layout.fragment_queue, container, false);

        // Инициализируем элементы:
        textNameSchedule = view.findViewById(R.id.textNameSchedule);
        textAmountQueue = view.findViewById(R.id.textAmountQueue);
        swipeDeck = view.findViewById(R.id.swipe_deck);
        empty = view.findViewById(R.id.empty);

        relativeBottom = view.findViewById(R.id.relativeBottom);
        relativeTop = view.findViewById(R.id.relativeTop);
        showQueue = view.findViewById(R.id.showQueue);

        empty.setVisibility(View.GONE);

        // Установить название активности
        textNameSchedule.setText(nameSchedule);

        //Установить настройки
        setSetting();



        // Получить следующего человека в очереди
        getUserInQueue();

        swipeDeck.setEventCallback(new SwipeDeck.SwipeEventCallback() {
            @Override
            public void cardSwipedLeft(int position) {
                AlertDialog.Builder mDialogBuilder = getDialog("  Человек опоздал?");

                //Настраиваем сообщение в диалоговом окне:
                mDialogBuilder
                        .setCancelable(false)
                        .setPositiveButton("OK",
                                new DialogInterface.OnClickListener() {
                                    public void onClick(DialogInterface dialog,int id) {
                                        // Пропустил очередь
                                        lateness();
                                    }
                                })
                        .setNegativeButton("Отмена",
                                new DialogInterface.OnClickListener() {
                                    public void onClick(DialogInterface dialog,int id) {
                                        dialog.cancel();

                                       swipeDeck.setSelection(position);
                                    }
                                });

                //Создаем AlertDialog:
                AlertDialog alertDialog = mDialogBuilder.create();
                //и отображаем его:
                alertDialog.show();
            }

            @Override
            public void cardSwipedRight(int position) {
                AlertDialog.Builder mDialogBuilder = getDialog("  Человек прошел чередь?");
                //Настраиваем сообщение в диалоговом окне:
                mDialogBuilder
                        .setCancelable(false)
                        .setPositiveButton("OK",
                                new DialogInterface.OnClickListener() {
                                    public void onClick(DialogInterface dialog,int id) {
                                        // Прошел очередь
                                        StepOut();
                                    }
                                })
                        .setNegativeButton("Отмена",
                                new DialogInterface.OnClickListener() {
                                    public void onClick(DialogInterface dialog,int id) {
                                        dialog.cancel();
                                        swipeDeck.setSelection(position);
                                    }
                                });

                //Создаем AlertDialog:
                AlertDialog alertDialog = mDialogBuilder.create();
                //и отображаем его:
                alertDialog.show();

            }

            @Override
            public void cardsDepleted() {}
            @Override
            public void cardActionDown() {}
            @Override
            public void cardActionUp() {}
        });




        showQueue.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                Fragment fragment = AllQueueFragment.newInstance(idShedule, nameSchedule);
                getActivity().getSupportFragmentManager().beginTransaction()
                        .replace(R.id.content_frame, fragment)
                        .addToBackStack(null)
                        .commit();
            }
        });






        return view;
    }


    private AlertDialog.Builder getDialog(String textDialog){
        //Получаем вид с файла dialog_swipee.xml, который применим для диалогового окна:
        LayoutInflater li = LayoutInflater.from(getContext());
        View promptsView = li.inflate(R.layout.dialog_swipe, null);
        //Создаем AlertDialog
        AlertDialog.Builder mDialogBuilder = new AlertDialog.Builder(getContext());

        //Настраиваем prompt.xml для нашего AlertDialog:
        mDialogBuilder.setView(promptsView);

        //Настраиваем отображение поля текста в открытом диалоге:
        final TextView tvText = (TextView) promptsView.findViewById(R.id.tvText);

        tvText.setText(textDialog);

        return mDialogBuilder;
    }


    // Получить следующего человека в очереди
    private void getUserInQueue(){

        Call<UserInQueue> call = getLoginService().getUserInQueue(idShedule);

        call.enqueue(new Callback<UserInQueue>() {

            @Override
            public void onResponse(Call<UserInQueue> call, retrofit2.Response<UserInQueue> response) {

                if (response.isSuccessful()) {

                    if(response.body().getUsers().size() == 0){
                        empty.setVisibility(View.VISIBLE);
                        return;
                    }

                    Users users = response.body().getUsers().get(0);


                    // Создаем адаптер
                    adapterCardQueue = new AdapterCardQueue(response.body(), getActivity());


                    // Имя и ид человека в очереди
                    userName = users.getName();

                    // Получить ид человека в очереди для дальнейших манипуляцмй
                    idUser = users.getId();

                    // Устанавливаем адаптер
                    swipeDeck.setAdapter(adapterCardQueue);

                    // Длина очереди
                    lengthQueue();

                    // сброс
                    //swipeDeck
                } else {
                    Toast.makeText(getContext(), "error response", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<UserInQueue> call, Throwable t) {
                Toast.makeText(getContext(), t.toString(), Toast.LENGTH_LONG).show();
            }
        });

    }//getUserInQueue


    // Прошел очередь
    private void StepOut(){

        Call<Void> deleteRequest = getLoginService().deleteUserInQueue(idShedule, idUser);

        deleteRequest.enqueue(new Callback<Void>() {
            @Override
            public void onResponse(Call<Void> call, Response<Void> response) {
                if(response.isSuccessful()){
                    Toast.makeText(getContext(), userName + " Прошел", Toast.LENGTH_LONG).show();
                    // Получить следующего человека в очереди
                    getUserInQueue();
                }else{
                    Toast.makeText(getContext(), "Упс", Toast.LENGTH_LONG).show();
                }
            }//onResponse

            @Override
            public void onFailure(Call<Void> call, Throwable t) {
                Toast.makeText(getContext(), "Вообще не так!!!!!", Toast.LENGTH_LONG).show();
            }//onFailure
        });
    }//StepOut



    // Пропустил очередь
    private void lateness(){
        Call<Void> latenessRequest = getLoginService().latenessUser(idShedule, idUser);

        latenessRequest.enqueue(new Callback<Void>() {
            @Override
            public void onResponse(Call<Void> call, Response<Void> response) {

                if(response.isSuccessful()){

                    // Если все пучком
                    Toast.makeText(getContext(), userName + "  Пропустил", Toast.LENGTH_LONG).show();
                    getUserInQueue();

                }else if(response.code() == 404){

                    Toast.makeText(getContext(), " User are not in queue", Toast.LENGTH_LONG).show();
                }else{
                    // Если нет код ошибки
                    Toast.makeText(getContext(), String.valueOf(response.code()), Toast.LENGTH_LONG).show();
                }
            }//onResponse

            @Override
            public void onFailure(Call<Void> call, Throwable t) {
                Toast.makeText(getContext(), "Вообще не так!!!!!", Toast.LENGTH_LONG).show();
            }//onFailure
        });

    }//StepOut




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



    private final Runnable delayedAction = new Runnable() {
        @Override
        public void run() {
            getUserInQueue(); // метод вызовется через 6 сек
        }
    };

}
