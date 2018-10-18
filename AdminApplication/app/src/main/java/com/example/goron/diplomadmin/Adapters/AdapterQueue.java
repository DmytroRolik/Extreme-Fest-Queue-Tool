package com.example.goron.diplomadmin.Adapters;

import android.databinding.DataBindingUtil;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.example.goron.diplomadmin.Model.Users;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.databinding.ItemQueueBinding;

import java.util.List;

public class AdapterQueue extends RecyclerView.Adapter<AdapterQueue.QueueAdapterVH>{


    List<Users> usersList;


    public AdapterQueue(List<Users> usersList) {
        this.usersList = usersList;

    }

    @NonNull
    @Override
    public QueueAdapterVH onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
        return new QueueAdapterVH(LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_queue, viewGroup, false));
    }


    @Override
    public void onBindViewHolder(@NonNull QueueAdapterVH queueAdapterVH, int i) {
        Users users = usersList.get(i);


        queueAdapterVH.binding.name.setText(users.getName());
        queueAdapterVH.binding.number.setText(users.getNumber());

    }

    @Override
    public int getItemCount() {
        return usersList.size();
    }


    // ViewHolder
    public class QueueAdapterVH extends RecyclerView.ViewHolder{

        ItemQueueBinding binding;
        public QueueAdapterVH(@NonNull View itemView) {
            super(itemView);

            binding = DataBindingUtil.bind(itemView);
        }
    }

}