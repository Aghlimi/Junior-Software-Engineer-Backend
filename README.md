# Junior Software Engineer Backend Challenge

Laravel + Vue implementation for product and category management with CLI commands, layered architecture, and product listing features.

## Stack

- PHP / Laravel
- Vue 3
- MySQL (or SQLite for local tests)

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

## Run

Start backend:

```bash
php artisan serve
```

Start frontend build/watch:

```bash
npm run dev
```

## Tests

Run all tests:

```bash
php artisan test
```

Run product feature tests only:

```bash
php artisan test tests/Feature/ProductsTest.php
```

## Architecture

The project follows a layered flow:

Controller -> Service -> Repository -> Eloquent Model

- Controllers handle request/response concerns.
- Services coordinate business use cases.
- Repositories encapsulate data access queries.

## Implemented Features

### CLI

- Create category: `php artisan category:create`
- Delete category: `php artisan category:delete`
- Create product: `php artisan product:create`
- Delete product: `php artisan product:delete`

### Web / API

- Create product (name, description, price, image, optional categories)
- Browse products with:
	- Pagination
	- Sort by name
	- Sort by price
	- Filter by category

## Notes

- Product images are stored on the `public` disk.
- API routes are defined in `routes/api.php`.