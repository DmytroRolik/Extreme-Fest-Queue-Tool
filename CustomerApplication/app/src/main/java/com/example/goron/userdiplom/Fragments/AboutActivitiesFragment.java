package com.example.goron.userdiplom.Fragments;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.widget.NestedScrollView;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.example.goron.userdiplom.Adapters.ImageAdapter;
import com.example.goron.userdiplom.Interface.Service;
import com.example.goron.userdiplom.Model.InfoQueue;
import com.example.goron.userdiplom.Model.Schedule;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.Service.ServiceGenerator;
import com.example.goron.userdiplom.SpacePhotoActivity;

import de.hdodenhof.circleimageview.CircleImageView;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


public class AboutActivitiesFragment extends Fragment implements NestedScrollView.OnScrollChangeListener  {

    private static final String ARG_PARAM_SCHEDULE = "schedule";
    private static final int LOCAL_DB_ID = 1;

    private Schedule schedule;

    private  String start = "<html> <head></head>  <body style=\"text-align:justify;color:white;\"> ";
    private String end = " </body> </html> ";

    // Информация по очереди
    private InfoQueue infoQueue;



    ImageView imageView;
    TextView textViewTime, textViewCount, textViewAvgTime;
    WebView webView;
    RecyclerView recyclerView;
    ProgressDialog mDialog;

    public AboutActivitiesFragment() {
        // Required empty public constructor
    }


    public static AboutActivitiesFragment newInstance(Schedule schedule) {
        AboutActivitiesFragment fragment = new AboutActivitiesFragment();
        Bundle args = new Bundle();
        args.putSerializable(ARG_PARAM_SCHEDULE, schedule);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            schedule = (Schedule) getArguments().getSerializable(ARG_PARAM_SCHEDULE);
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view =  inflater.inflate(R.layout.fragment_about_activities, container, false);



        if(mDialog == null) {
            mDialog = new ProgressDialog(getContext());
            mDialog.setMessage("Загрузка информации...");
            mDialog.setCancelable(false);
            mDialog.show();
        }else if(!mDialog.isShowing()){
            mDialog.show();

        }

        // Инициализируем элементы:
        webView = view.findViewById(R.id.webView);
        textViewTime = view.findViewById(R.id.textViewTime);
        textViewCount = view.findViewById(R.id.textViewCount);
        textViewAvgTime = view.findViewById(R.id.textViewAvgTime);
        imageView = view.findViewById(R.id.imageView);
        recyclerView = view.findViewById(R.id.recyclerImage);
        webView.setVerticalScrollBarEnabled(false);
        recyclerView = view.findViewById(R.id.recyclerImage);


        DisplayMetrics displayMetrics = new DisplayMetrics();
        getActivity().getWindowManager().getDefaultDisplay().getMetrics(displayMetrics);
        float density  = getResources().getDisplayMetrics().density;
        float height = displayMetrics.heightPixels / density;
        float width = displayMetrics.widthPixels / density;

        if(width < 400){
            recyclerView.setLayoutManager(new GridLayoutManager(getActivity(),2));
        }else {
            recyclerView.setLayoutManager(new GridLayoutManager(getActivity(),3));
        }

        ImageAdapter imageAdapter = new ImageAdapter(getContext(), schedule.getPhotos());

        recyclerView.setNestedScrollingEnabled(false);
        recyclerView.setAdapter(imageAdapter);

        ((TextView)getActivity().findViewById(R.id.textHeader)).setText(schedule.getName());

        getInfoQueue();
        return view;
    }//onCreateView



    // Длина очереди
    private void getInfoQueue(){

        Call<InfoQueue> queueInfo = getService().getInfoQueue(schedule.getId());

        queueInfo.enqueue(new Callback<InfoQueue>() {
            @Override
            public void onResponse(Call<InfoQueue> call, Response<InfoQueue> response) {
                if(response.isSuccessful()){

                    infoQueue = response.body();

                    Glide.with(getActivity())
                            .load(ServiceGenerator.API_BASE_URL_IMAGE + schedule.getMain_photo())
                            .asBitmap()
                            .error(R.drawable.ic_cloud_off_red)
                            .diskCacheStrategy(DiskCacheStrategy.SOURCE)
                            .into(imageView);

                    imageView.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View v) {
                            String spacePhoto = ServiceGenerator.API_BASE_URL_IMAGE + schedule.getMain_photo();
                            Intent intent = new Intent(getContext(), SpacePhotoActivity.class);
                            intent.putExtra(SpacePhotoActivity.EXTRA_SPACE_PHOTO, spacePhoto);
                            startActivity(intent);
                        }
                    });



                    if(schedule.getDescription() == null){
                        webView.loadData("Нет описания", "text/html; charset=utf-8", "utf-8");
                        webView.setBackgroundColor(0x00000000);
                    }else {
                        webView.loadData(start + schedule.getDescription() + end,"text/html; charset=utf-8", "utf-8");
                        webView.setBackgroundColor(0x00000000);

                    }

                    textViewTime.setText( schedule.getStart_time().substring(0,5) + " - " + schedule.getEnd_time().substring(0,5));

                    textViewCount.setText( String.valueOf(infoQueue.getInfo().getLength()));

                    String avg = infoQueue.getInfo().getAverageTime() < 0 ? "--" : String.valueOf(infoQueue.getInfo().getAverageTime());
                    textViewAvgTime.setText( avg );

                    mDialog.cancel();

                }else if(response.code() == 400){
                    Toast.makeText(getContext(), "Недействительные параметры", Toast.LENGTH_LONG).show();
                }else{
                    Toast.makeText(getContext(), "Ошибка код: "+ response.code(), Toast.LENGTH_LONG).show();
                }
            }//onResponse

            @Override
            public void onFailure(Call<InfoQueue> call, Throwable t) {
                Toast.makeText(getContext(), "Проблемы связи, сервера", Toast.LENGTH_LONG).show();
            }//onFailure
        });
    }//lengthQueue

    // Получить сервис для работы с сервером
    private Service getService(){
        return ServiceGenerator.createService(Service.class);
    }

    @Override
    public void onScrollChange(NestedScrollView nestedScrollView, int i, int i1, int i2, int i3) {
        if (nestedScrollView.getChildAt (nestedScrollView.getChildCount () - 1) != null) {
            if ((i1 >= (nestedScrollView.getChildAt(nestedScrollView.getChildCount() - 1).getMeasuredHeight() - nestedScrollView.getMeasuredHeight())) && i1 > i2) {

            }
        }
    }


    @Override
    public void onDestroy() {
        super.onDestroy();
        if(mDialog != null && mDialog.isShowing()) {
            mDialog.cancel();
        }
    }
}
