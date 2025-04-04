# AppFront - Laravel Setup Guide

This repository contains the Laravel-based AppFront application. Follow the instructions below to set up and run the project locally.

## Requirements

- PHP >= 8.2
- Composer
- Laravel 10+
- MySQL

---

## Installation

1. **Clone the Repository**

```bash
git clone https://github.com/hardikthinktank/appfront
cd appfront

#Install PHP Dependencies
composer install

#Copy the example environment configuration:
cp .env.example .env

#Update .env with your local database and mail configuration:\

DB_DATABASE=appfront
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

#Run Migrations
php artisan migrate

#Seed the Database 
php artisan db:seed

#Start the Server
php artisan serve

# update a product and trigger a price change notification
php artisan product:update {id} --name="New Name" --price=123.45


