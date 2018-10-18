package com.example.goron.diplomadmin.Fragments;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.widget.NestedScrollView;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.example.goron.diplomadmin.Adapters.AdapterImage;
import com.example.goron.diplomadmin.Interface.Service;
import com.example.goron.diplomadmin.Model.InfoQueue;
import com.example.goron.diplomadmin.Model.Schedule;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.Service.ServiceGenerator;
import com.example.goron.diplomadmin.SpacePhotoActivity;

import de.hdodenhof.circleimageview.CircleImageView;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


public class AboutActivitiesFragment extends Fragment implements NestedScrollView.OnScrollChangeListener {


    private static final String ARG_PARAM_SCHEDULE = "schedule";

    private Schedule schedule;

    private  String start = "<html> <head></head>  <body style=\"text-align:justify;color:white;\"> ";
    private String end = " </body> </html> ";

    // Информация по очереди
    private InfoQueue infoQueue;

    ImageView imageView;
    TextView textViewTime, textViewCount, textViewAvgTime;
    WebView webView;
    RecyclerView recyclerView;


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


        // Инициализируем элементы:
        webView = view.findViewById(R.id.webView);
        textViewTime = view.findViewById(R.id.textViewTime);
        textViewCount = view.findViewById(R.id.textViewCount);
        textViewAvgTime = view.findViewById(R.id.textViewAvgTime);
        imageView = view.findViewById(R.id.imageView);
        recyclerView = view.findViewById(R.id.recyclerImage);
        webView.setVerticalScrollBarEnabled(false);
        recyclerView = view.findViewById(R.id.recyclerImage);

        recyclerView.setLayoutManager(new GridLayoutManager(getActivity(),2));
        AdapterImage adapterImage = new AdapterImage(getContext(), schedule.getPhotos());

        recyclerView.setNestedScrollingEnabled(false);
        recyclerView.setAdapter(adapterImage);


        getInfoQueue();
        return view;
    }//onCreateView



    // Длина очереди
    private void getInfoQueue(){

        Call<InfoQueue> queueInfo = getService().queueInfo(schedule.getId());

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
                    textViewAvgTime.setText( String.valueOf(infoQueue.getInfo().getAverageTime()));


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
}
