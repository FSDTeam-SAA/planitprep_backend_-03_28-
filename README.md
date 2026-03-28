<div align="center">

# 🍽️ PlanIt Prep — Backend API Server

### AI-Powered Personal Nutritionist & Dietician Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![OpenAI](https://img.shields.io/badge/OpenAI-GPT--4o-412991?style=for-the-badge&logo=openai&logoColor=white)](https://openai.com)
[![Stripe](https://img.shields.io/badge/Stripe-Payments-008CDD?style=for-the-badge&logo=stripe&logoColor=white)](https://stripe.com)
[![Sanctum](https://img.shields.io/badge/Auth-Sanctum-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

**The server-side engine powering personalized AI diet plans, macro tracking, subscription billing, and a full admin CMS — serving the Flutter mobile app and web front-end.**

---

[Architecture](#-system-architecture) · [API Reference](#-api-reference) · [AI Engine](#-ai-diet-plan-engine) · [Database](#-database-schema) · [Setup](#-installation--setup) · [Admin Panel](#-admin-panel-cms)

</div>

---

## 📋 Table of Contents

1. [Overview](#-overview)
2. [System Architecture](#-system-architecture)
3. [Technology Stack](#-technology-stack)
4. [Project Structure](#-project-structure)
5. [API Reference](#-api-reference)
6. [AI Diet Plan Engine](#-ai-diet-plan-engine)
7. [Authentication & Security](#-authentication--security)
8. [Database Schema](#-database-schema)
9. [Eloquent Models](#-eloquent-models)
10. [Background Jobs & Queues](#-background-jobs--queues)
11. [Payment & Subscription System](#-payment--subscription-system)
12. [Admin Panel (CMS)](#-admin-panel-cms)
13. [Notification System](#-notification-system)
14. [API Resources & Collections](#-api-resources--collections)
15. [Third-Party Integrations](#-third-party-integrations)
16. [Environment Configuration](#-environment-configuration)
17. [Installation & Setup](#-installation--setup)
18. [Running the Application](#-running-the-application)
19. [Database Migrations](#-database-migrations)
20. [Testing](#-testing)
21. [Deployment](#-deployment)
22. [Troubleshooting](#-troubleshooting)
23. [Contributing](#-contributing)
24. [License](#-license)

---

## 🌟 Overview

**PlanIt Prep Backend** is a Laravel 11 RESTful API server that serves as the central intelligence layer for the PlanIt Prep nutrition platform. It orchestrates:

- **AI-powered diet plan generation** via OpenAI GPT-4o with full user-profile-aware prompt engineering
- **Surprise meal generation** with appliance-aware recipes and grocery lists
- **Batch meal preparation** planning with portion tracking and inventory management
- **Real-time macro & calorie tracking** with daily/weekly aggregation
- **Stripe subscription billing** with webhooks for lifecycle management
- **Multi-step user onboarding** (24+ health parameters collected and stored)
- **Full admin CMS** for managing food items, meal plans, users, packages, coupons, and site content
- **Push notifications** via Firebase Cloud Messaging (FCM)
- **Web front-end** for user registration, weight/food tracking, BMI calculator, and dashboards

### Technical Metadata

| Property | Value |
|---|---|
| **Framework** | Laravel 11.9+ |
| **PHP Version** | 8.2+ |
| **Authentication** | Laravel Sanctum (token-based) |
| **AI Provider** | OpenAI GPT-4o (configurable model) |
| **Payment Gateway** | Stripe (subscriptions + one-time) + Razorpay |
| **SMS/OTP Provider** | MSG91 |
| **Push Notifications** | Firebase Cloud Messaging (FCM) |
| **Database** | MySQL / MariaDB |
| **Queue Driver** | Laravel Queue (database/redis) |
| **Admin Panel** | Custom Blade-based CMS at `/secureAdmin` |
| **API Prefix** | `/api/v1/` |
| **Production URL** | `https://planitprep.cloud/` |

---

## 🏗 System Architecture

```
┌────────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                                │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────────┐ │
│  │ Flutter App   │  │ Web Frontend │  │ Admin CMS (Blade Views) │ │
│  │ (iOS/Android) │  │ (Blade SSR)  │  │ /secureAdmin/*          │ │
│  └──────┬───────┘  └──────┬───────┘  └───────────┬──────────────┘ │
└─────────┼─────────────────┼──────────────────────┼────────────────┘
          │ REST API         │ Web Routes            │ Web Routes
          ▼                  ▼                       ▼
┌────────────────────────────────────────────────────────────────────┐
│                     MIDDLEWARE LAYER                                │
│  ┌─────────────────────────────────────────────────────────────┐  │
│  │ CheckValidRequestBasedOnUserAndKey  (API User + Key check)  │  │
│  │ auth:sanctum  (Bearer token authentication)                 │  │
│  │ adminAuth:admin  (Admin session guard)                      │  │
│  └─────────────────────────────────────────────────────────────┘  │
└────────────────────────────┬───────────────────────────────────────┘
                             ▼
┌────────────────────────────────────────────────────────────────────┐
│                    CONTROLLER LAYER (Api/)                          │
│                                                                    │
│  ┌──────────────┐ ┌──────────────┐ ┌───────────────┐              │
│  │AuthController │ │UserController│ │ChatController  │              │
│  │ - authenticate│ │ - register   │ │ - dietPlan     │              │
│  │ - socialLogin │ │ - profile    │ │ - surpriseMeal │              │
│  │ - verifyOtp   │ │ - weight     │ │ - batchMeal    │              │
│  │ - refresh     │ │ - images     │ │ - userMeal     │              │
│  └──────────────┘ └──────────────┘ └───────┬───────┘              │
│                                             │                      │
│  ┌──────────────┐ ┌──────────────┐ ┌───────▼───────┐              │
│  │MealController │ │DashboardCtrl │ │AI Engine      │              │
│  │ - fetchMeals  │ │ - macros     │ │ - GPT-4o API  │              │
│  │ - intakeFood  │ │ - weekly     │ │ - Prompt Eng. │              │
│  │ - waterIntake │ │ - charts     │ │ - Response DB  │              │
│  └──────────────┘ └──────────────┘ └───────────────┘              │
│                                                                    │
│  ┌──────────────┐ ┌──────────────┐ ┌───────────────┐              │
│  │FoodController │ │PackageCtrl   │ │SubscriptionCtrl│             │
│  │ - likeDislike │ │ - getPackages│ │ - Stripe CRUD  │             │
│  │ - similar     │ │              │ │ - Webhooks     │             │
│  └──────────────┘ └──────────────┘ └───────────────┘              │
│                                                                    │
│  ┌──────────────┐ ┌──────────────┐ ┌───────────────┐              │
│  │FavouriteCtrl  │ │BatchMealCtrl │ │PaymentCtrl    │              │
│  │ - add/remove  │ │ - generate   │ │ - createIntent│              │
│  │ - check       │ │ - consume    │ │ - verify      │              │
│  └──────────────┘ └──────────────┘ └───────────────┘              │
└────────────────────────────┬───────────────────────────────────────┘
                             ▼
┌────────────────────────────────────────────────────────────────────┐
│                      SERVICE LAYER                                 │
│  ┌──────────────────┐  ┌──────────────────────────────────────┐   │
│  │  StripeService    │  │  Laravel Queue (Jobs)                │   │
│  │  - init()         │  │  - UserDietPlan (async generation)   │   │
│  │  - getOrCreate    │  │  - UserDayDietPlan (day-by-day GPT)  │   │
│  │    Customer()     │  │                                      │   │
│  └──────────────────┘  └──────────────────────────────────────┘   │
└────────────────────────────┬───────────────────────────────────────┘
                             ▼
┌────────────────────────────────────────────────────────────────────┐
│                     DATA LAYER (Eloquent ORM)                      │
│                                                                    │
│  78+ Eloquent Models · 120+ Database Migrations · MySQL/MariaDB    │
│                                                                    │
│  Core: User, Recipe, AiResponse, FoodItem, Meal, MealPlan         │
│  Health: Allergen, MedicalIssue, Goal, FitnessLevel, DietType     │
│  Preferences: Cuisine, CookingTime, CookingPrep, Appliance        │
│  Billing: Payment, Subscription, SubscriptionPlan, StripePayment   │
│  Content: Homepage, Service, Testimonial, Newsletter, Page         │
└────────────────────────────────────────────────────────────────────┘
                             ▼
┌────────────────────────────────────────────────────────────────────┐
│                   EXTERNAL SERVICES                                │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐             │
│  │ OpenAI   │ │ Stripe   │ │ Firebase │ │  MSG91   │             │
│  │ GPT-4o   │ │ Billing  │ │ FCM Push │ │ SMS/OTP  │             |
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘             │
└────────────────────────────────────────────────────────────────────┘
```

---

## 🛠 Technology Stack

### Core Framework & Language

| Technology | Version | Purpose |
|---|---|---|
| PHP | ^8.2 | Server-side language |
| Laravel | ^11.9 | MVC framework |
| Laravel Sanctum | ^4.2 | API token authentication |
| Laravel Socialite | ^5.15 | OAuth (Google sign-in) |
| Laravel UI | ^4.5 | Blade scaffolding for admin panel |
| Doctrine DBAL | ^4.4 | Database abstraction for migrations |

### AI & Machine Learning

| Technology | Version | Purpose |
|---|---|---|
| OpenAI GPT-4o | API v1 | Diet plan & recipe generation |
| Laravel HTTP Client | Built-in | OpenAI API communication |

### Payment Processing

| Technology | Version | Purpose |
|---|---|---|
| Stripe PHP SDK | ^19.2 | Subscription & one-time payments |
| Razorpay PHP SDK | ^2.9 | Alternative payment gateway (India) |

### Notifications & Messaging

| Technology | Version | Purpose |
|---|---|---|
| FCM Channel | ^4.3 | Firebase Cloud Messaging push notifications |
| MSG91 Laravel | ^0.15.0 | SMS OTP delivery |

### Admin & DevTools

| Technology | Version | Purpose |
|---|---|---|
| Yajra DataTables | ^11.1 | Server-side DataTable rendering in admin |
| Laravel Debugbar | ^3.14 | Dev-only debugging toolbar |
| Laravel Pint | ^1.24 | Code style fixer (PSR-12) |
| Laravel Installer | ^4.1 | Web-based installer |
| Iseed | ^3.0 | Inverse seeder (DB → seed files) |

### Frontend Build (Admin Panel)

| Technology | Version | Purpose |
|---|---|---|
| Vite | ^5.0 | Asset bundling |
| Bootstrap | ^5.2.3 | Admin panel CSS framework |
| Sass | ^1.56.1 | CSS preprocessing |
| Axios | ^1.6.4 | AJAX requests in admin |
| Firebase JS | ^11.0.1 | Client-side Firebase integration |

### Testing

| Technology | Version | Purpose |
|---|---|---|
| PHPUnit | ^11.0.1 | Unit & feature testing |
| Mockery | ^1.6 | Object mocking |
| Faker | ^1.23 | Test data generation |
| Collision | ^8.0 | Better error reporting in tests |

---

## 📁 Project Structure

```
planitprep_backend/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                          # ◀ REST API Controllers (17 files)
│   │   │   │   ├── AppController.php         #   App config, countries, states, cities
│   │   │   │   ├── AuthController.php        #   OTP auth, social login, token refresh
│   │   │   │   ├── BatchMealController.php   #   Batch meal prep generation + inventory
│   │   │   │   ├── ChatController.php        #   ◀ CORE AI ENGINE (1,589 lines)
│   │   │   │   ├── DashboardController.php   #   Macro aggregation, weekly/daily stats
│   │   │   │   ├── FavouriteMealController.php # Favourite meal CRUD
│   │   │   │   ├── FoodController.php        #   Like/dislike, similar food items
│   │   │   │   ├── MealController.php        #   Meal plans, food intake, water tracking
│   │   │   │   ├── PackageController.php     #   Membership packages listing
│   │   │   │   ├── PaymentController.php     #   Stripe one-time payment intents
│   │   │   │   ├── PaymentSubscriptionDataController.php # Payment history queries
│   │   │   │   ├── StripeWebhookController.php # Stripe webhook event handler
│   │   │   │   ├── SubscriptionController.php  # Stripe subscription lifecycle
│   │   │   │   ├── SubscriptionPlanController.php # Admin plan CRUD
│   │   │   │   ├── SurpriseMealController.php  # (Deprecated — logic in ChatController)
│   │   │   │   ├── TestDBController.php      #   Database connectivity test
│   │   │   │   └── UserController.php        #   User profile, weight, images, notifications
│   │   │   │
│   │   │   ├── Admin/                        # ◀ Admin Panel Controllers (27 files)
│   │   │   │   ├── Auth/LoginController.php  #   Admin authentication
│   │   │   │   ├── ActivityLevelController.php
│   │   │   │   ├── AllergenController.php
│   │   │   │   ├── ... (24 more admin controllers)
│   │   │   │   └── UserController.php        #   User management, diet plan assignment
│   │   │   │
│   │   │   ├── CheckoutController.php        # Payment checkout flow (web)
│   │   │   ├── Controller.php                # Base controller
│   │   │   ├── FrontController.php           # Web front-end (registration, tracking)
│   │   │   ├── FrontDashboardController.php  # Web dashboard (intake logging)
│   │   │   ├── HomeController.php            # Public pages, contact form
│   │   │   └── ScriptController.php          # Utility scripts (test notification)
│   │   │
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php              # Standard Laravel auth middleware
│   │   │   └── CheckValidRequestBasedOnUserAndKey.php # ◀ Custom API key guard
│   │   │
│   │   └── Resources/                        # API Resource transformers (7 files)
│   │       ├── CouponResource.php
│   │       ├── IntakeMealItemsCollection.php
│   │       ├── IntakeMealsCollection.php
│   │       ├── MealItemsCollection.php
│   │       ├── MealsCollection.php
│   │       ├── MembershipCollection.php
│   │       └── PaymentCollection.php
│   │
│   ├── Jobs/                                 # ◀ Background Queue Jobs
│   │   ├── UserDietPlan.php                  #   Weekly diet plan generation (async)
│   │   └── UserDayDietPlan.php               #   Single-day diet plan generation (async)
│   │
│   ├── Models/                               # ◀ 78 Eloquent Models
│   │   ├── User.php                          #   Central user model (15+ relationships)
│   │   ├── Recipe.php                        #   AI-generated recipe storage
│   │   ├── AiResponse.php                    #   AI response history (7-day dedup window)
│   │   ├── FoodItem.php                      #   Nutritional database (25+ macro fields)
│   │   ├── Meal.php                          #   Meal types (Breakfast, Lunch, etc.)
│   │   ├── BatchMealConfig.php               #   Batch meal prep configuration
│   │   ├── BatchMealInventory.php            #   Portion tracking for batch meals
│   │   ├── Subscription.php                  #   Stripe subscription records
│   │   └── ... (70 more models)
│   │
│   ├── Notifications/
│   │   └── UserNotification.php              # FCM push notification class
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   │
│   └── Services/
│       └── StripeService.php                 # Stripe init & customer management
│
├── config/                                   # 14 configuration files
├── database/
│   ├── migrations/                           # ◀ 120+ migration files (2024-08 to 2026-03)
│   ├── seeders/                              # Database seeders
│   └── factories/                            # Model factories
│
├── resources/
│   ├── views/                                # Blade templates (admin + web)
│   ├── js/ · sass/ · css/ · lang/
│
├── routes/
│   ├── api.php                               # ◀ All REST API routes (199 lines)
│   ├── web.php                               # Web + Admin routes (420 lines)
│   ├── channels.php                          # Broadcasting channels
│   └── console.php                           # Artisan commands
│
├── tests/                                    # PHPUnit tests
├── composer.json                             # PHP dependencies
├── package.json                              # Node.js dependencies
├── vite.config.js                            # Vite build configuration
└── phpunit.xml                               # PHPUnit configuration
```

---

## 📡 API Reference

All API endpoints are prefixed with `/api/v1/` and require custom headers `USER` and `KEY` for gateway authentication.

### Authentication Headers (All Requests)

| Header | Value | Required |
|---|---|---|
| `USER` | `DietitianApi` (configured in `.env`) | ✅ Always |
| `KEY` | API secret key (configured in `.env`) | ✅ Always |
| `Authorization` | `Bearer {sanctum_token}` | 🔒 Protected routes only |
| `Content-Type` | `application/json; charset=UTF-8` | ✅ Always |

---

### 🔐 Authentication Endpoints

| Method | Endpoint | Auth | Controller | Description |
|---|---|---|---|---|
| `POST` | `/authenticate` | ❌ | `AuthController@Authenticate` | Send OTP to phone/email |
| `POST` | `/verify-otp` | ❌ | `AuthController@verify_otp` | Verify OTP → Sanctum token |
| `POST` | `/social-login` | ❌ | `AuthController@socialLogin` | Google/Apple social login |
| `POST` | `/refresh` | 🔒 | `AuthController@refresh` | Refresh session & token |
| `POST` | `/resend-otp` | ❌ | `AuthController@resend_otp` | Resend OTP |

### 👤 User Management Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/update-user-information` | 🔒 | Complete onboarding (24+ fields) |
| `POST` | `/update-profile` | 🔒 | Edit basic profile info |
| `POST` | `/update-fcm-token` | 🔒 | Register FCM push token |
| `POST` | `/update-current-weight` | 🔒 | Log current weight |
| `POST` | `/update-target-weight` | 🔒 | Update target weight goal |
| `GET` | `/fetch-weight-history` | 🔒 | Get weight tracking timeline |
| `POST` | `/upload-image` | 🔒 | Upload progress photo |
| `GET` | `/user-progress-images` | 🔒 | Get all progress photos |
| `POST` | `/upload-profile-image` | 🔒 | Upload/change profile picture |
| `GET` | `/my-payments` | 🔒 | Get payment transaction history |
| `GET` | `/my-coupons` | 🔒 | Get available coupons |
| `POST` | `/apply-coupon` | 🔒 | Apply coupon to purchase |
| `GET` | `/notifications` | 🔒 | Get paginated notifications |
| `POST` | `/delete-profile` | 🔒 | Permanently delete user account |
| `POST` | `/generate-user-diet-plan` | 🔒 | Trigger async diet plan generation |
| `POST` | `/replace-food-item` | 🔒 | Replace food item in diet plan |
| `GET` | `/get-daily-average` | 🔒 | Get average daily diet goals |

### 🍽️ Meal & Food Tracking Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/save-user-diet-plan` | 🔒 | Save AI-generated diet plan to DB |
| `GET` | `/fetch-meals` | 🔒 | Get assigned meal plan by day |
| `POST` | `/save-intake-food-item` | 🔒 | Log food consumption |
| `POST` | `/update-water-intake` | 🔒 | Update daily water intake |
| `GET` | `/fetch-my-meals` | 🔒 | Get consumed meals by date |
| `GET` | `/fetch-macro-tracker-data` | 🔒 | Get macro data for date range (graphs) |
| `GET` | `/get-food-items` | ❌ | Search food items database |
| `POST` | `/like-dislike-food-item` | 🔒 | Like or dislike a food item |
| `GET` | `/similar-food-items` | 🔒 | Get similar food items for replacement |

### 🤖 AI Diet Plan Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/get-full-diet-plan` | ❌* | Generate full daily diet plan via GPT-4o |
| `POST` | `/get-surprise-meal` | ❌* | Generate surprise meal by appliances |
| `POST` | `/get-user-meal` | ❌* | Unified meal generation (day-wise/batch) |
| `POST` | `/generate-batch-meal` | ❌* | Generate batch meal prep with portions |
| `POST` | `/consume-batch-meal` | ❌* | Mark batch meal portion as consumed |

> *These routes use `USER`/`KEY` middleware but are outside `auth:sanctum` — they authenticate via `user_id` in the request body.

### 💳 Payment & Subscription Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/create-payment-intent` | 🔒 | Create Stripe one-time payment |
| `POST` | `/create-subscription` | 🔒 | Create Stripe subscription |
| `POST` | `/cancel-subscription` | 🔒 | Cancel active subscription |
| `POST` | `/verify-payment` | 🔒 | Verify payment status |
| `GET` | `/payments-subscriptions-data` | 🔒 | All payment/subscription data |
| `POST` | `/checkSubscription` | ❌* | Check user subscription status |
| `POST` | `/stripe/webhook` | ❌ | Stripe webhook (no middleware) |

### ⭐ Favourite Meal Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/add-favourite-meal` | ❌* | Mark recipe as favourite |
| `POST` | `/delete-favourite-meal` | ❌* | Remove from favourites |
| `POST` | `/get-favourite-meal` | ❌* | Get all favourite meals |
| `POST` | `/check-isFavourite` | ❌* | Check if meal is favourited |
| `POST` | `/check-SM-isFavourite` | ❌* | Check surprise meal favourite |
| `POST` | `/update-isFavourite` | ❌* | Toggle favourite status |
| `POST` | `/remove-isFavourite` | ❌* | Remove favourite flag |

### 📊 Dashboard & Config Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/get-dashboard-data` | 🔒 | Full dashboard (macros, goals, weekly stats) |
| `GET` | `/app-config` | ❌ | App configuration & feature flags |
| `GET` | `/get-countries` | ❌ | Country list |
| `GET` | `/get-states` | ❌ | State list (by country) |
| `GET` | `/get-cities` | ❌ | City list (by state) |
| `GET` | `/get-packages` | ❌ | Membership packages |

### 📋 Subscription Plan Management

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/plans/create` | ❌* | Create subscription plan |
| `POST` | `/plans/update` | ❌* | Update subscription plan |
| `POST` | `/plans/delete` | ❌* | Delete subscription plan |
| `GET` | `/plans/getAllPlans` | ❌* | List all subscription plans |

---

## 🧠 AI Diet Plan Engine

The AI engine is the core differentiator of PlanIt Prep. It lives in `ChatController.php` (1,589 lines) and implements three generation modes.

### Architecture

```
┌─────────────────────────────────────────────────────────┐
│                   ChatController.php                     │
│                                                         │
│  ┌───────────────────┐   ┌───────────────────────────┐ │
│  │ generateUserMeal() │──▶│ ROUTING DECISION           │ │
│  │ (unified entry)    │   │                           │ │
│  └───────────────────┘   │ DAY_WISE + diet_plan       │ │
│                          │   → getFullDietPlan...()   │ │
│                          │ DAY_WISE + surprise_meal    │ │
│                          │   → getSurpriseMeal()      │ │
│                          │ BATCH_MEAL                  │ │
│                          │   → (future)               │ │
│                          └─────────────┬─────────────┘ │
│                                        ▼               │
│  ┌─────────────────────────────────────────────────┐   │
│  │         getOrCreateAiResponse()                  │   │
│  │         (Cache-or-Generate Pattern)              │   │
│  │                                                  │   │
│  │  1. Check Recipe DB for today's active plan      │   │
│  │  2. If exists & !refresh → return cached         │   │
│  │  3. If refresh → version++ & deactivate old      │   │
│  │  4. Call GPT callback                            │   │
│  │  5. Store new recipes in DB                      │   │
│  │  6. Return combined meals                        │   │
│  └───────────────────────┬─────────────────────────┘   │
│                          ▼                              │
│  ┌─────────────────────────────────────────────────┐   │
│  │   generateDietPlan() / generateSurpriseMeal()    │   │
│  │         (OpenAI GPT-4o API Call)                  │   │
│  │                                                  │   │
│  │  • Build system prompt with JSON schema           │   │
│  │  • Inject user profile as user message            │   │
│  │  • Include 7-day recipe deduplication list         │   │
│  │  • POST to OpenAI /v1/chat/completions            │   │
│  │  • Parse & validate JSON response                 │   │
│  │  • Assign unique keys per meal                    │   │
│  └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

### User Parameters Sent to AI

The AI prompt includes the following user data collected during onboarding:

| Parameter | Source | Example |
|---|---|---|
| `name` | `users.full_name` | "John Doe" |
| `weight` | `users.weight` | 75 |
| `height` | `users.height` | 175 |
| `gender` | `users.gender` | "Male" |
| `age` | `users.age` | 28 |
| `goal` | `goals.name` | "Lose Weight" |
| `fitness_level` | `fitness_levels.name` | "Intermediate" |
| `exercise_in_a_week` | `exercise_plan_days.name` | "3-4 days" |
| `allergies` | `allergens.name` (joined) | "Peanuts, Shellfish" |
| `diet_types` | `diet_types.name` (joined) | "Vegetarian" |
| `preferred_cuisine` | `cuisines.name` | "Indian" |
| `preferred_cooking_time` | `cooking_times.name` | "Under 30 min" |
| `cooking_prep_preference` | `cooking_preps.name` | "Minimal Prep" |
| `available_appliances` | `appliances.name` (M2M joined) | "Microwave, Oven, Stove" |
| `meal_prep_schedule` | `meal_prep_schedules.name` | "Daily" |
| `preferred_meal_count` | `meal_counts.name` | "3 Meals" |
| `total_kcal_limit` | `users.daily_calorie_intake` | 2000 |
| `other_food_preferences` | `users.other_food_preferences` | "Low spice" |
| `other_allergies` | `users.other_allergies` | "Soy" |

### Recipe Deduplication (7-Day Window)

The system prevents recipe repetition using a sliding window:

1. `getLast7DaysAiResponses($userId, $type)` — fetches previous AI responses from `ai_responses` table
2. `extractUsedRecipes($previousResponses)` — extracts unique recipe names
3. Injected into system prompt: `"Previously used recipes (DO NOT REPEAT): [...]"`

### Dynamic Meal Count Handling

The prompt dynamically adjusts meal structure based on user's `preferred_meal_count`:

| Meal Count | Generated Meals |
|---|---|
| 2 | Lunch, Dinner |
| 3 (default) | Breakfast, Lunch, Dinner |
| 4 | Breakfast, Lunch, Evening Snack, Dinner |

### AI Response Schema

```json
{
  "diet_plan": [
    {
      "day": "Day 1",
      "meals": {
        "Breakfast": {
          "uniqueKey": "1_1_2026-03-02T00:00:00+00:00",
          "recipeName": "Creamy Scrambled Eggs & Whole Wheat Toast",
          "prepTime": "10 min",
          "calories": "350 kcal",
          "isVegetarian": true,
          "recipePoints": "1. Crack 3 eggs... (detailed step-by-step)",
          "grocery_list": [
            { "name": "Eggs", "amount": "3", "isVegetarian": true },
            { "name": "Whole-wheat toast", "amount": "2 slices", "isVegetarian": true }
          ]
        },
        "Lunch": { "..." },
        "Dinner": { "..." }
      },
      "daily_summary": {
        "total_calories": "Approx 2000 kcal",
        "protein": "Approx 150g"
      }
    }
  ]
}
```

### Recipe Persistence (Versioned)

Generated recipes are stored in the `recipes` table with versioning support:

| Column | Type | Purpose |
|---|---|---|
| `userid` | int | Owner user |
| `date` | date | Date the plan is for |
| `meal_type` | enum | BREAKFAST, LUNCH, DINNER, SNACKS, SURPRISE_MEAL |
| `preparation_type` | string | DAY_WISE_MEAL, BATCH_MEAL |
| `type` | string | diet_plan, surprise_meal |
| `version` | int | Incremented on refresh |
| `is_active` | bool | Only one active version per date/type |
| `recipeName` | string | AI-generated recipe name |
| `prepTime` | string | Preparation time |
| `calories` | string | Calorie string |
| `isVegetarian` | bool | Dietary flag |
| `recipePoints` | text | Step-by-step instructions |
| `grocery_list` | json | Array of ingredients |
| `isFavorate` | bool | User favourite flag |
| `isDeleted` | bool | Soft delete flag |

### Batch Meal System

The `BatchMealController` implements a batch cooking workflow:

1. **Configuration**: User sets cooking days and meal count via `BatchMealConfig`
2. **Calculation**: System calculates portions until next cooking day
3. **Generation**: GPT generates recipes scaled for batch portions with calories-per-portion
4. **Inventory**: `BatchMealInventory` tracks total/used/remaining portions
5. **Consumption**: `consumePortion()` decrements remaining portions with DB transaction

### OpenAI API Configuration

| Config Key | Default | Description |
|---|---|---|
| `app.anthropic_api_key` | `.env` | Anthropic API key |
| `app.anthropic_model` | `claude-sonnet-4-20250514` | Claude model identifier |
| Max tokens (diet plan) | 4096 | Token limit for diet plans |
| Max tokens (surprise) | 1500 | Token limit for surprise meals |
| Temperature (surprise) | 1.0 | High randomness for variety |
| Timeout | 120s | HTTP timeout for AI calls |
| Response format | `json_object` | Enforced JSON output |

---

## 🔐 Authentication & Security

### Dual-Layer Authentication

```
Request Flow:
  ┌──────────────────────────────────────────────────┐
  │ 1. CheckValidRequestBasedOnUserAndKey middleware  │
  │    - Validates HTTP_USER header against config    │
  │    - Validates HTTP_KEY header against config     │
  │    - Rejects if either missing or invalid         │
  └────────────────────┬─────────────────────────────┘
                       ▼
  ┌──────────────────────────────────────────────────┐
  │ 2. auth:sanctum middleware (protected routes)     │
  │    - Validates Bearer token from Authorization    │
  │    - Resolves authenticated User model            │
  │    - Rejects if token expired or invalid          │
  └──────────────────────────────────────────────────┘
```

### Authentication Flows

**OTP Flow (Phone/Email):**
```
Client → POST /authenticate { user_input: "+91..." | "user@email.com" }
Server → Creates OTP record (6-digit, 1-min TTL) → Sends via MSG91/email
Client → POST /verify-otp { user_input, otp }
Server → Validates OTP → Returns Sanctum token + user data
```

**Social Login Flow (Google/Apple):**
```
Client → POST /social-login { email, login_type: "G"|"A", ios_uuid? }
Server → Finds or creates user by email/ios_uuid
Server → Returns Sanctum token + user data + membership status
```

**Token Refresh:**
```
Client → POST /refresh (with Bearer token)
Server → Validates token → Returns refreshed user data + membership status
```

### Admin Authentication

- Login: `POST /secureAdmin/login`
- Guard: `adminAuth:admin` (session-based)
- Separate `admins` table and `Admin` model

---

## 🗄 Database Schema

### Entity Relationship Overview

```
                    ┌─────────────┐
                    │    User     │
                    │ (central)   │
                    └──────┬──────┘
                           │
        ┌──────────────────┼──────────────────────┐
        │                  │                      │
   ┌────▼────┐     ┌──────▼──────┐      ┌────────▼────────┐
   │ Health  │     │   Meals     │      │    Billing      │
   │ Profile │     │   & Food    │      │    & Subs       │
   └─────────┘     └─────────────┘      └─────────────────┘
   - UserGoal       - MealPlan           - Payment
   - UserAllergies  - MealPlanItem       - Subscription
   - UserMedical    - IntakeFoodItem     - StripePayment
   - UserPreference - FoodItem           - UserMembership
   - UserDietRegime - Recipe             - SubscriptionPlan
   - WeightTracking - AiResponse         - Coupon/CouponUser
   - UserImage      - FavouriteMeal
                    - BatchMealConfig
                    - BatchMealInventory
```

### Key Table Groups (120+ Migrations)

#### Core User Tables

| Table | Purpose | Key Columns |
|---|---|---|
| `users` | Central user table | full_name, email, phone, gender, age, height, weight, goal_id, fitness_level_id, cuisine_id, cooking_time_id, cooking_prep_id, meal_count_id, daily_calorie_intake, fcm_token, stripe_customer_id, ios_uuid, login_type |
| `user_goals` | Weight goals with timeline | user_id, goal_id, current_value, target_value, start_date, end_date |
| `user_allergies` | Pivot: user ↔ allergens | user_id, allergy_id |
| `user_medical_issues` | Pivot: user ↔ medical conditions | user_id, medical_issue_id |
| `user_preferences` | Pivot: user ↔ diet types | user_id, diet_type_id |
| `user_appliances` | Pivot: user ↔ appliances (M2M) | user_id, appliance_id |
| `weight_trackings` | Weight history log | user_id, current_value, target_value |
| `user_images` | Progress photos | user_id, image_path |
| `user_test_reports` | Medical test uploads | user_id, name, date, file |

#### AI & Recipe Tables

| Table | Purpose | Key Columns |
|---|---|---|
| `recipes` | AI-generated recipe storage (versioned) | userid, date, meal_type, preparation_type, type, version, is_active, recipeName, grocery_list (JSON), isFavorate |
| `ai_responses` | AI response history (7-day dedup) | user_id, type, response_date, response_json |
| `chatgpt_ai_settings` | Admin-managed AI model config | model, key, settings |
| `batch_meal_configs` | Batch cooking configuration | user_id, days_of_cook, is_active |
| `batch_meal_inventories` | Portion tracking | recipe_id, total_portions, used_portions, remaining_portions, calories_per_portion |

#### Food & Nutrition Tables

| Table | Purpose |
|---|---|
| `food_items` | Nutritional DB (25+ macro/micro fields, glycemic index, food type) |
| `meals` | Meal categories (Breakfast, Lunch, Dinner, Snacks) |
| `meal_plans` | Dietitian-created meal plans |
| `meal_plan_items` | Items in plans (meal_id, food_item_id, quantity, day) |
| `intake_food_items` | Daily food intake log |
| `food_item_like_dislikes` | User food preferences |
| `similar_food_items` | Pre-computed similar foods |
| `food_groups` | Food categorization |
| `serving_units` | Standard serving units |

#### Reference Data Tables

| Table | Purpose |
|---|---|
| `goals` | Lose Weight, Gain Muscle, Maintain |
| `fitness_levels` | Beginner, Intermediate, Advanced |
| `exercise_plan_days` | Exercise frequency options |
| `allergens` | Allergen types |
| `medical_issues` | Medical conditions |
| `diet_types` | Vegetarian, Non-Veg, Vegan |
| `diet_regimes` | Keto, Paleo, Mediterranean |
| `cuisines` | Indian, Italian, etc. |
| `cooking_times` / `cooking_preps` / `appliances` | Kitchen preferences |
| `meal_prep_schedules` / `meal_counts` | Prep scheduling |
| `countries` / `states` / `cities` | Geographic data |

#### Billing & Membership Tables

| Table | Purpose |
|---|---|
| `packages` / `features` | Membership tiers & features |
| `memberships` / `user_memberships` | Membership assignments |
| `payments` / `payment_methods` | Razorpay payments |
| `stripe_payments` / `subscriptions` / `subscription_plans` | Stripe billing |
| `coupons` / `coupon_users` | Discount coupons |

#### CMS & Content Tables

| Table | Purpose |
|---|---|
| `settings` / `login_settings` | Global & login config |
| `homepages` / `services` / `testimonials` | Homepage content |
| `newsletters` / `newsletter_subscriptions` | Email marketing |
| `pages` | Dynamic CMS pages |
| `enquiries` | Contact form submissions |

---

## 📦 Eloquent Models

### User Model — Central Entity (15+ Relationships)

```
User
├── BelongsTo: Goal, CookingPrep, Cuisine, CookingTime, MealPrepSchedule,
│              MealCount, Country, State, City
├── BelongsToMany: ActivityLevel, MedicalIssue, DietType, Appliance
├── HasOne: UserMembership, BatchMealConfig
├── HasMany: Payment, Subscription, BatchMealInventory
└── Traits: HasApiTokens, HasFactory, Notifiable
```

### Recipe Model — AI-Generated Content

```
Recipe
├── fillable: userid, date, meal_type, preparation_type, type, version,
│             is_active, recipeName, prepTime, calories, isVegetarian,
│             recipePoints, grocery_list, isFavorate, isDeleted
├── casts: date→date, isVegetarian→boolean, grocery_list→array,
│          isFavorate→boolean, isDeleted→boolean, is_active→boolean
```

---

## ⚙️ Background Jobs & Queues

### Job: UserDietPlan

| Property | Value |
|---|---|
| **File** | `app/Jobs/UserDietPlan.php` (182 lines) |
| **Purpose** | Async weekly diet plan generation triggered after registration |
| **Flow** | Load user profile → build prompt → dispatch 7 × `UserDayDietPlan` jobs |
| **Memory** | `ini_set('memory_limit', '18048M')` |
| **Timeout** | `ini_set('max_execution_time', 0)` |

### Job: UserDayDietPlan

| Property | Value |
|---|---|
| **File** | `app/Jobs/UserDayDietPlan.php` (375 lines) |
| **Purpose** | Single-day plan generation via ChatGPT + DB persistence |
| **Flow** | Call OpenAI → parse JSON → upsert FoodItem records → create MealPlanItem records |

---

## 💳 Payment & Subscription System

### Dual Payment Gateway

| Gateway | Market | Capabilities |
|---|---|---|
| **Stripe** | Global (primary) | Subscriptions, one-time, webhooks, customer management |
| **Razorpay** | India | One-time payments, checkout flow |

### Stripe Service Layer (`StripeService.php`)

| Method | Description |
|---|---|
| `init()` | Sets Stripe API key from config |
| `getOrCreateCustomer($user)` | Creates/retrieves Stripe customer by `stripe_customer_id` |

### Stripe Webhook Events Handled

| Event | Action |
|---|---|
| `customer.subscription.created` | Create `Subscription` record, `is_subscribed = 0` |
| `customer.subscription.updated` | Update status, `is_subscribed = 1` if active |
| `customer.subscription.deleted` | Set `is_subscribed = 0`, status = canceled |
| `invoice.payment_succeeded` | Create `Payment` record |
| `invoice.payment_failed` | Log failure |

### Webhook Security

Stripe webhook signature verification using `STRIPE_WEBHOOK_SECRET` from `.env`.

---

## 🖥 Admin Panel (CMS)

### Access

- **URL**: `https://planitprep.cloud/secureAdmin/`
- **Guard**: `adminAuth:admin` (session-based)
- **Framework**: Blade templates + Bootstrap 5 + Yajra DataTables

### Admin Modules (27 Controllers)

| Module | Features |
|---|---|
| **Dashboard** | System overview, stats |
| **Users** | CRUD, diet plan assignment, payment link generation, meal drag-and-drop |
| **Food Items** | CRUD with 25+ nutritional fields, similar item management |
| **Meal Plans** | Plan creation, food item assignment per day/meal |
| **Meals** / **Serving Units** | Reference data management |
| **Goals** / **Allergens** / **Diet Types** / **Medical Issues** | Health options |
| **Activity Levels** / **Exercise Plan Days** / **Fitness Levels** | Fitness options |
| **Food Groups** | Food categorization |
| **Packages** / **Features** / **Memberships** | Billing management |
| **Supplements** / **Supplement Types** | Supplement database |
| **Coupons** / **Coupon Users** | Coupon CRUD with random generation |
| **Settings** | Global app settings |
| **ChatGPT Settings** | AI model & key configuration |
| **Login Settings** | Login page configuration |
| **Manage Homepage** | Homepage CMS |
| **Services** / **Testimonials** / **Newsletter** / **Pages** | Content management |

---

## 🔔 Notification System

| Property | Value |
|---|---|
| **Package** | `laravel-notification-channels/fcm` ^4.3 |
| **Class** | `app/Notifications/UserNotification.php` |
| **Token** | `users.fcm_token` column |
| **Update** | `POST /update-fcm-token` |
| **Deep Links** | `page_name` field (e.g., `daily_meal`) |

---

## 🔄 API Resources & Collections

| Resource | Purpose |
|---|---|
| `MealsCollection` | Transforms meal plan data |
| `MealItemsCollection` | Individual meal item serialization |
| `IntakeMealsCollection` | Consumed meals with macros |
| `IntakeMealItemsCollection` | Individual intake serialization |
| `PaymentCollection` | Payment history |
| `MembershipCollection` | Membership data |
| `CouponResource` | Coupon details |

---

## 🔌 Third-Party Integrations

| Service | Package | Purpose | Config Key |
|---|---|---|---|
| **Anthropic Claude** | HTTP Client | AI diet plan generation | `app.anthropic_api_key` |
| **Stripe** | `stripe/stripe-php` ^19.2 | Subscriptions & payments | `services.stripe.*` |
| **Razorpay** | `razorpay/razorpay` ^2.9 | Indian payments | Razorpay env vars |
| **Firebase FCM** | `laravel-notification-channels/fcm` | Push notifications | Firebase credentials |
| **MSG91** | `craftsys/msg91-laravel` | SMS OTP delivery | MSG91 auth key |
| **Google OAuth** | `laravel/socialite` | Social login | `services.google.*` |
| **DataTables** | `yajra/laravel-datatables-oracle` | Admin tables | Auto-discovered |

---

## ⚙️ Environment Configuration

### Required `.env` Variables

```env
# ─── Application ───────────────────────────────
APP_NAME=PlanitPrep
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://planitprep.cloud

# ─── Database ──────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=planitprep
DB_USERNAME=root
DB_PASSWORD=secret

# ─── API Authentication ───────────────────────
API_USER=DietitianApi
API_KEY=your-api-secret-key

# ─── OpenAI / ChatGPT ─────────────────────────
ANTHROPIC_API_KEY=your-anthropic-key
ANTHROPIC_MODEL=claude-sonnet-4-20250514

# ─── Stripe ────────────────────────────────────
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# ─── Razorpay ──────────────────────────────────
RAZORPAY_KEY=rzp_live_...
RAZORPAY_SECRET=...

# ─── Google OAuth ──────────────────────────────
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=...

# ─── MSG91 (SMS/OTP) ──────────────────────────
MSG91_KEY=...

# ─── Firebase (FCM) ───────────────────────────
FCM_CREDENTIALS_PATH=storage/firebase-credentials.json

# ─── Queue ─────────────────────────────────────
QUEUE_CONNECTION=database

# ─── Mail ──────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

---

## 🚀 Installation & Setup

### Prerequisites

| Tool | Version | Required |
|---|---|---|
| PHP | 8.2+ | ✅ |
| Composer | 2.x | ✅ |
| MySQL / MariaDB | 8.0+ / 10.6+ | ✅ |
| Node.js | 18+ | ✅ (for admin assets) |
| npm | 9+ | ✅ |

### Step-by-Step Setup

```bash
# 1. Clone the repository
git clone https://github.com/your-org/planitprep_backend.git
cd planitprep_backend

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure .env (see Environment Configuration section)

# 7. Run database migrations
php artisan migrate

# 8. Seed reference data
php artisan db:seed

# 9. Create storage symlink
php artisan storage:link

# 10. Build frontend assets
npm run build
```

---

## ▶️ Running the Application

### Development

```bash
# Start Laravel development server
php artisan serve

# Start Vite dev server (admin panel hot reload)
npm run dev

# Start queue worker (for background jobs)
php artisan queue:work
```

### Production

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Build production assets
npm run build

# Start queue worker (supervisor recommended)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Useful Artisan Commands

```bash
php artisan optimize:clear           # Clear all caches
php artisan migrate:status           # Check migration status
php artisan queue:failed             # Check failed jobs
php artisan queue:retry all          # Retry failed jobs
php artisan route:list --columns=method,uri,name  # List routes
```

---

## 🗄 Database Migrations

120+ migrations spanning August 2024 to March 2026:

| Period | Key Changes |
|---|---|
| **2024-08** | Core tables: users, food_items, meals, meal_plans, goals, allergens |
| **2024-09** | Auth: tokens, OTPs, countries/states/cities, exercise plans |
| **2024-10** | Billing: packages, memberships, payments, diet regimes |
| **2024-11** | Social: food like/dislike, similar items, user images, coupons |
| **2025-01–03** | Notifications, settings, homepage, memberships expansion |
| **2025-06–07** | AI: ChatGPT credits, serving units, AI settings |
| **2025-11** | Onboarding: cuisines, cooking times, appliances (M2M), meal prep |
| **2026-01** | Stripe: customer_id, payments, subscriptions |
| **2026-02–03** | AI v2: recipes, ai_responses, favourites, batch meals |

---

## 🧪 Testing

```bash
php artisan test                           # Run all tests
php artisan test --testsuite=Feature       # Feature tests
php artisan test --testsuite=Unit          # Unit tests
php artisan test --coverage                # With coverage
./vendor/bin/pint --test                   # Code style check
./vendor/bin/pint                          # Fix code style
```

---

## 🚢 Deployment

### Production Checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] All `.env` variables configured
- [ ] `composer install --optimize-autoloader --no-dev`
- [ ] `php artisan config:cache && route:cache && view:cache`
- [ ] `npm run build`
- [ ] `php artisan migrate --force`
- [ ] Supervisor for queue workers
- [ ] Stripe webhook: `https://planitprep.cloud/api/stripe/webhook`
- [ ] Storage directory writable
- [ ] SSL certificate configured
- [ ] CORS headers restricted (currently `Access-Control-Allow-Origin: *`)

### Supervisor Configuration

```ini
[program:planitprep-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/planitprep-worker.log
```

---

## 🔧 Troubleshooting

| Issue | Cause | Solution |
|---|---|---|
| `Api User is required` | Missing `USER` header | Add `USER: DietitianApi` header |
| `Api Key is invalid` | Wrong `KEY` header | Verify `API_KEY` in `.env` |
| `Unauthenticated` (401) | Expired Sanctum token | Call `/refresh` or re-authenticate |
| ChatGPT timeout | OpenAI response > 120s | Increase `Http::timeout()` |
| `Invalid JSON response from AI` | GPT returned non-JSON | Retry; `response_format: json_object` enforced |
| Queue jobs stuck | Worker not running | `php artisan queue:work` or Supervisor |
| Stripe webhook fails | Signature mismatch | Verify `STRIPE_WEBHOOK_SECRET` |

---

## 🤝 Contributing

### Branch Naming

| Type | Pattern | Example |
|---|---|---|
| Feature | `feature/name` | `feature/batch-meal-inventory` |
| Bug Fix | `fix/description` | `fix/stripe-webhook-duplicate` |
| Hotfix | `hotfix/fix` | `hotfix/ai-timeout-increase` |

### Commit Format

```
type(scope): description

feat(ai): add 7-day recipe deduplication
fix(stripe): handle missing user_id in webhook
```

### Code Conventions

- **PSR-12** coding standard (enforced by Laravel Pint)
- Use **Form Requests** for validation
- Use **API Resources** for responses
- Use **Service classes** for business logic
- Use **Jobs** for long-running operations
- Log with `Log::info()` / `Log::error()` — never `echo`/`dd()` in production
- All secrets via `.env` — never hardcode

---

## 📄 License

This project is licensed under the **MIT License**.

---

<div align="center">

**Built with ❤️ using Laravel 11, OpenAI GPT-4o, and Stripe**

[⬆ Back to Top](#-planit-prep--backend-api-server)

</div>
