# Internal Tools Management API - Rust + Axum Implementation

High-performance RESTful API built with Rust and Axum framework for managing internal software tools, subscriptions, and cost analytics.

## 🦀 Tech Stack

- **Rust** 1.75+ (edition 2021)
- **Axum** 0.7 - Ergonomic async web framework
- **Tokio** - Async runtime
- **tokio-postgres** - Async PostgreSQL client
- **deadpool-postgres** - Connection pooling
- **utoipa** - OpenAPI/Swagger documentation
- **Serde** - Serialization/deserialization
- **Chrono** - Date/time handling

## 🚀 Features

### CRUD Operations
- ✅ Create, Read, Update, Delete tools
- ✅ Advanced filtering and pagination
- ✅ Type-safe request validation
- ✅ Proper error handling with descriptive messages

### Analytics Endpoints
- ✅ Department cost analysis
- ✅ Expensive tools identification
- ✅ Tools by category breakdown
- ✅ Low usage tools detection
- ✅ Vendor summary and efficiency ratings

### Additional Features
- ✅ Comprehensive OpenAPI documentation (utoipa)
- ✅ CORS support
- ✅ Request tracing and logging
- ✅ Health check endpoint
- ✅ Async database connection pooling
- ✅ Zero-cost abstractions
- ✅ Memory safety guarantees

## 🛠️ Installation

### Prerequisites
- Rust 1.75 or higher
- PostgreSQL 14+
- Git

### Setup Steps

1. **Navigate to the Rust project**
   ```bash
   cd rust-axum
   ```

2. **Install dependencies**
   ```bash
   cargo build
   ```

3. **Configure environment**
   
   Create a `.env` file in the project root (or use the existing one):
   ```env
   DB_HOST=localhost
   DB_PORT=5432
   DB_USER=dev
   DB_PASSWORD=dev123
   DB_NAME=internal_tools
   PORT=8000
   RUST_LOG=info
   ```

4. **Initialize database**
   
   Run the SQL initialization script:
   ```bash
   psql -U dev -d internal_tools -f ../postgresql/init.sql
   ```

5. **Run the application**
   ```bash
   cargo run
   ```

   For optimized release build:
   ```bash
   cargo run --release
   ```

   The server will start on http://localhost:8000

## 📚 API Documentation

### Swagger UI
Access interactive API documentation at:
```
http://localhost:8000/docs
```

### Available Endpoints

#### Health Check
- `GET /api/health` - Check API and database status

#### Tools Management
- `GET /api/tools` - Get all tools (with filters)
- `GET /api/tools/{id}` - Get tool by ID
- `POST /api/tools` - Create new tool
- `PUT /api/tools/{id}` - Update tool
- `DELETE /api/tools/{id}` - Delete tool

#### Analytics
- `GET /api/analytics/department-costs` - Cost breakdown by department
- `GET /api/analytics/expensive-tools` - Most expensive tools analysis
- `GET /api/analytics/tools-by-category` - Tools grouped by category
- `GET /api/analytics/low-usage-tools` - Underutilized tools detection
- `GET /api/analytics/vendor-summary` - Vendor cost and efficiency analysis

## 🧪 Testing

### Test Health Endpoint
```powershell
Invoke-RestMethod http://localhost:8000/api/health | ConvertTo-Json
```

### Test Tools Endpoint
```powershell
# Get all tools
Invoke-RestMethod http://localhost:8000/api/tools | ConvertTo-Json -Depth 5

# Get with filters
Invoke-RestMethod "http://localhost:8000/api/tools?status=active&limit=10" | ConvertTo-Json -Depth 5

# Get single tool
Invoke-RestMethod http://localhost:8000/api/tools/1 | ConvertTo-Json
```

### Create Tool
```powershell
$body = @{
    name = "Rust Tool"
    description = "A blazingly fast tool"
    vendor = "Rust Foundation"
    website_url = "https://rust-lang.org"
    category_id = 2
    monthly_cost = 0
    owner_department = "Engineering"
    status = "active"
    active_users_count = 100
} | ConvertTo-Json

Invoke-RestMethod -Method POST -Uri http://localhost:8000/api/tools -ContentType "application/json" -Body $body | ConvertTo-Json
```

## 🏗️ Project Structure

```
rust-axum/
├── src/
│   ├── main.rs              # Application entry point with routing
│   ├── db/
│   │   └── mod.rs          # Database connection pooling
│   ├── models/
│   │   ├── mod.rs          # Module exports
│   │   ├── tool.rs         # Tool models and DTOs
│   │   └── analytics.rs    # Analytics models
│   └── handlers/
│       ├── mod.rs          # Module exports
│       ├── health.rs       # Health check handler
│       ├── tools.rs        # CRUD handlers
│       └── analytics.rs    # Analytics handlers
├── Cargo.toml              # Rust dependencies
├── Cargo.lock              # Locked dependency versions
├── .env                    # Environment variables
├── .gitignore             # Git ignore rules
└── README.md              # This file
```

## 🎯 Key Rust Features Demonstrated

### 1. **Ownership & Borrowing**
- Zero-cost memory safety without garbage collection
- No null pointer exceptions
- Thread-safe concurrency

### 2. **Type System**
- Strong static typing with inference
- Option<T> for nullable values
- Result<T, E> for error handling

### 3. **Pattern Matching**
- Exhaustive match expressions
- Error handling with `?` operator
- Destructuring in handlers

### 4. **Async/Await**
- Tokio runtime for async operations
- Non-blocking database queries
- Efficient request handling

### 5. **Traits**
- Serde for serialization
- ToSchema for OpenAPI
- FromSql/ToSql for database types

### 6. **Raw SQL**
- Direct SQL queries with tokio-postgres
- Parameterized queries for SQL injection prevention
- Manual but explicit database operations
- Maximum control and performance

**Note on ORMs:** This implementation uses raw SQL with `tokio-postgres` for explicit control. Rust has excellent ORM options if preferred:
- **Diesel** - Most mature, compile-time query validation
- **SeaORM** - Async-first, similar to TypeORM
- **sqlx** - Async SQL toolkit with compile-time checking

### 7. **Zero-Cost Abstractions**
- No runtime overhead
- Compile-time optimizations
- Inlining and monomorphization

## 🔒 Validation Rules

### Tool Creation/Update
- **name**: Required, string
- **description**: Required, string
- **vendor**: Required, string
- **website_url**: Optional, string
- **category_id**: Required, i32
- **monthly_cost**: Required, f64 >= 0
- **active_users_count**: Optional, i32 >= 0
- **owner_department**: Required, one of: Engineering, Sales, Marketing, HR, Finance, Operations, Design
- **status**: Optional, one of: active, deprecated, trial

## 🚦 HTTP Status Codes

- `200 OK` - Successful GET/PUT requests
- `201 Created` - Successful POST requests
- `204 No Content` - Successful DELETE requests
- `400 Bad Request` - Invalid input or validation errors
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server-side errors

## 📊 Performance

Rust + Axum provides exceptional performance:
- ⚡ **Blazing fast** - Comparable to C/C++
- 🔋 **Low memory** - No garbage collector
- 🔒 **Thread-safe** - Fearless concurrency
- 📦 **Small binaries** - Optimized release builds

Expected response times (approximate):
- Health check: < 1ms
- GET /api/tools: < 10ms
- GET /api/tools/:id: < 5ms
- POST /api/tools: < 15ms
- PUT /api/tools/:id: < 15ms
- DELETE /api/tools/:id: < 10ms
- Analytics endpoints: < 50ms

## 🔧 Development

### Build for Development
```bash
cargo build
```

### Build for Release (Optimized)
```bash
cargo build --release
```

### Run with Hot Reload (using cargo-watch)
```bash
# Install cargo-watch
cargo install cargo-watch

# Run with auto-reload
cargo watch -x run
```

### Format Code
```bash
cargo fmt
```

### Lint Code
```bash
cargo clippy
```

### Run Tests
```bash
cargo test
```

## 🌟 Why Rust + Axum?

### Rust Advantages
- **Memory Safety** - No null pointers, no data races
- **Performance** - Zero-cost abstractions, comparable to C++
- **Concurrency** - Fearless concurrent programming
- **Tooling** - Cargo is excellent for package management
- **Type System** - Prevents many bugs at compile time

### Axum Advantages
- **Ergonomic** - Clean, intuitive API design
- **Type-safe** - Leverages Rust's type system
- **Fast** - Built on Tokio and Hyper
- **Modular** - Easy to compose middleware
- **Well-documented** - Great examples and docs

## 📝 License

MIT License - feel free to use this project for learning or production.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📧 Support

For questions or issues, please open an issue on GitHub.

---

**Built with 🦀 using Rust and Axum Framework**
