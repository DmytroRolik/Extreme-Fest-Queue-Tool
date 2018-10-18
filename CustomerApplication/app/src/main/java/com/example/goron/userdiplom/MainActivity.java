package com.example.goron.userdiplom;

import android.content.Intent;
import android.support.design.widget.TextInputEditText;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.TextView;
import android.widget.Toast;

import com.example.goron.userdiplom.Interface.Service;
import com.example.goron.userdiplom.Manager.DbManager;
import com.example.goron.userdiplom.Manager.SerializableManager;
import com.example.goron.userdiplom.Model.DatesFestival;
import com.example.goron.userdiplom.Model.Login;
import com.example.goron.userdiplom.Model.PostTokken;
import com.example.goron.userdiplom.Model.RequestTokken;
import com.example.goron.userdiplom.Service.ServiceGenerator;

import java.util.HashMap;

import retrofit2.Call;
import retrofit2.Callback;

public class MainActivity extends AppCompatActivity {


    // Элементы интерфейса
    private TextInputEditText textInputPassword, textInputName;
    private CheckBox checkBoxRememberMe;
    Button goToStartActivity;

    private final  String FileNameLogin = "Login.ser";
    private final  String FileNameDatesFestival = "DatesFestival.ser";
    // Ошибка 401
    private final int UNAUTHORIZED = 401;

    private DbManager dbManager;

    // Объект login
    private Login login;

    // Объект хранящий дату фестивалей
    private DatesFestival datesFestival;
    public  Call<DatesFestival> callDate;

    private Call<PostTokken> callPostTokken;



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        dbManager = new DbManager(getApplicationContext());

        HashMap<String,String> userHashMap = dbManager.getUserData();

        if(userHashMap.size() > 0){
            setContentView(R.layout.startlogin);
            getDateToShedule(userHashMap.get("login"),userHashMap.get("password"));

        }else {
            setContentView(R.layout.activity_main);

            // Инициализируем элементы:
            textInputPassword = findViewById(R.id.textInputPassword);
            textInputName = findViewById(R.id.textInputName);
            checkBoxRememberMe = findViewById(R.id.checkBoxRememberMe);
            goToStartActivity = findViewById(R.id.goToStartActivity);


            // Читаем информацию о входе из файла(Запомнить меня)
            login = SerializableManager.readSerializableObject(getApplicationContext(), FileNameLogin);

            // Обработчик нажатия на кнопку вход
            clickButtonGoToStartActivity();


            // Сохранения логина и пароля
            loginSave();


            // При выборе checkBoxRememberMe
            checkBoxRememberMeCheced();
        }
    }

    // При выборе checkBoxRememberMe
    private void checkBoxRememberMeCheced() {
        checkBoxRememberMe.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if(isChecked){
                    // Сохранить login and password из textView
                    Login login = new Login(textInputName.getText().toString(), textInputPassword.getText().toString(), isChecked);
                    SerializableManager.saveSerializableObject(getApplication(), login, FileNameLogin);
                }else {
                    // Сохранить login and password из textView
                    Login login = new Login("","", isChecked);
                    SerializableManager.saveSerializableObject(getApplication(), login, FileNameLogin);
                }
            }
        });
    }

    // Сохранения логина и пароля
    private void loginSave() {
        // если login не null и был установлен флажок запомни меня
        if(login != null && login.isCheck()){
            textInputName.setText(login.getName());
            textInputPassword.setText(login.getPassword());
            checkBoxRememberMe.setChecked(login.isCheck());
        }//if
    }//loginSave

    // Обработчик нажатия на кнопку вход
    private void clickButtonGoToStartActivity() {
        goToStartActivity.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                getDateToShedule(null, null);
            }
        });
    }//clickButtonGoToStartActivity




    // Вернуть все даты фестиваля (Каждый раз делать не разумно наверное стоит сохранять в файл)
    private void getDateToShedule(String nameFromDb, String passwordFromDb){

        final  String name, password;

        if(nameFromDb != null && passwordFromDb != null){
           name = nameFromDb;
           password = passwordFromDb;
        }else {
           name = textInputName.getText().toString();
           password = textInputPassword.getText().toString();
        }


        // Если TextInputEditText c  именем или паролем пустые
        if(TextUtils.isEmpty(name) || TextUtils.isEmpty(password)){
            Toast.makeText(getApplicationContext(), "Имя и пароль должны быть заполнены", Toast.LENGTH_LONG).show();

        }else {

            callDate = getService(name, password).getDatesFestival();

            callDate.enqueue(new Callback<DatesFestival>() {
                @Override
                public void onResponse(Call<DatesFestival> call, retrofit2.Response<DatesFestival> response) {
                    if (response.isSuccessful()) {

                        dbManager.addUserData(name,password);

                        // Получить даты фестиваля
                        datesFestival = response.body();

                        // Сохранить полученные права пользователя в файл для дальнейшего использования
                        SerializableManager.saveSerializableObject(getApplication(), datesFestival, FileNameDatesFestival);

                        postTokken();

                        // Если ошибка 401 это ошибка авторизации
                    } else if (response.code() == UNAUTHORIZED) {
                        Toast.makeText(getApplicationContext(), "Не верное имя или пароль", Toast.LENGTH_LONG).show();
                        // Любая другая ошибка
                    } else {
                        Toast.makeText(getApplicationContext(), "error response, no access to resource?", Toast.LENGTH_LONG).show();

                    }
                }

                @Override
                public void onFailure(Call<DatesFestival> call, Throwable t) {
                    Toast.makeText(getApplicationContext(), t.toString(), Toast.LENGTH_LONG).show();
                }
            });
        }
    }


    // Получить сервис для работы с сервером
    private Service getService(String name, String password){
        return ServiceGenerator.createService(Service.class, name, password);
    }


    private Service getService(){
        return ServiceGenerator.createService(Service.class);
    }

    private void postTokken(){


        String tokken = dbManager.getFirebaseToken();

        RequestTokken requestTokken = new RequestTokken(tokken);

        callPostTokken =  getService().postToken(requestTokken);

        callPostTokken.enqueue(new Callback<PostTokken>() {
            @Override
            public void onResponse(Call<PostTokken> call, retrofit2.Response<PostTokken> response) {
                if (response.isSuccessful()) {


                    Boolean isSuccess = response.body().isSuccess();

                    // Открыть стартовую активность
                    Intent intent = new Intent(getApplicationContext(), StartActivity.class);
                    startActivity(intent);

                    // Если ошибка 401 это ошибка авторизации
                } else if (response.code() == UNAUTHORIZED) {
                    Toast.makeText(getApplicationContext(), "Не верное имя или пароль", Toast.LENGTH_LONG).show();
                    // Любая другая ошибка
                } else {
                    Toast.makeText(getApplicationContext(), "Сервер временно недоступен. Повторите попытку позже или обратитесь к администрации.", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<PostTokken> call, Throwable t) {
                Toast.makeText(getApplicationContext(), t.toString(), Toast.LENGTH_LONG).show();
            }
        });

    }

}
