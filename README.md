# 🧪 Backend Developer Challenge – Laravel 10 Boilerplate

Welcome! This is the starting point for your backend coding challenge. It includes a basic Laravel 10 setup with a `users` table and a `transactions` table. Your job is to implement the logic described below using best practices and clear code structure.

## 🧠 Objective

You are building part of a game system where users can earn, spend, and request payouts using an in-game currency (1 SCR = 1 EUR). Your job is to implement endpoints that provide a summary of users’ transactions and allow them to request payouts.

The application will handle **millions of requests per day**, so your solution should consider performance, caching, and scalability.

---

## ✅ What’s Included in This Boilerplate

- Laravel 10 setup
- `User` model (default Laravel, integer IDs)
- `Transaction` model and migration
- SQLite-based database for quick setup
- Factory and seeder to create users and transactions

---

## 🔧 Setup Instructions

1. **Fork the repository**

   Fork the repository and navigate to the project folder


2. **Install dependencies**
    ```bash
    composer install
    ```

3. **Set up environment**
    ```bash
    cp .env.example .env
    touch database/database.sqlite
    php artisan key:generate
    ```

4. **Run migrations and seed data**

   Ensure that you update the DB_DATABASE variable in .env to the absolute path of the database.sqlite file.   

    ```bash
    php artisan migrate --seed
    ```

5. **Start the development server**
    ```bash
    php artisan serve
    ```

---

