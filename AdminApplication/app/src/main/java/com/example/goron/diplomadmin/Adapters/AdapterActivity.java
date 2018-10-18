package com.example.goron.diplomadmin.Adapters;

import android.content.Context;
import android.support.annotation.NonNull;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.example.goron.diplomadmin.Fragments.QueueFragment;
import com.example.goron.diplomadmin.Model.AccountInformation;
import com.example.goron.diplomadmin.Model.Activities;
import com.example.goron.diplomadmin.Model.DatesFestival;
import com.example.goron.diplomadmin.Model.Setting;
import com.example.goron.diplomadmin.R;

import java.text.SimpleDateFormat;
import java.util.List;

import retrofit2.Call;

public class AdapterActivity extends RecyclerView.Adapter<AdapterActivity.AdapterSheduleVH>  {


    List<Activities> activitiesList;
    AccountInformation accountInformation;
    Context context;
    Setting setting;


    public AdapterActivity(List<Activities> activitiesList, Context context, AccountInformation accountInformation, Setting setting) {
        this.activitiesList = activitiesList;
        this.accountInformation = accountInformation;
        this.context = context;
        this.setting = setting;
    }

    @NonNull
    @Override
    public AdapterSheduleVH onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_activity, viewGroup, false);

        return new AdapterSheduleVH(view);
    }

    @Override
    public void onBindViewHolder(@NonNull final AdapterSheduleVH adapterSheduleVH, int i) {
        final Activities activities = activitiesList.get(i);

        // Если название активности состоит из нескольких слов встовляем перенос между ними
        String nameActivity = activities.getName().trim().replace(" ", " \n ");


        adapterSheduleVH.itemTopLayout.setBackgroundResource(setting.getItemTopLayout());


        adapterSheduleVH.start.setText(activities.getStart_time().substring(0,5) + " - " + activities.getEnd_time().substring(0,5));



        adapterSheduleVH.name.setText(nameActivity);

        adapterSheduleVH.linear.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                if(accountInformation.getUser().getPermission_to_activities().contains(activities.getId())){


                    ((FragmentActivity)context).getSupportFragmentManager().beginTransaction()
                            .replace(R.id.content_frame, QueueFragment.newInstance(activities.getId(), activities.getName())).addToBackStack(null).commit();


                }else{
                    Toast.makeText(context, "У вас нет доступа", Toast.LENGTH_LONG).show();
                }

            }
        });
    }

    @Override
    public int getItemCount() {
        return activitiesList.size();
    }


    // ViewHolder
    public class AdapterSheduleVH extends RecyclerView.ViewHolder {

        TextView name, start;
        LinearLayout linear, itemTopLayout, itemBottomLayout;

        public AdapterSheduleVH(@NonNull View itemView) {
            super(itemView);

            linear = itemView.findViewById(R.id.linear);
            name = itemView.findViewById(R.id.tvName);
            start = itemView.findViewById(R.id.tvStratTime);
            itemBottomLayout = itemView.findViewById(R.id.itemBottomLayout);
            itemTopLayout = itemView.findViewById(R.id.itemTopLayout);

        }
    }
}
