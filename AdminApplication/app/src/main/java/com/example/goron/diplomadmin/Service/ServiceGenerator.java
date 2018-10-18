package com.example.goron.diplomadmin.Service;

import android.content.Context;
import android.text.TextUtils;

import com.example.goron.diplomadmin.Interceptors.AuthenticationInterceptor;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.google.gson.internal.bind.SqlDateTypeAdapter;

import okhttp3.Credentials;
import okhttp3.OkHttpClient;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ServiceGenerator {


    public static final String API_BASE_URL = "http://34185c4f.ngrok.io/api/v1/";
    public static final String API_BASE_URL_IMAGE = "http://34185c4f.ngrok.io/";


    private static OkHttpClient.Builder httpClient = new OkHttpClient.Builder();

    private static SqlDateTypeAdapter sqlAdapter = new SqlDateTypeAdapter();

    private static Gson gson = new GsonBuilder()
                                                .registerTypeAdapter(java.sql.Date.class, sqlAdapter)
                                                .setDateFormat("yyyy-MM-dd")
                                                .create();

    private static Retrofit.Builder builder = new Retrofit.Builder()
                                                                    .baseUrl(API_BASE_URL)
                                                                    .addConverterFactory(GsonConverterFactory.create(gson))
                                                                    .addConverterFactory(GsonConverterFactory.create());

    public static Retrofit retrofit = builder.build();



    public static <S> S createService(Class<S> serviceClass) {
        return createService(serviceClass, null, null);
    }//createService

    // username and password
    public static <S> S createService(Class<S> serviceClass, String username, String password) {

        if (!TextUtils.isEmpty(username) && !TextUtils.isEmpty(password)) {
            String authToken = Credentials.basic(username, password);
            return createService(serviceClass, authToken);
        }//if


        return createService(serviceClass, null);
    }//createService



    public static <S> S createService(Class<S> serviceClass, final String authToken) {

        if (!TextUtils.isEmpty(authToken)) {
            AuthenticationInterceptor interceptor = new AuthenticationInterceptor(authToken);

            if (!httpClient.interceptors().contains(interceptor)) {
                httpClient.addInterceptor(interceptor);
                builder.client(httpClient.build());
                retrofit = builder.build();
            }//if
        }//if

        return retrofit.create(serviceClass);
    }//createService
}
