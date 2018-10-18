package com.example.goron.diplomadmin;

import android.app.ProgressDialog;
import android.content.Intent;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.databinding.DataBindingUtil;
import android.graphics.Color;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.DisplayMetrics;
import android.view.View;
import android.widget.CompoundButton;
import android.widget.SimpleCursorAdapter;
import android.widget.Toast;

import com.example.goron.diplomadmin.Interface.Service;
import com.example.goron.diplomadmin.Manager.DbManager;
import com.example.goron.diplomadmin.Manager.SerializableManager;
import com.example.goron.diplomadmin.Model.AccountInformation;
import com.example.goron.diplomadmin.Model.Login;
import com.example.goron.diplomadmin.Service.ServiceGenerator;
import com.example.goron.diplomadmin.databinding.ActivityMainBinding;

import java.io.IOException;
import java.nio.channels.NoConnectionPendingException;
import java.util.HashMap;
import java.util.Timer;
import java.util.TimerTask;

import me.itangqi.waveloadingview.WaveLoadingView;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Converter;

public class MainActivity extends AppCompatActivity {

    // К какому LAYOUT привязан
    private static final int LAYOUT = R.layout.activity_main;

    // Binding
    private ActivityMainBinding binding;

    // Ошибка 401
    private final int UNAUTHORIZED = 401;

    private DbManager dbManager;


    // Имя файла для сохранения информации об акаунте
    private final  String FileNameAccountInformation = "AccountInformation.ser";
    private final  String FileNameLogin = "Login.ser";

    private Login login;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(LAYOUT);



        // Метод DataBindingUtil.setContentView внутри себя сделает привычный нам setContentView для Activity, а также настроит и вернет объект биндинга MainActivityBinding.
        binding = DataBindingUtil.setContentView(this, LAYOUT);

        // Стартовая анимация
        binding.waveLoadingView.setVisibility(View.GONE);
        binding.waveLoadingView.setShapeType(WaveLoadingView.ShapeType.SQUARE);
        binding.waveLoadingView.setProgressValue(10);
        binding.waveLoadingView.setAmplitudeRatio(60);
        binding.waveLoadingView.setTopTitleStrokeColor(Color.GREEN);
        binding.waveLoadingView.setTopTitleStrokeWidth(3);
        binding.waveLoadingView.setAnimDuration(1000);

        // Читаем информацию о входе из файла(Запомнить меня)
        login = SerializableManager.readSerializableObject(getApplicationContext(), "Login.ser");

        // если login не null и был установлен флажок запомни меня
        if(login != null && login.isCheck()){
            binding.textInputName.setText(login.getName());
            binding.textInputPassword.setText(login.getPassword());
            binding.checkBoxRememberMe.setChecked(login.isCheck());
        }//if

        // При нажатии на кнопку login переходим на активити StartActivity
        clickButtonGoToStartActivity();


        // При выборе checkBoxRememberMe
        binding.checkBoxRememberMe.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if(isChecked){
                    // Сохранить login and password из textView
                    Login login = new Login(binding.textInputName.getText().toString(), binding.textInputPassword.getText().toString(), isChecked);
                    SerializableManager.saveSerializableObject(getApplication(), login, FileNameLogin);
                }else {
                    // Сохранить login and password из textView
                    Login login = new Login("","", isChecked);
                    SerializableManager.saveSerializableObject(getApplication(), login, FileNameLogin);
                }
            }
        });



        // Временный код получения и отображение дисплей метрики
        DisplayMetrics displayMetrics = new DisplayMetrics();
        getWindowManager().getDefaultDisplay().getMetrics(displayMetrics);
        float density  = getResources().getDisplayMetrics().density;
        float height = displayMetrics.heightPixels / density;
        float width = displayMetrics.widthPixels / density;
        Toast.makeText(this, String.valueOf(height) + " - " + String.valueOf(width), Toast.LENGTH_LONG).show();

    }


    // При нажатии на кнопку login переходим на активити StartActivity
    private void clickButtonGoToStartActivity(){
        binding.goToStartActivity.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                // Запустить анимацию
                binding.waveLoadingView.startAnimation();
                // Спрятать главный relation
                binding.mainRelation.setVisibility(View.GONE);

                // Показать анимацию
                binding.waveLoadingView.setVisibility(View.VISIBLE);

                // Получить информацию об акаунте
                getAccountInformation();
            }
        });
    }//clickButtonGoToStartActivity


    // Получить информацию по аккаунту
    private void getAccountInformation() {

        // Повысить шкалу анимации до 20
        binding.waveLoadingView.setProgressValue(20);
        // Получить имя
        String name = binding.textInputName.getText().toString();
        // Получить пароль
        String password = binding.textInputPassword.getText().toString();

        // Повысить шкалу анимации до 30
        binding.waveLoadingView.setProgressValue(30);

        // Если TextInputEditText c  именем или паролем пустые
        if(TextUtils.isEmpty(name) || TextUtils.isEmpty(password)){
            binding.waveLoadingView.setVisibility(View.GONE);
            binding.mainRelation.setVisibility(View.VISIBLE);
            Toast.makeText(getApplicationContext(), "Имя и пароль должны быть заполнены", Toast.LENGTH_LONG).show();

        }else{
            // Повысить шкалу анимации до 60
            binding.waveLoadingView.setProgressValue(60);


                Call<AccountInformation> call = getService(name,password).getEmployeePermission();
                call.enqueue(new Callback<AccountInformation>() {
                    @Override
                    public void onResponse(Call<AccountInformation> call, retrofit2.Response<AccountInformation> response) {



                        if (response.isSuccessful()) {

                            // Повысить шкалу анимации до 80
                            binding.waveLoadingView.setProgressValue(80);
                            // Получить информацию из запроса права пользователя
                            AccountInformation accountInformation = response.body();


                            // Сохранить полученные права пользователя в файл для дальнейшего использования
                            SerializableManager.saveSerializableObject(getApplication(), accountInformation, FileNameAccountInformation);

                            binding.waveLoadingView.setProgressValue(100);

                            // Открыть стартовую активность
                            Intent intent = new Intent(getApplicationContext(), StartActivity.class);
                            startActivity(intent);



                            // Если ошибка 401 это ошибка авторизации
                        }else if(response.code() == UNAUTHORIZED) {
                            binding.waveLoadingView.setVisibility(View.GONE);
                            binding.mainRelation.setVisibility(View.VISIBLE);
                            Toast.makeText(getApplicationContext(), "Не верное имя или пароль", Toast.LENGTH_LONG).show();

                            // Любая другая ошибка
                        }else{
                            Toast.makeText(getApplicationContext(), String.valueOf(response.code()), Toast.LENGTH_LONG).show();
                        }

                    }

                    // Вызывается, когда произошло сетевое исключение, разговаривающее с сервером или когда возникло непредвиденное исключение, создающее запрос или обработку ответа.
                    @Override
                    public void onFailure(Call<AccountInformation> call, Throwable t) {

                        Toast.makeText(getApplicationContext(),t.toString(), Toast.LENGTH_LONG).show();
                    }

                });

        }//if


    }//getAccountInformation

    private Service getService(String name, String password){
        return ServiceGenerator.createService(Service.class, name, password);
    }

    @Override
    protected void onStop() {
        super.onStop();
        binding.waveLoadingView.setProgressValue(10);
        binding.mainRelation.setVisibility(View.VISIBLE);
        binding.waveLoadingView.setVisibility(View.GONE);
    }
}
