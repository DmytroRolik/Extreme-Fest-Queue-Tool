package com.example.goron.userdiplom.Adapters;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.support.annotation.NonNull;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.example.goron.userdiplom.Interface.Service;
import com.example.goron.userdiplom.Model.Activities;
import com.example.goron.userdiplom.Model.Queue;
import com.example.goron.userdiplom.Model.QueueAddorDelete;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.Service.ServiceGenerator;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;

public class MyQueueAdapter extends RecyclerView.Adapter<MyQueueAdapter.MyQueueAdapterVH> {

    List<Activities> activitiesList;
    Context context;
    ProgressDialog mDialog;
    SwipeRefreshLayout refresh;



    public MyQueueAdapter(List<Activities> activitiesList, Context context,ProgressDialog mDialog, SwipeRefreshLayout refresh) {
        this.activitiesList = activitiesList;
        this.context = context;
        this.mDialog = mDialog;
        this.refresh = refresh;

    }

    @NonNull
    @Override
    public MyQueueAdapterVH onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_my_queue, viewGroup, false);

        return new MyQueueAdapterVH(view);
    }

    @Override
    public void onBindViewHolder(@NonNull final MyQueueAdapterVH myQueueAdapterVH, final int i) {

        final Activities activities = activitiesList.get(i);
        myQueueAdapterVH.nameActivity.setText(activities.getName());
        getMyQueue(activities.getId(), myQueueAdapterVH.myQueue, i);

        myQueueAdapterVH.layoutItemQueue.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                mDialog.show();

                if(myQueueAdapterVH.myQueue.getText().equals("--")){

                    AlertDialog.Builder mDialogBuilder = getDialog("ВСТАТЬ в очередь на " +activities.getName() +" ?");

                    //Настраиваем сообщение в диалоговом окне:
                    mDialogBuilder
                            .setCancelable(false)
                            .setPositiveButton("OK",
                                    new DialogInterface.OnClickListener() {
                                        public void onClick(DialogInterface dialog,int id) {
                                            addMeFromQueue(activities.getId(), myQueueAdapterVH.myQueue, i);
                                        }
                                    })
                            .setNegativeButton("Отмена",
                                    new DialogInterface.OnClickListener() {
                                        public void onClick(DialogInterface dialog,int id) {
                                            dialog.cancel();
                                            if(mDialog != null){
                                                mDialog.cancel();
                                            }
                                        }
                                    });

                    //Создаем AlertDialog:
                    AlertDialog alertDialog = mDialogBuilder.create();
                    //и отображаем его:
                    alertDialog.show();



                }else{


                    AlertDialog.Builder mDialogBuilder = getDialog("УЙТИ из очереди на " +activities.getName() +" ?");

                    //Настраиваем сообщение в диалоговом окне:
                    mDialogBuilder
                            .setCancelable(false)
                            .setPositiveButton("OK",
                                    new DialogInterface.OnClickListener() {
                                        public void onClick(DialogInterface dialog,int id) {
                                            deleteMeFromQueue(activities.getId(), myQueueAdapterVH.myQueue, i);
                                        }
                                    })
                            .setNegativeButton("Отмена",
                                    new DialogInterface.OnClickListener() {
                                        public void onClick(DialogInterface dialog,int id) {
                                            dialog.cancel();
                                            if(mDialog != null){
                                                mDialog.cancel();
                                            }
                                        }
                                    });

                    //Создаем AlertDialog:
                    AlertDialog alertDialog = mDialogBuilder.create();
                    //и отображаем его:
                    alertDialog.show();

                }

            }
        });

    }


    private AlertDialog.Builder getDialog(String textDialog){
        //Получаем вид с файла dialog_swipee.xml, который применим для диалогового окна:
        LayoutInflater li = LayoutInflater.from(context);
        View promptsView = li.inflate(R.layout.dialog_click_queue, null);
        //Создаем AlertDialog
        AlertDialog.Builder mDialogBuilder = new AlertDialog.Builder(context);

        //Настраиваем prompt.xml для нашего AlertDialog:
        mDialogBuilder.setView(promptsView);

        //Настраиваем отображение поля текста в открытом диалоге:
        final TextView tvText = (TextView) promptsView.findViewById(R.id.tvText);

        tvText.setText(textDialog);

        return mDialogBuilder;
    }



    @Override
    public int getItemCount() {
        return activitiesList.size();
    }

    private void getMyQueue(int idActivity, final TextView myQueue, final int position){

       Call<Queue> queueCall = getService().getMuQueueInActivity(idActivity);



        queueCall.enqueue(new Callback<Queue>() {
                @Override
                public void onResponse(Call<Queue> call, retrofit2.Response<Queue> response) {
                    if (response.isSuccessful()) {

                        myQueue.setText(String.valueOf(response.body().getPosition()+1));

                        if(position == getItemCount() - 1){
                            mDialog.cancel();
                            if(refresh.isRefreshing()){
                                refresh.setRefreshing(false);
                            }
                        }
                        // Если ошибка 404
                    } else if (response.code() == 404) {
                        myQueue.setText("--");
                        if(position == getItemCount() - 1){
                            mDialog.cancel();
                            if(refresh.isRefreshing()){
                                refresh.setRefreshing(false);
                            }
                        }
                        // Любая другая ошибка
                    } else {
                        Toast.makeText(context, "error response, no access to resource?", Toast.LENGTH_LONG).show();

                    }
                }

                @Override
                public void onFailure(Call<Queue> call, Throwable t) {
                    Toast.makeText(context, t.toString(), Toast.LENGTH_LONG).show();
                }
            });


    }//getMyQueue



    // Удалить меня из очереди
    private void deleteMeFromQueue(final int idActivity, final TextView myQueue, final int position){

        Call<QueueAddorDelete> queueCall = getService().deleteMeFromQueue(idActivity);

        queueCall.enqueue(new Callback<QueueAddorDelete>() {
            @Override
            public void onResponse(Call<QueueAddorDelete> call, retrofit2.Response<QueueAddorDelete> response) {
                if (response.isSuccessful()) {

                    QueueAddorDelete queueAddorDelete = response.body();

                    getMyQueue(idActivity, myQueue, position);

                    mDialog.cancel();
                    Toast.makeText(context, queueAddorDelete.getMessage(), Toast.LENGTH_LONG).show();

                    // Если ошибка 404
                } else if (response.code() == 404) {

                    QueueAddorDelete queueAddorDelete = response.body();
                    mDialog.cancel();
                    Toast.makeText(context, queueAddorDelete.getMessage(), Toast.LENGTH_LONG).show();
                    // Любая другая ошибка
                } else {
                    Toast.makeText(context, "error response, no access to resource?", Toast.LENGTH_LONG).show();

                }
            }

            @Override
            public void onFailure(Call<QueueAddorDelete> call, Throwable t) {
                Toast.makeText(context, t.toString(), Toast.LENGTH_LONG).show();
            }
        });

    }



    // Добавить меня в очередь
    private void addMeFromQueue(final int idActivity, final TextView myQueue, final int position){

        Call<QueueAddorDelete> queueCall = getService().addMeFromQueue(idActivity);


        queueCall.enqueue(new Callback<QueueAddorDelete>() {
            @Override
            public void onResponse(Call<QueueAddorDelete> call, retrofit2.Response<QueueAddorDelete> response) {
                if (response.isSuccessful()) {

                    QueueAddorDelete queueAddorDelete = response.body();

                    getMyQueue(idActivity, myQueue, position);
                    mDialog.cancel();
                    Toast.makeText(context, queueAddorDelete.getMessage(), Toast.LENGTH_LONG).show();

                    // Если ошибка 404
                } else if (response.code() == 409) {

                    QueueAddorDelete queueAddorDelete = response.body();
                    mDialog.cancel();
                    Toast.makeText(context, queueAddorDelete.getMessage(), Toast.LENGTH_LONG).show();
                    // Любая другая ошибка
                } else {
                    Toast.makeText(context, "error response, no access to resource?", Toast.LENGTH_LONG).show();

                }
            }

            @Override
            public void onFailure(Call<QueueAddorDelete> call, Throwable t) {
                Toast.makeText(context, t.toString(), Toast.LENGTH_LONG).show();
            }
        });

    }

    // Получить сервис для работы с сервером
    private Service getService(){
        return ServiceGenerator.createService(Service.class);
    }

    public class MyQueueAdapterVH extends RecyclerView.ViewHolder {

    TextView myQueue, nameActivity;
    LinearLayout layoutItemQueue;

        public MyQueueAdapterVH(@NonNull View itemView) {
            super(itemView);

            nameActivity = itemView.findViewById(R.id.nameActivity);
            myQueue = itemView.findViewById(R.id.myQueue);
            layoutItemQueue = itemView.findViewById(R.id.layoutItemQueue);
        }
    }
}
