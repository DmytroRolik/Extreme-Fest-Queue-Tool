package com.example.goron.diplomadmin;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

public class SpacePhotoActivity extends AppCompatActivity {
    public static final String EXTRA_SPACE_PHOTO = "SpacePhotoActivity.SPACE_PHOTO";

    ImageView spacePhoto;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_space_photo);
        spacePhoto = findViewById(R.id.spacePhoto);


        String url = getIntent().getStringExtra(EXTRA_SPACE_PHOTO);

        Glide.with(this)
                .load(url)
                .asBitmap()
                .error(R.drawable.ic_cloud_off_red)
                .diskCacheStrategy(DiskCacheStrategy.RESULT)
                .into(spacePhoto);
    }
}
