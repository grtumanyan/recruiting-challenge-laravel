## 💼 Payout System Overview

Provides a simple transaction-based payout system.

---

### ✅ Assumptions Made

- Each user has `earned`, `spent`, and `payout` transactions, all stored in a unified `transactions` table.
- Payouts have a `status`: `requested`, `approved`, or `paid`.
- Users may only request payouts up to their current available balance.
- Admins are responsible for approving payout requests.
- Transactions use UUIDs
- Caching strategy is time-sensitive (max 2-minute TTL)
- All endpoints requiring authentication use Laravel Sanctum (`auth:sanctum`)

---

### ⚡️ Caching and Aggregation Strategy

**Caching Summary Data**
- User balance summaries (earned, spent, payout breakdown) are cached (duration is **2 minutes**) per user under the key `cache-summary-user-{userId}`
- When transactions that affect balance are created or updated, the cache is **invalidated immediately**
- Chose TTL caching over Laravel Scheduler because it offers a more lightweight, efficient solution for cache management, automatically handling expiration without the need for additional scheduled tasks
---

## 🔌 API Endpoints

| Method | Endpoint                     | Description                                      |
|--------|------------------------------|--------------------------------------------------|
| GET    | /api/v1/users/{id}/summary   | Returns user's balance summary (cached)          |
| GET    | /api/v1/payouts/requests     | Lists total requested payouts per user           |
| POST   | /api/v1/users/{id}/payout    | Allows a user to request a payout                |
| PATCH  | /api/v1/payouts/{id}/approve | Allows admin to approve a payout request         |

---

## ✅ Feature Test Classes

#### `tests/Feature/PayoutRequestTest.php`
#### `tests/Feature/ApprovePayoutTest.php`
#### `tests/Feature/PayoutRequestListTest.php`
#### `tests/Feature/UserTransactionSummaryTest.php`

---

## 🧪 Unit Test Classes & Cases

#### `tests/Unit/UserTransactionSummaryServiceTest.php`

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

