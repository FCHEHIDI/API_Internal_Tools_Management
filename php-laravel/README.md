# PHP Laravel - Internal Tools API

## 🎯 Overview

PHP implementation of the Internal Tools Management API using Laravel 11.x framework with Eloquent ORM and PostgreSQL database.

## 🛠️ Technologies

- **Language**: PHP 8.3+
- **Framework**: Laravel 11.x
- **ORM**: Eloquent ORM
- **Database**: PostgreSQL 16
- **API Documentation**: Laravel Scribe
- **Port**: 8000 (configurable)

## 📋 Prerequisites

Before starting, you need:
- PHP 8.3+ with extensions: pdo_pgsql, pgsql, mbstring, openssl, curl
- Composer 2.x
- Docker & Docker Compose (for PostgreSQL)
- Git

**See [SETUP_GUIDE.md](SETUP_GUIDE.md) for detailed installation instructions on Windows**

## 🚀 Quick Start

### 1. Start Database

```powershell
# From project root (API_Internal_Tools_Management/)
docker-compose --profile postgres up -d
```

### 2. Install Dependencies

```powershell
cd php-laravel
composer install
```

### 3. Configure Environment

```powershell
# Copy example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Update .env

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=internal_tools
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 5. Run Migrations

```powershell
php artisan migrate
```

### 6. Start Development Server

```powershell
php artisan serve
```

✅ API available at: **http://localhost:8000**

✅ Documentation: **http://localhost:8000/docs**

## 📚 API Endpoints

### CRUD Operations

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tools` | List all tools with filters |
| GET | `/api/tools/{id}` | Get tool details |
| POST | `/api/tools` | Create new tool |
| PUT | `/api/tools/{id}` | Update existing tool |
| DELETE | `/api/tools/{id}` | Delete tool (soft delete) |

### Analytics Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/analytics/department-costs` | Department cost breakdown |
| GET | `/api/analytics/expensive-tools` | Top expensive tools analysis |
| GET | `/api/analytics/tools-by-category` | Tools distribution by category |
| GET | `/api/analytics/low-usage-tools` | Underutilized tools report |
| GET | `/api/analytics/vendor-summary` | Vendor cost analysis |

## 🧪 Testing

```powershell
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter ToolApiTest

# Run with coverage
php artisan test --coverage
```

## 🏗️ Project Structure

```
php-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ToolController.php         # CRUD operations
│   │   │   └── AnalyticsController.php    # Analytics endpoints
│   │   ├── Requests/
│   │   │   ├── StoreToolRequest.php       # POST validation
│   │   │   └── UpdateToolRequest.php      # PUT validation
│   │   └── Resources/
│   │       ├── ToolResource.php           # JSON formatting
│   │       └── ToolCollection.php         # Collection formatting
│   ├── Models/
│   │   ├── Tool.php                       # Eloquent model
│   │   └── Category.php                   # Eloquent model
│   └── Services/
│       └── AnalyticsService.php           # Business logic
├── database/
│   ├── migrations/                        # Database schema
│   └── seeders/                           # Test data
├── routes/
│   └── api.php                            # API routes
└── tests/
    └── Feature/                           # API tests
```

## 🔍 Key Features

### 1. Eloquent ORM
Laravel's powerful ORM for database interactions with relationships, scopes, and query builder.

### 2. Form Request Validation
Dedicated request classes with validation rules:
```php
// StoreToolRequest.php
public function rules()
{
    return [
        'name' => 'required|string|min:2|max:100|unique:tools',
        'monthly_cost' => 'required|numeric|min:0',
        'owner_department' => 'required|in:Engineering,Sales,Marketing,HR,Finance,Operations,Design',
    ];
}
```

### 3. API Resources
Consistent JSON response formatting:
```php
// ToolResource.php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'monthly_cost' => (float) $this->monthly_cost,
        'category' => $this->category->name,
    ];
}
```

### 4. Service Layer
Complex business logic separation:
```php
// AnalyticsService.php
public function getDepartmentCosts()
{
    return Tool::where('status', 'active')
        ->groupBy('owner_department')
        ->selectRaw('owner_department, SUM(monthly_cost) as total_cost')
        ->get();
}
```

### 5. Exception Handling
Global exception handler for consistent error responses.

## 📝 Configuration

### Environment Variables

Key variables in `.env`:

```env
APP_NAME="Internal Tools API"
APP_ENV=local
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
```

### Database Configuration

Connection defined in `config/database.php`:
```php
'pgsql' => [
    'driver' => 'pgsql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'internal_tools'),
    'username' => env('DB_USERNAME', 'postgres'),
    'password' => env('DB_PASSWORD', 'postgres'),
],
```

## 🔧 Development

### Useful Artisan Commands

```powershell
# Create controller
php artisan make:controller ToolController --api

# Create model with migration
php artisan make:model Tool -m

# Create form request
php artisan make:request StoreToolRequest

# Create resource
php artisan make:resource ToolResource

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh database with seeders
php artisan migrate:fresh --seed

# Generate API documentation
php artisan scribe:generate

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Database Seeding

```powershell
# Seed all data
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=CategorySeeder
```

## 🎨 Why Laravel?

### Advantages for this Project:

1. **Mature Ecosystem** - 12+ years of development, battle-tested
2. **Eloquent ORM** - Intuitive Active Record pattern with relationships
3. **Built-in Validation** - Comprehensive validation with custom rules
4. **API Resources** - Elegant JSON transformation layer
5. **Artisan CLI** - Powerful code generation and task automation
6. **Middleware** - Easy request/response pipeline
7. **Service Container** - Dependency injection out of the box
8. **Testing Suite** - PHPUnit integration with database factories
9. **Queue System** - Built-in job queuing for async tasks
10. **Large Community** - Extensive packages and documentation

### Laravel vs Other PHP Frameworks:

- **vs Symfony**: Laravel is more approachable, faster development
- **vs Slim/Lumen**: Laravel provides more features out of the box
- **vs CodeIgniter**: Laravel has better ORM and modern architecture
- **vs CakePHP**: Laravel has larger community and ecosystem

## 📊 Performance Considerations

### Optimization Strategies:

1. **Database Indexing** - Indexes on frequently queried columns
2. **Eager Loading** - Prevent N+1 queries with `with()`
3. **Query Caching** - Cache expensive analytics queries
4. **API Rate Limiting** - Protect endpoints from abuse
5. **Response Caching** - Cache static data responses
6. **Database Connection Pooling** - Reuse connections

### Example Optimizations:

```php
// Eager load relationships to avoid N+1
Tool::with('category')->get();

// Cache expensive queries
Cache::remember('department-costs', 3600, function () {
    return AnalyticsService::getDepartmentCosts();
});

// Select specific columns only
Tool::select(['id', 'name', 'monthly_cost'])->get();
```

## 🐛 Troubleshooting

### Common Issues:

**"Could not find driver" error**
```powershell
# Enable pdo_pgsql in php.ini:
extension=pdo_pgsql
extension=pgsql
```

**"The stream or file could not be opened" error**
```powershell
# Clear config cache
php artisan config:clear

# Fix permissions (Linux/WSL)
chmod -R 775 storage bootstrap/cache
```

**"SQLSTATE[HY000] [2002] Connection refused"**
```powershell
# Verify PostgreSQL is running
docker ps

# Check database credentials in .env
# Test connection
php artisan migrate:status
```

## 📖 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Eloquent ORM](https://laravel.com/docs/11.x/eloquent)
- [API Resources](https://laravel.com/docs/11.x/eloquent-resources)
- [Validation](https://laravel.com/docs/11.x/validation)
- [Testing](https://laravel.com/docs/11.x/testing)
- [Laravel Scribe](https://scribe.knuckles.wtf/laravel/)

## 🤝 Contributing

This is a training project comparing multiple technology stacks. See parent repository for overall project structure.

## 📄 License

This project is part of the API Internal Tools Management multi-stack comparison.
