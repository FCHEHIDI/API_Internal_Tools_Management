# PHP Laravel Implementation - Setup Guide

## Prerequisites Installation

### 1. Install PHP (Windows)

**Option A: Using XAMPP (Recommended for Windows)**
1. Download XAMPP from https://www.apachefriends.org/download.html
2. Install XAMPP (includes PHP, Apache, MySQL)
3. Add PHP to PATH: `C:\xampp\php`
4. Verify: `php --version` (should show PHP 8.3+)

**Option B: Using Chocolatey**
```powershell
choco install php
choco install composer
```

**Option C: Manual PHP Installation**
1. Download from https://windows.php.net/download/
2. Extract to `C:\php`
3. Add `C:\php` to System PATH
4. Copy `php.ini-development` to `php.ini`
5. Enable extensions in `php.ini`:
   - `extension=pdo_pgsql`
   - `extension=pgsql`
   - `extension=mbstring`
   - `extension=openssl`
   - `extension=curl`

### 2. Install Composer

1. Download from https://getcomposer.org/Composer-Setup.exe
2. Run installer (will detect PHP automatically)
3. Verify: `composer --version`

### 3. Verify Installation

```powershell
php --version      # Should show PHP 8.3+
composer --version # Should show Composer 2.x
```

## Laravel Project Setup

### 1. Create Laravel Project

```powershell
# In the project root (API_Internal_Tools_Management/)
composer create-project laravel/laravel php-laravel --prefer-dist
cd php-laravel
```

### 2. Install Dependencies

```powershell
composer require doctrine/dbal
composer install
```

### 3. Configure Environment

Copy `.env.example` to `.env` and update:

```env
APP_NAME="Internal Tools API"
APP_ENV=local
APP_KEY=base64:... # Will be generated
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=internal_tools
DB_USERNAME=postgres
DB_PASSWORD=postgres

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 4. Generate Application Key

```powershell
php artisan key:generate
```

### 5. Start Database

```powershell
# From project root
docker-compose --profile postgres up -d
```

### 6. Run Migrations

```powershell
# We'll create migrations for tools and categories
php artisan migrate
```

### 7. Start Development Server

```powershell
php artisan serve
# API will be available at http://localhost:8000
```

## Implementation Overview

### API Endpoints to Implement

**CRUD Operations:**
- `GET /api/tools` - List tools with filters
- `GET /api/tools/{id}` - Get tool details
- `POST /api/tools` - Create new tool
- `PUT /api/tools/{id}` - Update tool
- `DELETE /api/tools/{id}` - Delete tool (soft delete)

**Analytics:**
- `GET /api/analytics/department-costs` - Department cost breakdown
- `GET /api/analytics/expensive-tools` - Top expensive tools
- `GET /api/analytics/tools-by-category` - Category distribution
- `GET /api/analytics/low-usage-tools` - Underutilized tools
- `GET /api/analytics/vendor-summary` - Vendor analysis

### Laravel Structure

```
php-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ToolController.php         # CRUD operations
│   │   │   └── AnalyticsController.php    # Analytics endpoints
│   │   ├── Requests/
│   │   │   ├── StoreToolRequest.php       # Validation for POST
│   │   │   └── UpdateToolRequest.php      # Validation for PUT
│   │   └── Resources/
│   │       ├── ToolResource.php           # JSON response formatting
│   │       └── ToolCollection.php         # Collection formatting
│   ├── Models/
│   │   ├── Tool.php                       # Eloquent model
│   │   └── Category.php                   # Eloquent model
│   └── Services/
│       └── AnalyticsService.php           # Business logic for analytics
├── database/
│   ├── migrations/
│   │   ├── 2025_01_01_000001_create_categories_table.php
│   │   └── 2025_01_01_000002_create_tools_table.php
│   └── seeders/
│       └── DatabaseSeeder.php             # Seed initial data
├── routes/
│   └── api.php                            # API route definitions
└── tests/
    └── Feature/
        ├── ToolApiTest.php
        └── AnalyticsApiTest.php
```

### Key Laravel Features to Use

1. **Eloquent ORM** - Database interactions
2. **Request Validation** - Form requests with validation rules
3. **API Resources** - Consistent JSON responses
4. **Service Layer** - Complex business logic (analytics)
5. **Query Builder** - Complex SQL queries with joins/aggregations
6. **Exception Handling** - Custom error responses

## Development Workflow

### 1. Create Models & Migrations

```powershell
php artisan make:model Category -m
php artisan make:model Tool -m
```

### 2. Create Controllers

```powershell
php artisan make:controller ToolController --api
php artisan make:controller AnalyticsController
```

### 3. Create Form Requests

```powershell
php artisan make:request StoreToolRequest
php artisan make:request UpdateToolRequest
```

### 4. Create API Resources

```powershell
php artisan make:resource ToolResource
php artisan make:resource ToolCollection
```

### 5. Define Routes

Edit `routes/api.php` to add endpoints

### 6. Run Tests

```powershell
php artisan test
# or
./vendor/bin/phpunit
```

## Database Schema

### Categories Table
```sql
- id (SERIAL PRIMARY KEY)
- name (VARCHAR)
- description (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Tools Table
```sql
- id (SERIAL PRIMARY KEY)
- name (VARCHAR UNIQUE)
- description (TEXT)
- vendor (VARCHAR)
- website_url (VARCHAR NULLABLE)
- category_id (INTEGER FK -> categories.id)
- monthly_cost (DECIMAL(10,2))
- owner_department (ENUM)
- status (ENUM: active, deprecated, trial)
- active_users_count (INTEGER DEFAULT 0)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

## API Documentation

Laravel will use **Laravel Scribe** for API documentation:

```powershell
composer require --dev knuckleswtf/scribe
php artisan scribe:generate
```

Documentation will be available at `/docs`

## Testing Database

Use existing PostgreSQL Docker container:
- Database: `internal_tools`
- Port: `5432`
- User: `postgres`
- Password: `postgres`

## Troubleshooting

### PHP Extensions Missing
Edit `php.ini` and enable:
```ini
extension=pdo_pgsql
extension=pgsql
extension=mbstring
extension=openssl
```

### Composer Memory Issues
```powershell
$env:COMPOSER_MEMORY_LIMIT=-1
composer install
```

### Permission Issues
```powershell
# Windows - No action needed usually
# Linux/WSL:
chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed
1. Verify PostgreSQL is running: `docker ps`
2. Check `.env` database credentials
3. Test connection: `php artisan migrate:status`

## Next Steps

1. ✅ Install PHP and Composer
2. ✅ Create Laravel project
3. ⏳ Create models and migrations
4. ⏳ Implement CRUD endpoints
5. ⏳ Implement analytics endpoints
6. ⏳ Add validation and error handling
7. ⏳ Generate API documentation
8. ⏳ Write tests
9. ⏳ Update README with setup instructions

## Resources

- Laravel Documentation: https://laravel.com/docs/11.x
- Laravel API Resources: https://laravel.com/docs/11.x/eloquent-resources
- Laravel Validation: https://laravel.com/docs/11.x/validation
- Laravel Scribe (API Docs): https://scribe.knuckles.wtf/laravel/
