![](readme.png)
#Queue tool

Laravel

##Backend Installation

Clone repo

```sh
git clone https://github.com/VsevolodLoboda/queuetool
```

Install composer dependencies

```sh
cd localsite_directory && composer install
```

Copy .env file as main config from example
```sh
cp .env.example .env
```

Set in .env file you database settings and run migrations
```sh
php artisan migrate
```