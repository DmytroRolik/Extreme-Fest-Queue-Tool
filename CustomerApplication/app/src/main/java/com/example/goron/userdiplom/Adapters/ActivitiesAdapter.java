package com.example.goron.userdiplom.Adapters;

import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.drawable.Drawable;
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
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.example.goron.userdiplom.Fragments.AboutActivitiesFragment;
import com.example.goron.userdiplom.Model.Activities;
import com.example.goron.userdiplom.Model.Schedule;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.Service.ServiceGenerator;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

public class ActivitiesAdapter extends RecyclerView.Adapter<ActivitiesAdapter.ActivitiesAdapterVH> {


    List<Schedule> scheduleList;
    Context context;
    ProgressDialog mDialog;

    private int countItem = 4;

    public ActivitiesAdapter(List<Schedule> scheduleList, Context context, ProgressDialog mDialog) {
        this.scheduleList = clearListFromDuplicateName(scheduleList);
        this.context = context;
        this.mDialog = mDialog;
    }

    private List<Schedule> clearListFromDuplicateName(List<Schedule> list1) {

        Map<String, Schedule> cleanMap = new LinkedHashMap<String, Schedule>();
        for (int i = list1.size()-1; i >=0 ; i--) {
            cleanMap.put(list1.get(i).getName(), list1.get(i));
        }
        List<Schedule> list = new ArrayList<Schedule>(cleanMap.values());
        return list;
    }



    @NonNull
    @Override
    public ActivitiesAdapterVH onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {

        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_activities,viewGroup, false);

        return new ActivitiesAdapterVH(view);
    }

    @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN_MR1)
    @Override
    public void onBindViewHolder(@NonNull ActivitiesAdapterVH activitiesAdapterVH, int i) {

        final Schedule schedule = scheduleList.get(i);

        activitiesAdapterVH.nameActivity.setText(schedule.getName());

        Glide.with(context)
                .load(ServiceGenerator.API_BASE_URL_IMAGE + schedule.getMain_photo())
                .asBitmap()
                .placeholder(R.drawable.ic_cloud_off_red)
                .diskCacheStrategy(DiskCacheStrategy.RESULT)
                .into(activitiesAdapterVH.imageActivity);



        activitiesAdapterVH.relationLayoutItemActiviti.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
               ((FragmentActivity)context).getSupportFragmentManager().beginTransaction()
                       .replace(R.id.content_frame, AboutActivitiesFragment.newInstance(schedule )).addToBackStack(null).commit();
            }
        });

        if(mDialog != null){
            mDialog.cancel();
        }

    }//onBindViewHolder

    @Override
    public int getItemCount() {
        return scheduleList.size();
    }



    public  class ActivitiesAdapterVH extends RecyclerView.ViewHolder {


        ImageView imageActivity;
        TextView nameActivity;


        RelativeLayout relationLayoutItemActiviti;

        public ActivitiesAdapterVH(@NonNull View itemView) {
            super(itemView);

            imageActivity = itemView.findViewById(R.id.imageActivity);
            nameActivity = itemView.findViewById(R.id.nameActivity);

            relationLayoutItemActiviti = itemView.findViewById(R.id.relationLayoutItemActiviti);
        }
    }
}
