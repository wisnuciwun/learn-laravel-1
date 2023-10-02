<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Cubet Techno Labs](https://cubettech.com)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[Many](https://www.many.co.uk)**
-   **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
-   **[DevSquad](https://devsquad.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[OP.GG](https://op.gg)**
-   **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
-   **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# NOTE

## how to make hot reload :

npm install
php artisan serve
npm run dev

add @vite([]) to the blade

## how to create controller :

php artisan make:controller PagesController

php artisan make:controller PostController --resource

## how to create model :

php artisan make:model Post -m

## how to migrate :

php artisan migrate

## how to use tinker :

php artisan tinker

## how to use laravel collective :

composer require laravelcollective/html

App\Models\Post::count()

```
$post->updated_at='12/12/2023
```

```
$post->save();
```

## how to add bootstrap :

composer require laravel/ui
php artisan ui bootstrap
php artisan ui bootstrap --auth

```html
<script src="{{ asset('js/app.js') }}" defer></script>
<link href="{{ asset('css/app.css') }}" rel="stylesheet" />
```

## how to add php artisan make:auth (because it no longer exist on laravel 5.2+)

(add = composer require laravel/ui if you never done before)
php artisan ui vue --auth
php artisan migrate

## how to create model relationship :

create function as on Post.php and User.php
check table you want to relate, if user_id doesn't exist yet do a migrate with php artisan make:migration
add some code in up() like in new_post_table_with_userid.php
then php artisan migrate

## how to add table :

php artisan make:migration create_images_table --create=images

## how to create symlink to access image on public folder :

php artisan storage:link --> don't forget hit this each time upload to fly

## FLY.IO

- fly launch : to create fly.toml config file
- fly deploy : create application to docker
- fly ssh console
- flyctl image update
- fly apps restart [appname]
- fly apps destroy
- fly machine destroy
- flyctl auth login

# PLANETSCALE

- brew install planetscale/tap/pscale : install planet scale
- pscale auth login

to open db in local, use MYSQL_ATTR_SSL_CA=/etc/ssl/cert.pem
to open db in production, use MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
to open db in workbench, disable save migration  (also adjust the host, use general option in planetscale web)
to connect in local, add config to .env
to connect in production, add config to fly.toml
