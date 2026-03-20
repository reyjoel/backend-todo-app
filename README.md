# 📝 Todo API (Laravel)

## Overview

This is a RESTful API for managing user tasks with support for:

* Authentication via Laravel Sanctum
* Task CRUD operations
* Task reordering and prioritization
* Date-based filtering
* Search functionality

The system is designed with scalability and maintainability in mind, following a layered architecture.

---

## 🏗 Architecture

The application follows a **layered architecture**:

```
Controller → Service → Repository → Model
```

### Why this approach?

* **Controllers** handle HTTP concerns only
* **Services** encapsulate business logic
* **Repositories** abstract data access
* **Models** represent database entities

This separation:

* Improves testability
* Keeps logic reusable
* Prevents fat controllers

---

## 🔐 Authentication

Authentication is handled using **Laravel Sanctum**.

### Flow:

1. User logs in via `/api/login`
2. API returns a token
3. Token is used in headers:

```
Authorization: Bearer {token}
```

---

## 📦 API Endpoints

### Auth

* `POST /api/login`

### Tasks (Protected)

* `GET /api/tasks?date=2026-03-20&q=test`
* `POST /api/tasks`
* `GET /api/tasks/{id}`
* `PUT /api/tasks/{id}`
* `DELETE /api/tasks/{id}`

### Custom

* `PATCH /api/tasks/{task}/toggle`
* `POST /api/tasks/reorder`

---

## 🗃 Database Design

### Tasks Table

| Column       | Type    | Notes             |
| ------------ | ------- | ----------------- |
| id           | bigint  | Primary key       |
| user_id      | FK      | Owner of task     |
| statement    | string  | Task content      |
| is_completed | boolean | Status            |
| task_date    | date    | Scheduled date    |
| priority     | integer | Optional priority |
| position     | integer | Ordering index    |

### Key Decisions

* `position` used for manual ordering
* `task_date` enables calendar-based filtering
* `cascadeOnDelete` ensures cleanup

---

## ⚙️ Setup

```bash
git clone <repo>
cd backend-todo-app

composer install

cp .env.example .env
php artisan key:generate

php artisan migrate

php artisan serve
```

---

## 🧪 Testing (Recommended)

```bash
php artisan test
```

