package com.example.goron.userdiplom.Adapters;

import android.content.Context;
import android.content.Intent;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.LayoutAnimationController;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.example.goron.userdiplom.Manager.SerializableManager;
import com.example.goron.userdiplom.R;
import com.example.goron.userdiplom.Service.ServiceGenerator;
import com.example.goron.userdiplom.SpacePhotoActivity;

import java.util.ArrayList;
import java.util.List;

public class ImageAdapter extends RecyclerView.Adapter<ImageAdapter.ImageAdapterVH>  {


    List<String> urlList;
    Context context;



    public ImageAdapter(Context context, List<String> urlList) {
        this.urlList = urlList;
        this.context = context;

    }

    @NonNull
    @Override
    public ImageAdapterVH onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_image, viewGroup,false);
        return new ImageAdapterVH(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ImageAdapterVH imageAdapterVH, int i) {
        String photo = ServiceGenerator.API_BASE_URL_IMAGE + urlList.get(i);
        ImageView imageView = imageAdapterVH.imageViewItem;

        Glide.with(context)
                .load(photo)
                .diskCacheStrategy(DiskCacheStrategy.ALL)
                .placeholder(R.drawable.ic_cloud_off_red)
                .into(imageView);

    }

    @Override
    public int getItemCount() {
        return urlList.size();
    }


    public class ImageAdapterVH extends RecyclerView.ViewHolder implements View.OnClickListener {


        ImageView imageViewItem;

        public ImageAdapterVH(@NonNull View itemView) {
            super(itemView);

            imageViewItem = itemView.findViewById(R.id.imageViewItem);

            itemView.setOnClickListener(this);
        }


        @Override
        public void onClick(View v) {
            int position = getAdapterPosition();
            if(position != RecyclerView.NO_POSITION) {
                String spacePhoto = ServiceGenerator.API_BASE_URL_IMAGE + urlList.get(position);
                Intent intent = new Intent(context, SpacePhotoActivity.class);
                intent.putExtra(SpacePhotoActivity.EXTRA_SPACE_PHOTO, spacePhoto);
                context.startActivity(intent);
            }
        }
    }
}
