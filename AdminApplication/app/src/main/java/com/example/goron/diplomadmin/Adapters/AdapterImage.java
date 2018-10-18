package com.example.goron.diplomadmin.Adapters;

import android.content.Context;
import android.content.Intent;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.example.goron.diplomadmin.R;
import com.example.goron.diplomadmin.Service.ServiceGenerator;
import com.example.goron.diplomadmin.SpacePhotoActivity;

import java.util.List;

public class AdapterImage extends RecyclerView.Adapter<AdapterImage.AdapterImageVH>  {

    List<String> urlList;
    Context context;



    public AdapterImage(Context context, List<String> urlList) {
        this.urlList = urlList;
        this.context = context;

    }

    @NonNull
    @Override
    public AdapterImageVH onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_image, viewGroup,false);
        return new AdapterImageVH(view);
    }

    @Override
    public void onBindViewHolder(@NonNull AdapterImageVH imageAdapterVH, int i) {
        String urlPhoto = ServiceGenerator.API_BASE_URL_IMAGE + urlList.get(i);
        ImageView imageView = imageAdapterVH.imageViewItem;

        Glide.with(context)
                .load(urlPhoto)
                .asBitmap()
                .placeholder(R.drawable.ic_cloud_off_red)
                .diskCacheStrategy(DiskCacheStrategy.RESULT)
                .into(imageView);

    }

    @Override
    public int getItemCount() {
        return urlList.size();
    }


    public class AdapterImageVH extends RecyclerView.ViewHolder implements View.OnClickListener {


        ImageView imageViewItem;

        public AdapterImageVH(@NonNull View itemView) {
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
