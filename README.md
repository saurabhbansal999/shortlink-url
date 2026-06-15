# Laravel Sembark Assignment

A URL shortener application built with Laravel, Inertia.js, and React.

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL

---

## Setup

**1. Clone the repository**

```bash
git clone <repository-url>
cd assignment-laravel-sembark
```

**2. Install dependencies**

```bash
composer install
npm install
```

**3. Configure environment**

```bash
cp .env.example .env
```

> The `.env.example` is already pre-configured for local MySQL. Update `DB_PASSWORD` if your MySQL root password is different.

**4. Run migrations and seed**

```bash
php artisan migrate --seed
```

**5. Build frontend assets**

```bash
npm run build
```

**6. Start the server**

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000)

---

## Default Credentials

| Role       | Email                    | Password   |
|------------|--------------------------|------------|
| Super Admin | superadmin@yopmail.com  | password   |

---

## Running Tests

```bash
php artisan test
```

---

## Roles

| Role         | Can Create URLs | Can View URLs          |
|--------------|-----------------|------------------------|
| Super Admin  | No              | All (across companies) |
| Company Admin| No              | Own company only       |
| Member       | No              | Own URLs only          |
