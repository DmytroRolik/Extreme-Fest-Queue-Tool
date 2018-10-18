package com.example.goron.diplomadmin.Adapters;

import android.content.Context;
import android.os.Build;
import android.support.annotation.NonNull;
import android.support.annotation.RequiresApi;
import android.support.v4.app.FragmentActivity;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.example.goron.diplomadmin.Fragments.AboutActivitiesFragment;
import com.example.goron.diplomadmin.Model.Schedule;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.Service.ServiceGenerator;

import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

public class AdapterActivities extends RecyclerView.Adapter<AdapterActivities.AdapterActivitiesVH> {

    List<Schedule> scheduleList;
    Context context;

    public AdapterActivities(List<Schedule> scheduleList, Context context) {
        this.scheduleList = clearListFromDuplicateName(scheduleList);
        this.context = context;
    }

    private List<Schedule> clearListFromDuplicateName(List<Schedule> list1) {

        Map<String, Schedule> cleanMap = new LinkedHashMap<String, Schedule>();
        for (int i = 0; i < list1.size(); i++) {
            cleanMap.put(list1.get(i).getName(), list1.get(i));
        }
        List<Schedule> list = new ArrayList<Schedule>(cleanMap.values());
        return list;
    }



    @NonNull
    @Override
    public AdapterActivitiesVH onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {

        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_activities,viewGroup, false);

        return new AdapterActivitiesVH(view);
    }

    @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN_MR1)
    @Override
    public void onBindViewHolder(@NonNull AdapterActivitiesVH activitiesAdapterVH, int i) {

        final Schedule schedule = scheduleList.get(i);



        activitiesAdapterVH.nameActivity.setText(schedule.getName());


        Glide.with(context)
                .load(ServiceGenerator.API_BASE_URL_IMAGE + schedule.getMain_photo())
                .asBitmap()
                .placeholder(R.drawable.ic_cloud_off_red)
                .diskCacheStrategy(DiskCacheStrategy.RESULT)
                .into(activitiesAdapterVH.imageActivity);

       // activitiesAdapterVH.imageActivity.setImageResource(getImageFromActivitie(schedule.getName()));


        activitiesAdapterVH.relationLayoutItemActiviti.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ((FragmentActivity)context).getSupportFragmentManager().beginTransaction()
                        .replace(R.id.content_frame, AboutActivitiesFragment.newInstance(schedule)).addToBackStack(null).commit();
            }
        });



    }

    @Override
    public int getItemCount() {
        return scheduleList.size();
    }


    private int getImageFromActivitie(String nameActivitie){

        switch (nameActivitie){
            case "Байдарки": return R.drawable.canoes;
            case "Троллей": return R.drawable.trolley;
            case "Скалолазание": return R.drawable.rock_climbing;
            case "Слеклайн" : return  R.drawable.slackline;
            case "Страйкбол": return R.drawable.paintball;
            case "Парапланы": return R.drawable.hangglider;
        }

        return R.drawable.notimage;
    }

    public  class AdapterActivitiesVH extends RecyclerView.ViewHolder {


        ImageView imageActivity;
        TextView nameActivity;

        RelativeLayout relationLayoutItemActiviti;

        public AdapterActivitiesVH(@NonNull View itemView) {
            super(itemView);

            imageActivity = itemView.findViewById(R.id.imageActivity);
            nameActivity = itemView.findViewById(R.id.nameActivity);
            relationLayoutItemActiviti = itemView.findViewById(R.id.relationLayoutItemActiviti);
        }
    }
}
