# Spos Server

### Introduction

This project is a reference implementation of the server-side [Spos API](https://github.com/seorospa/spos-api), build on top of the [Lumen PHP Framework](https://github.com/laravel/lumen).

### Requirements
* **SERVER**: Apache 2 or NGINX.
* **RAM**: 1 GB or higher.
* **PHP**: 7.3 or higher.
* **Composer**: 2.1.3 or higher.

### Installation and configuration

~~~bash
git clone https://github.com/seorospa/spos-server
cd spos-server
composer install

cp .env.example .env # edit .env later
php artisan jwt:secret
php artisan migrate

# To run locally
php -S localhost:8000 -t public
~~~


### License
This implementation is under the [MIT License](https://github.com/seorospa/spos-server/blob/main/LICENSE).
