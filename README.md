# Smart Subscription API

## Overview

This project is a **subscription & usage-based billing API** built with **Laravel 10**. It supports:

* User authentication using JWT
* Subscription management (subscribe, upgrade, downgrade)
* Usage tracking per billing cycle
* Automated billing cycle renewal using Laravel Scheduler
* Clean, testable API architecture with feature tests

The project is designed to reflect **real-world SaaS backend patterns**, not just CRUD operations.

## Design Assumptions

- A user can have only one active subscription at a time
- Billing cycles are monthly
- Usage resets at the start of each cycle
- Downgrades apply in the next billing cycle
- Upgrades apply immediately
- JWT is used for stateless API authentication

---

## Why Laravel 10 instead of Node.js?

While Node.js is a strong choice for backend APIs, **Laravel 10 was chosen intentionally** for this project due to the following reasons:

### 1. Productivity & Convention

Laravel provides:

* Built-in routing, validation, authentication, migrations, and testing
* Strong conventions that reduce boilerplate
* Faster development with fewer external libraries

This allows focusing more on **business logic (subscriptions, billing cycles)** rather than framework setup.

### 2. Built-in Ecosystem

Laravel offers first-class support for:

* Authentication guards
* Middleware
* Job scheduling (Cron)
* Database migrations & seeders
* Feature & unit testing

In Node.js, many of these require manual setup using multiple libraries.

### 3. Clean Architecture for APIs

Laravel encourages:

* Thin controllers
* Fat models
* Clear separation of concerns

This makes the codebase **easier to reason about and maintain**, especially for complex billing logic.

### 4. Interview & Enterprise Readiness

Laravel is widely used in:

* SaaS products
* Enterprise backends
* Subscription-based systems

This project demonstrates **production-level Laravel knowledge**, including testing and scheduling.

---

## Tech Stack

### Backend

* **Laravel 10** (PHP 8.1+)
* **JWT Authentication** (php-open-source-saver/jwt-auth)
* **MySQL** (production & testing)

### Testing

* Laravel Feature Tests
* PHPUnit
* Separate testing database

### Tooling

* Laravel Scheduler (Cron)
* Eloquent ORM
* RESTful JSON APIs

---

## Project Architecture

The project follows a **layered API architecture**:

```
app/
├── Console/
│   └── Commands/
│       └── RenewSubscriptions.php
│
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── PlanController.php
│   │   ├── SubscriptionController.php
│   │   └── UsageController.php
│   │
│   ├── Middleware/
│   └── Resources/
│       └── PlanResource.php
│
├── Models/
│   ├── User.php
│   ├── Plan.php
│   ├── Subscription.php
│   └── UsageCounter.php
│
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
│
└── tests/
    └── Feature/
```

### Key Design Decisions

* **Controllers** handle request/response only
* **Models** manage relationships and data
* **Usage counters** are stored per billing cycle (history preserved)
* **Subscription upgrades** apply immediately
* **Subscription downgrades** are scheduled for the next cycle

---

## Subscription & Billing Design

### Core Tables

* `plans` – subscription plans
* `subscriptions` – active subscription per user
* `usage_counters` – usage per billing cycle

### Important Rules

* One active subscription per user
* One usage record per billing cycle
* New usage row created on each renewal
* Downgrade uses `next_plan_id`

This mirrors how real SaaS billing systems work.

---

## Project Setup

### Prerequisites

* PHP 8.1+
* Composer
* MySQL

### 1. Clone the repository

```bash
git clone <repository-url>
cd smart_subscription_api
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment setup

```bash
cp .env.example .env
```

Configure database credentials in `.env`.

Generate keys:

```bash
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider
php artisan key:generate
php artisan jwt:secret
```

### 4. Run migrations & seeders

```bash
php artisan migrate --seed
```

### 5. Start the server

```bash
php artisan serve
```

API will be available at:

```
http://localhost:8000/api
```

---

## Running Tests

### Test Environment

* Uses a separate database (`.env.testing`)
* Safe and isolated from production data

### Run all tests

```bash
php artisan test
```

Tests cover:

* Authentication
* Subscription creation
* Preventing duplicate subscriptions
* Usage consumption
* Protected routes

---

## Scheduler / Cron Setup

### Laravel Scheduler

The billing cycle reset runs via:

```bash
php artisan subscription:renew
```

Scheduled in `Console/Kernel.php`:

```php
$schedule->command('subscription:renew')->daily();
```

### Server Cron

```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

Database Schema

Below is a high-level overview of the database schema used in the project. The schema is designed to support subscription-based billing with usage tracking per billing cycle.

users

Stores application users.

Users table created via laravel default users table via migration


plans

Defines available subscription plans.
---
id (PK, bigint)
name (string)
price (decimal)
monthly_limit (integer, nullable)
is_unlimited (boolean)
timestamps


subscriptions

Represents a user's subscription.
---
id (PK, bigint)
user_id (FK → users.id, bigint)
plan_id (FK → plans.id, bigint)
next_plan_id (FK → plans.id, bigint, nullable)
start_date (date)
current_cycle_start (date)
current_cycle_end (date)
status (enum: active | inactive)
timestamps

usage_counters

Tracks usage per billing cycle.
---
id (PK, bigint)
user_id (FK → users.id, bigint)
subscription_id (FK → subscriptions.id, bigint)
used_units (integer)
cycle_start (date)
cycle_end (date)
timestamps

Schema Relationships

User => Subscription: One-to-One

Subscription => UsageCounters: One-to-Many (one per cycle)

Plan => Subscription: One-to-Many

This schema ensures:
Historical usage is preserved
Plan upgrades/downgrades are handled safely
Billing cycles are deterministic and auditable

## Summary

This project demonstrates:

* Real-world subscription billing logic
* JWT-secured APIs
* Clean Laravel architecture
* Automated billing cycles
* Proper feature testing

It is designed to be **interview-ready, production-oriented, and scalable**.
