<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

---

# Laravel Basic App

This is a starter Laravel 12-based admin panel project powered by **FilamentPHP**. It includes user authentication, role and permission control, and country management. Ideal as a foundation for building admin dashboards and back-office systems.

---

## 🎯 Features

- Laravel 12 with Breeze authentication
- Admin panel using FilamentPHP 3
- Role and permission management with Spatie
- User CRUD with role assignment
- Role and Permission CRUD
- Country management module
- Route protection based on user roles
- CRUD generation with `ibex/crud-generator`

---

## 🧰 Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL or PostgreSQL
- PHP extensions required by Laravel

---

## 🚀 Quick Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/basic.git
cd basic

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Copy environment file
cp .env.example .env
php artisan key:generate

# 4. Configure your database in the .env file

# 5. Run migrations and seeders
php artisan migrate --seed

# 6. Start the development server
php artisan serve

