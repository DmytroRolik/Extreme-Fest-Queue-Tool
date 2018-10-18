package com.example.goron.diplomadmin.Adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.TextView;

import com.example.goron.diplomadmin.Model.DatesFestival;
import com.example.goron.diplomadmin.Model.Schedule;
import com.example.goron.diplomadmin.R;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class AdapterSchedule extends BaseExpandableListAdapter {


    Context context;
    List<Schedule> scheduleList;

    List<Date> dates;

    Map<Date, List<Schedule>> dateScheduleMap = new HashMap<>();


    // Конструктор с параметроми
    public AdapterSchedule(Context context, List<Schedule> scheduleList, DatesFestival datesFestival) {
        this.context = context;
        this.scheduleList = scheduleList;
        this.dates = datesFestival.getDates();


        //
        for (Schedule objects : scheduleList) {
            if (dateScheduleMap.containsKey(objects.getDate())) {
                List<Schedule> list = dateScheduleMap.get(objects.getDate());
                list.add(objects);
                dateScheduleMap.put(objects.getDate(), list);
            } else {
                List<Schedule> list = new ArrayList<>();
                list.add(objects);
                dateScheduleMap.put(objects.getDate(), list);
            }
        }
    }//AdapterSchedule



    @Override
    public int getGroupCount() {
        return dates.size();
    }

    @Override
    public int getChildrenCount(int groupPosition) {
        return dateScheduleMap.get(dates.get(groupPosition)).size();
    }



    @Override
    public Object getGroup(int groupPosition) {
        return dates.get(groupPosition);
    }

    @Override
    public Object getChild(int groupPosition, int childPosition) {
        return dateScheduleMap.get(dates.get(groupPosition)).get(childPosition);
    }

    @Override
    public long getGroupId(int groupPosition) {
        return groupPosition;
    }

    @Override
    public long getChildId(int groupPosition, int childPosition) {
        return childPosition;
    }

    @Override
    public boolean hasStableIds() {
        return false;
    }

    @Override
    public View getGroupView(int groupPosition, boolean isExpanded, View convertView, ViewGroup parent) {
        Date headerTitle = (Date)getGroup(groupPosition);

        if(convertView == null){
            LayoutInflater layoutInflater = (LayoutInflater)this.context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = layoutInflater.inflate(R.layout.list_group, null);
        }

        TextView lblListHeader = convertView.findViewById(R.id.lblListHeader);
        lblListHeader.setText(new SimpleDateFormat("yyyy-MM-dd").format(headerTitle));



        return convertView;
    }

    @Override
    public View getChildView(int groupPosition, int childPosition, boolean isLastChild, View convertView, ViewGroup parent) {
        final Schedule schedule = (Schedule)getChild(groupPosition,childPosition);

        if(convertView == null){
            LayoutInflater layoutInflater = (LayoutInflater)this.context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = layoutInflater.inflate(R.layout.item_schedule, null);
        }


        TextView textAction = convertView.findViewById(R.id.lblListItemActionName);
        TextView textTime = convertView.findViewById(R.id.lblListItemActionTime);


        String[] names =  schedule.getName().trim().split(" ");

        if(names.length > 1){
            textTime.setText( schedule.getStart_time().substring(0,5) +" - "+ schedule.getEnd_time().substring(0,5) + "\n");
        }else {
            textTime.setText(schedule.getStart_time().substring(0,5) +" - "+ schedule.getEnd_time().substring(0,5));
        }


        textAction.setText(schedule.getName());


        return convertView;
    }

    @Override
    public boolean isChildSelectable(int groupPosition, int childPosition) {
        return true;
    }


}
