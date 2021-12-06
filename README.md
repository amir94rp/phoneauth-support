## About PhoneAuth

This package built on top of Laravel Breeze and changes laravel default email based authentication to phone number auth.


### Install

First, you should create a new Laravel application, configure your database, and run your database migrations. Once you have created a new Laravel application, you may install PhoneAuth using Composer:

```php
composer require amir94rp/phoneauth
```

After Composer has installed the PhoneAuth package, you may run the phoneauth:install Artisan command. This command publishes the authentication views, routes, controllers, and other resources to your application.

```php
php artisan phoneauth:install

npm install
npm run dev
php artisan migrate
```

## License

The Laravel package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
