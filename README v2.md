# Task 1 — Laravel + Filament: Product & Category Management

## 1. Project Overview

A Laravel application managing Products and Categories through:
- A REST API (`/api/categories`, `/api/products`) with validation, filtering, search, sorting, and pagination.
- A Filament admin panel (`/admin`) for full CRUD management, with authentication, search, sorting, filters, pagination, and product image upload.

Category `hasMany` Products, Product `belongsTo` Category. Product `sku` is unique across the table.

## 2. Technology Stack & Versions

- PHP 8.2+
- Laravel 13.x
- Filament 3.x
- MySQL 8.x
- Composer 2.x

## 3. Why the files are structured this way

This folder contains **only the application-specific files** for this task (models, migrations, controllers, Filament resources, etc.) — not a full vendor/framework skeleton. You'll generate a fresh Laravel skeleton with Composer (which needs real internet access to Packagist) and then overlay these files on top. This keeps the deliverable clean and avoids committing any generated boilerplate, consistent with the assessment's requirement not to commit `vendor/`.

## 4. Preparation

After downloading the php, moving it to your disc C, and adding the path `C:\php-8.5.9` to the environment vairable `Path`, go to `C:\php-8.5.9\` and change the following inside the file `php.ini`:

### Step 1 - Enable the extension zip

Press **Ctrl + F** and search for extension=zip.
Remove the semicolon (;) from the start of the line so it looks exactly like this:

```ini
extension=zip
```

Next, search for extension_dir = "ext". Ensure it does not have a semicolon in front of it either.

Save the file.

### Step 2 - Enable the extension fileinfo

Press **Ctrl + F** and search for extension=fileinfo
Remove the semicolon (;) from the start of the line so it looks exactly like this:

```ini
iniextension=fileinfo
```

Save the file.


### Step 3 - Enable the extension intl

Press **Ctrl + F** and search for extension=intl.
Remove the semicolon (;) from the start of the line so it looks exactly like this:

```ini
extension=intl
```

Save the file.

### Step 4 - Enable MySQL

Press **Ctrl + F** and search for pdo_mysql.
Remove the semicolon (;) from the beginning of the line so it reads:

```ini
iniextension=pdo_mysql
```

(Optional) Search for `extension=mysqli` and uncomment it as well if you plan to use other database tools.

Save and close the file.

## 5. Installation & Setup

### Step 1 — Create a fresh Laravel project

```bash
composer create-project laravel/laravel task1-laravel-filament
cd task1-laravel-filament
```

### Step 2 — Install Filament

```bash
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

When prompted for a panel ID, accept the default (`admin`).

### Step 3 — Enable the API routes file

Laravel 11 doesn't load `routes/api.php` by default. Enable it with:

```bash
php artisan install:api
```

This creates `routes/api.php` (which you'll overwrite with the one provided) and registers it in `bootstrap/app.php`.

### Step 4 — Copy this project's files into place

Copy every file from this folder into your fresh Laravel project, preserving the folder structure (they map 1:1 onto Laravel's standard paths):

```
app/Models/Category.php
app/Models/Product.php
app/Http/Controllers/Api/CategoryController.php
app/Http/Controllers/Api/ProductController.php
app/Http/Requests/StoreCategoryRequest.php
app/Http/Requests/UpdateCategoryRequest.php
app/Http/Requests/StoreProductRequest.php
app/Http/Requests/UpdateProductRequest.php
app/Http/Resources/CategoryResource.php
app/Http/Resources/ProductResource.php
app/Filament/Resources/CategoryResource.php
app/Filament/Resources/CategoryResource/Pages/*.php
app/Filament/Resources/ProductResource.php
app/Filament/Resources/ProductResource/Pages/*.php
app/Providers/Filament/AdminPanelProvider.php   (overwrite the generated one)
database/migrations/2024_01_01_000001_create_categories_table.php
database/migrations/2024_01_01_000002_create_products_table.php
database/factories/CategoryFactory.php
database/factories/ProductFactory.php
database/seeders/CategorySeeder.php
database/seeders/ProductSeeder.php
database/seeders/DatabaseSeeder.php   (overwrite the generated one)
routes/api.php   (overwrite the generated one)
```

### Step 5 — Configure your `.env`

Copy `.env.example` from this folder over the generated `.env` (or merge the `DB_*` values in), then:

```bash
php artisan key:generate
```

Create the MySQL database referenced in `.env` (default name `task1_filament_db`):

```sql
CREATE DATABASE task1_filament_db;
```

### Step 6 — Link storage (for product image uploads)

```bash
php artisan storage:link
```

### Step 7 — Migrate and seed

```bash
php artisan migrate --seed
```

This creates the tables and seeds:
- An admin user: **email `admin@example.com`, password `password`**
- ~10 categories and ~50+ products with realistic sample data

### Step 8 — Run the app

```bash
php artisan serve
```

- API base URL: `http://localhost:8000/api`
- Filament admin panel: `http://localhost:8000/admin` (log in with the credentials above)

## 6. Database Setup & Migrations

Two migrations are included:
- `create_categories_table` — `id, name, description, status, timestamps`
- `create_products_table` — `id, category_id (FK), name, sku (unique), description, price, cost_price, stock_quantity, image_path, status, timestamps`

Deleting a category that still has products is blocked at both the API and Filament layers (a `422` response from the API, and a UI notification in Filament) to protect referential integrity, rather than silently cascading deletes.

## 7. API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/categories` | List categories. Supports `?search=`, `?status=`, `?sort_by=`, `?sort_direction=`, `?per_page=` |
| POST | `/api/categories` | Create a category |
| GET | `/api/categories/{id}` | Show a category |
| PUT/PATCH | `/api/categories/{id}` | Update a category |
| DELETE | `/api/categories/{id}` | Delete a category (blocked if it has products) |
| GET | `/api/products` | List products. Supports `?search=`, `?category_id=`, `?status=`, `?sort_by=`, `?sort_direction=`, `?per_page=` |
| POST | `/api/products` | Create a product |
| GET | `/api/products/{id}` | Show a product |
| PUT/PATCH | `/api/products/{id}` | Update a product |
| DELETE | `/api/products/{id}` | Delete a product |

All endpoints return JSON with appropriate HTTP status codes (`200`, `201`, `404`, `422`). Validation failures return Laravel's standard `422` response with an `errors` object per field.

## 8. Filament Admin Panel

- **Categories**: create/edit/delete, searchable by name, filterable by status, sortable columns, pagination, delete-guard when a category has products.
- **Products**: create/edit/delete, searchable by name/SKU, filterable by category and status, sortable columns, pagination, image upload (with built-in image editor/cropper), color-coded stock and status badges.
- Both resources sit under a "Catalog" navigation group.
- The panel requires authentication — unauthenticated visits to `/admin` redirect to `/admin/login`.

**Test credentials**: `admin@example.com` / `password`

## 9. Verification Checklist

After following the setup steps above, confirm:

- [ ] `php artisan serve` boots without errors
- [ ] `/admin/login` loads and you can log in with the seeded admin
- [ ] Categories and Products lists show seeded data
- [ ] Creating/editing/deleting a Category works; deleting a Category with products shows a blocking notification
- [ ] Creating a Product with a duplicate SKU shows a validation error
- [ ] Product search, category filter, status filter, and column sorting all work in Filament
- [ ] `GET http://localhost:8000/api/products` returns paginated JSON
- [ ] `POST http://localhost:8000/api/products` with an invalid/missing field returns `422` with error details
- [ ] `POST http://localhost:8000/api/categories` then `GET /api/categories/{id}` round-trips correctly

## 10. Known Design Decisions

- SKU uniqueness is enforced both at the database level (unique index) and in validation (`unique` rule), so races and direct DB inserts are still protected.
- Category deletion is **restricted**, not cascading, when products exist — this avoids silent data loss; the alternative (cascade) was considered but rejected for a catalog management context.
- Prices are stored as `decimal(12,2)` rather than floats to avoid floating-point rounding issues in a financial context.
