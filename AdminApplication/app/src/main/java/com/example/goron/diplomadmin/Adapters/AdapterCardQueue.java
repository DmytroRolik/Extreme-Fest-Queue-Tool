package com.example.goron.diplomadmin.Adapters;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.example.goron.diplomadmin.Model.UserInQueue;
import com.example.goron.diplomadmin.Model.Users;
import com.example.goron.diplomadmin.R;

import java.util.List;

public class AdapterCardQueue extends BaseAdapter {

    List<Users> usersList;
    Activity activity;


    public AdapterCardQueue(UserInQueue userInQueue, Activity activity) {
        this.usersList = userInQueue.getUsers();
        this.activity = activity;
    }//AdapterCardQueue

    @Override
    public int getCount() {
        return usersList.size();
    }

    @Override
    public Object getItem(int i) {
        return usersList.get(i);
    }

    @Override
    public long getItemId(int i) {
        return i;
    }

    @Override
    public View getView(int i, View view, ViewGroup viewGroup) {

        Users users = usersList.get(i);
        CardsAdapterVH holder;


        if (view == null) {

            LayoutInflater layoutInflater = (LayoutInflater) activity.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
            view = layoutInflater.inflate(R.layout.item_card_queue, null);
            holder = new CardsAdapterVH(view);
            view.setTag(holder);
        }else{
            holder = new CardsAdapterVH(view);
        }

        holder.name.setText(users.getName());
        holder.number.setText(users.getNumber());

        return view;
    }


    // ViewHolder
    private class CardsAdapterVH{

        TextView name, number;

        public CardsAdapterVH(View view) {
            name = view.findViewById(R.id.name);
            number = view.findViewById(R.id.number);
        }
    }

}
