<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Fianut Backend API

REST API backend untuk ekosistem **Fianut** — platform multi-tenant SaaS untuk manajemen bisnis.

## Tech Stack

| Teknologi | Versi |
|-----------|-------|
| Laravel | 10.x |
| PHP | ^8.1 |
| Laravel Sanctum | ^3.2 |
| Laravel UI | ^4.2 (Bootstrap scaffolding) |
| Laravel Collective HTML | ^6.4 |
| Laravel Pint | ^1.0 (code style) |
| PHPUnit | ^10.1 |
| Vite + Tailwind CSS | v4 (asset pipeline) |

## Struktur Folder

```
learn-laravel-1/
├── app/
│   ├── Models/
│   │   └── Fianut/            # Domain models (User, Apps, Instances, dll.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/           # API controllers
│   │   │   └── ...            # Web controllers
│   │   └── Middleware/
│   ├── Helpers/
│   │   └── ItsHelper.php      # Custom helper utilities
│   └── Providers/
├── config/
│   ├── sanctum.php
│   ├── services.php
│   └── ...
├── database/
├── routes/
│   ├── api.php                # API route definitions
│   ├── web.php                # Web routes + CORS preflight
│   └── console.php
├── resources/
├── public/
├── storage/
├── tests/
├── artisan
├── vite.config.js
├── composer.json
└── .env.example
```

## API Modules

- **Auth** — register, login, logout, google-signin, verify-token
- **Admin** — app/pricing/instance-types/user-priviledges/payment/role/texts/settings management
- **Hello** — landing page, showcase, shop list, templates
- **Instance** — profile, employees, app list, manage, delete
- **Proficash** — summary, transactions, spending
- **Inventory** — list, detail, manage, delete

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev
php artisan serve
```

## Deployment

### Fly.io

- `fly launch` — create fly.toml config file
- `fly deploy` — deploy application to docker
- `fly ssh console`
- `flyctl image update`
- `fly apps restart [appname]`
- `fly apps destroy`
- `fly machine destroy`
- `flyctl auth login`

### PlanetScale

- `brew install planetscale/tap/pscale` — install PlanetScale CLI
- `pscale auth login`

To open DB in local, use `MYSQL_ATTR_SSL_CA=/etc/ssl/cert.pem`  
To open DB in production, use `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt`  
To open DB in workbench, disable save migration (also adjust the host, use general option in PlanetScale web)  
To connect in local, add config to `.env`  
To connect in production, add config to `fly.toml`
