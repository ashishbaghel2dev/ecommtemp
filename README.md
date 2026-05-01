# eCommerce Laravel Template

A Laravel eCommerce starter project with client and admin layout scaffolding, custom authentication, social login routes, and role-based access control. It is set up as a foundation for building storefront, product catalog, cart, checkout, order, and admin dashboard features.

## Features

- Laravel 13 project structure
- Email and password registration
- Email and password login
- Google and Facebook login routes with Laravel Socialite
- Role enum for `user`, `admin`, and `super_admin`
- Role middleware for protected routes
- User status, avatar, phone, verification, and login tracking fields
- Soft deletes for users
- Separate Blade layout folders for client and admin views
- Vite asset pipeline with Bootstrap, Sass, and Tailwind CSS
- PHPUnit test setup

## Tech Stack

- PHP `^8.3`
- Laravel `^13.0`
- Laravel Socialite
- Laravel UI
- Vite
- Bootstrap
- Tailwind CSS
- Sass
- PHPUnit

## Requirements

Install these before running the project:

- PHP 8.3 or newer
- Composer
- Node.js and npm
- MySQL, MariaDB, PostgreSQL, or SQLite

## Installation

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the Laravel app key:

```bash
php artisan key:generate
```

Update the database settings in `.env`, then run migrations:

```bash
php artisan migrate
```

Build frontend assets:

```bash
npm run build
```

## Running The App

Start Laravel:

```bash
php artisan serve
```

Start Vite in a second terminal:

```bash
npm run dev
```

The local app usually runs at:

```text
http://127.0.0.1:8000
```

You can also use the combined development command:

```bash
composer run dev
```

This runs the Laravel server, queue listener, log tailing, and Vite together.

## Authentication Routes

```text
GET  /register                Show registration page
POST /register                Create a new user account
GET  /login                   Show login page
POST /login                   Login with email and password
GET  /auth/google/redirect    Redirect to Google
GET  /auth/google/callback    Handle Google callback
GET  /auth/facebook/redirect  Redirect to Facebook
GET  /auth/facebook/callback  Handle Facebook callback
```

After login, users are redirected by role:

```text
admin        -> /admin/dashboard
super_admin  -> /super-admin/dashboard
user         -> /dashboard
```

## Social Login Setup

Social authentication uses Laravel Socialite. Add provider credentials to `.env` and make sure `config/services.php` contains matching provider configuration.

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=
```

Local callback URLs:

```text
http://127.0.0.1:8000/auth/google/callback
http://127.0.0.1:8000/auth/facebook/callback
```

## Roles And Middleware

Roles are defined in:

```text
app/Enums/UserRole.php
```

Available roles:

- `user`
- `admin`
- `super_admin`

The role middleware is implemented in:

```text
app/Http/Middleware/RoleMiddleware.php
```

It is registered in:

```text
bootstrap/app.php
```

Example protected route:

```php
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return "Admin Dashboard";
        });
    });
```

## Main Routes

```text
GET /                  Client home page
GET /home              Authenticated home/dashboard view
GET /login             Login page
POST /login            Login action
GET /register          Register page
POST /register         Register action
GET /admin/dashboard   Admin dashboard placeholder
```

## Project Structure

```text
app/
  Enums/               User role enum
  Http/Controllers/    Auth and home controllers
  Http/Middleware/     Role middleware
  Models/              Eloquent models

database/
  migrations/          Database schema
  seeders/             Seed data

resources/
  views/auth/          Login and registration views
  views/client/        Client-facing layout and pages
  views/admin/         Admin layout includes
  sass/                Sass entry files
  js/                  JavaScript entry files

routes/
  web.php              Web routes
```

## Useful Commands

Run migrations:

```bash
php artisan migrate
```

Run tests:

```bash
composer test
```

Format PHP code:

```bash
vendor/bin/pint
```

Build production assets:

```bash
npm run build
```

Clear config cache:

```bash
php artisan config:clear
```

## Current Notes

- The client home page is currently a basic placeholder.
- The admin dashboard route currently returns a placeholder response.
- Product, category, cart, checkout, order, and admin CRUD modules still need to be implemented.
- `/dashboard` and `/super-admin/dashboard` are referenced by login redirects but are not fully defined yet.

## License

This project is based on Laravel and uses the MIT license.
