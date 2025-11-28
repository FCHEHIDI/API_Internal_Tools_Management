# Rust + Axum CRUD Architecture - Request Flow Pipeline

## 📊 Complete Request Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           CLIENT REQUEST                                    │
│                  POST /api/tools (Create New Tool)                          │
│                  Content-Type: application/json                             │
│                  Body: {"name":"Slack", "vendor":"Slack",...}               │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 1: HANDLER (Axum Route Handler)                                      │
│  📁 handlers/tools.rs                                                       │
├─────────────────────────────────────────────────────────────────────────────┤
│  use axum::{Json, Extension, http::StatusCode};                             │
│  use sqlx::PgPool;                                                          │
│                                                                             │
│  pub async fn create_tool(                                                  │
│      Extension(pool): Extension<PgPool>,    // Database connection pool    │
│      Json(payload): Json<CreateToolRequest> // Auto-deserialize JSON       │
│  ) -> Result<(StatusCode, Json<ToolResponse>), ApiError> {                  │
│      // Step 1: Validate input (Serde does basic validation)                │
│      // Step 2: Call service layer for business logic                       │
│      let tool = tool_service::create_tool(&pool, payload).await?;           │
│                                                                             │
│      // Step 3: Return 201 Created with JSON response                       │
│      Ok((StatusCode::CREATED, Json(tool)))                                  │
│  }                                                                          │
│                                                                             │
│  ROLE: HTTP request handling, routing, response formatting                  │
│  INPUT: HTTP request + CreateToolRequest (deserialized by Serde)            │
│  OUTPUT: HTTP 201 + ToolResponse as JSON                                    │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │   Serde deserialization │
                    │   + validation          │
                    └────────────┬────────────┘
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 2: STRUCTS (Data Validation & Serialization with Serde)              │
│  📁 models/tool.rs                                                          │
├─────────────────────────────────────────────────────────────────────────────┤
│  use serde::{Deserialize, Serialize};                                       │
│  use validator::Validate;                                                   │
│  use rust_decimal::Decimal;                                                 │
│  use chrono::{DateTime, Utc};                                               │
│                                                                             │
│  // PostgreSQL ENUM types                                                   │
│  #[derive(Debug, Serialize, Deserialize, sqlx::Type)]                       │
│  #[sqlx(type_name = "department_type", rename_all = "PascalCase")]          │
│  pub enum Department {                                                      │
│      Engineering,                                                           │
│      Sales,                                                                 │
│      Marketing,                                                             │
│      IT,                                                                    │
│      HR,                                                                    │
│      Finance,                                                               │
│      Operations,                                                            │
│  }                                                                          │
│                                                                             │
│  #[derive(Debug, Serialize, Deserialize, sqlx::Type)]                       │
│  #[sqlx(type_name = "tool_status_type", rename_all = "lowercase")]          │
│  pub enum ToolStatus {                                                      │
│      Active,                                                                │
│      Deprecated,                                                            │
│      Trial,                                                                 │
│  }                                                                          │
│                                                                             │
│  #[derive(Debug, Deserialize, Validate)]                                    │
│  pub struct CreateToolRequest {                                             │
│      #[validate(length(min = 2, max = 100))]                                │
│      pub name: String,                                                      │
│                                                                             │
│      #[validate(length(max = 500))]                                         │
│      pub description: Option<String>,                                       │
│                                                                             │
│      #[validate(length(min = 1))]                                           │
│      pub vendor: String,                                                    │
│                                                                             │
│      #[validate(url)]                                                       │
│      pub website_url: Option<String>,                                       │
│                                                                             │
│      #[validate(range(min = 0))]                                            │
│      pub monthly_cost: Decimal,                                             │
│                                                                             │
│      pub category_id: i32,                                                  │
│      pub owner_department: Department,                                      │
│      pub status: Option<ToolStatus>,                                        │
│                                                                             │
│      #[validate(range(min = 0))]                                            │
│      pub active_users_count: i32,                                           │
│  }                                                                          │
│                                                                             │
│  #[derive(Debug, Serialize, sqlx::FromRow)]                                 │
│  pub struct ToolResponse {                                                  │
│      pub id: i32,                                                           │
│      pub name: String,                                                      │
│      pub description: Option<String>,                                       │
│      pub vendor: String,                                                    │
│      pub website_url: Option<String>,                                       │
│      pub category: String,              // Joined from categories table     │
│      pub monthly_cost: Decimal,                                             │
│      pub owner_department: Department,                                      │
│      pub status: ToolStatus,                                                │
│      pub active_users_count: i32,                                           │
│      pub created_at: DateTime<Utc>,                                         │
│      pub updated_at: DateTime<Utc>,                                         │
│  }                                                                          │
│                                                                             │
│  ROLE: Data structures, validation rules, serialization/deserialization     │
│  INPUT: JSON from HTTP request                                              │
│  OUTPUT: Validated Rust structs (or validation errors)                      │
│                                                                             │
│  IF VALIDATION FAILS: Returns 422 Unprocessable Entity ──────────────────┐  │
└────────────────────────────────┬────────────────────────────────────────┘│  │
                                 │                                          │  │
                                 ▼                                          │  │
┌────────────────────────────────────────────────────────────────────────┼──┤
│  LAYER 3: SERVICE (Business Logic Layer)                              │  │
│  📁 services/tool_service.rs                                          │  │
├────────────────────────────────────────────────────────────────────────┼──┤
│  use sqlx::PgPool;                                                     │  │
│  use crate::models::{CreateToolRequest, ToolResponse};                │  │
│  use crate::errors::ApiError;                                         │  │
│                                                                        │  │
│  pub async fn create_tool(                                            │  │
│      pool: &PgPool,                                                   │  │
│      data: CreateToolRequest                                          │  │
│  ) -> Result<ToolResponse, ApiError> {                                │  │
│      // STEP 1: Validate input using validator crate                  │  │
│      data.validate()                                                  │  │
│          .map_err(|e| ApiError::ValidationError(e.to_string()))?;    │ ─┘
│                                                                        │
│      // STEP 2: Verify category exists (business rule)                │
│      let category_exists = sqlx::query_scalar!(                       │
│          "SELECT EXISTS(SELECT 1 FROM categories WHERE id = $1)",     │
│          data.category_id                                             │
│      )                                                                │
│      .fetch_one(pool)                                                 │
│      .await?                                                          │
│      .unwrap_or(false);                                               │
│                                                                        │
│      if !category_exists {                                            │
│          return Err(ApiError::NotFound(                               │
│              format!("Category {} not found", data.category_id)       │
│          ));                                                          │
│      }                                                                │
│                                                                        │
│      // STEP 3: Insert into database using SQLx                       │
│      let status = data.status.unwrap_or(ToolStatus::Active);          │
│                                                                        │
│      let tool = sqlx::query_as!(                                      │
│          ToolResponse,                                                │
│          r#"                                                          │
│          INSERT INTO tools (                                          │
│              name, description, vendor, website_url,                  │
│              monthly_cost, category_id, owner_department,             │
│              status, active_users_count                               │
│          )                                                            │
│          VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)                  │
│          RETURNING                                                    │
│              id, name, description, vendor, website_url,              │
│              monthly_cost,                                            │
│              owner_department as "owner_department: Department",      │
│              status as "status: ToolStatus",                          │
│              active_users_count,                                      │
│              created_at, updated_at,                                  │
│              (SELECT name FROM categories WHERE id = $6) as category  │
│          "#,                                                          │
│          data.name,                                                   │
│          data.description,                                            │
│          data.vendor,                                                 │
│          data.website_url,                                            │
│          data.monthly_cost,                                           │
│          data.category_id,                                            │
│          data.owner_department as Department,                         │
│          status as ToolStatus,                                        │
│          data.active_users_count                                      │
│      )                                                                │
│      .fetch_one(pool)                                                 │
│      .await?;                                                         │
│                                                                        │
│      Ok(tool)                                                         │
│  }                                                                    │
│                                                                        │
│  ROLE: Business logic, validation, database operations                │
│  INPUT: Database pool + validated struct                              │
│  OUTPUT: ToolResponse or ApiError                                     │
└────────────────────────────────┬───────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 4: SQLx (Compile-Time Checked SQL Queries)                          │
│  📁 Direct SQL with type safety                                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  // SQLx provides compile-time verification of SQL queries                  │
│  // No ORM abstraction - direct SQL with type safety                        │
│                                                                             │
│  Features:                                                                  │
│  ✅ Compile-time SQL validation (catches typos before runtime)              │
│  ✅ Automatic Rust type mapping from PostgreSQL types                       │
│  ✅ Connection pooling built-in                                             │
│  ✅ Async/await support (Tokio runtime)                                     │
│  ✅ PostgreSQL ENUM support via #[sqlx(type_name = "...")]                  │
│                                                                             │
│  Example Query Macro:                                                       │
│  sqlx::query_as!(                                                           │
│      ToolResponse,                      // Map result to this struct        │
│      r#"                                                                    │
│      SELECT                                                                 │
│          t.id, t.name, t.description,                                       │
│          t.owner_department as "owner_department: Department",              │
│          t.status as "status: ToolStatus",                                  │
│          c.name as category                                                 │
│      FROM tools t                                                           │
│      JOIN categories c ON t.category_id = c.id                              │
│      WHERE t.id = $1                                                        │
│      "#,                                                                    │
│      tool_id                                                                │
│  )                                                                          │
│  .fetch_one(pool)                                                           │
│  .await?;                                                                   │
│                                                                             │
│  ROLE: Type-safe database queries, connection management                    │
│  INPUT: SQL queries + parameters                                            │
│  OUTPUT: Rust structs or query errors                                       │
│  ADVANTAGE: Catches SQL errors at compile time, not runtime!                │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         DATABASE (PostgreSQL 15)                            │
│  📊 Table: tools                                                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  SQL Executed:                                                              │
│                                                                             │
│  INSERT INTO tools (                                                        │
│    name, description, vendor, website_url, monthly_cost,                    │
│    category_id, owner_department, status,                                   │
│    active_users_count                                                       │
│  ) VALUES (                                                                 │
│    'Slack',                                                                 │
│    'Team messaging platform',                                               │
│    'Slack Technologies',                                                    │
│    'https://slack.com',                                                     │
│    8.00,                                                                    │
│    1,                                                                       │
│    'Engineering'::department_type,                                          │
│    'active'::tool_status_type,                                              │
│    0                                                                        │
│  )                                                                          │
│  RETURNING                                                                  │
│    id, name, description, vendor, website_url,                              │
│    monthly_cost, owner_department, status,                                  │
│    active_users_count, created_at, updated_at,                              │
│    (SELECT name FROM categories WHERE id = 1) as category;                  │
│                                                                             │
│  Result: Row(id=21, name="Slack", category="Communication", ...)            │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
              ┌──────────────────┴──────────────────┐
              │  RESPONSE FLOW (Going back up)      │
              └──────────────────┬──────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                      HTTP RESPONSE TO CLIENT                                │
│  Status: 201 Created                                                        │
│  Content-Type: application/json                                             │
│  Body:                                                                      │
│  {                                                                          │
│    "id": 21,                                                                │
│    "name": "Slack",                                                         │
│    "description": "Team messaging platform",                                │
│    "vendor": "Slack Technologies",                                          │
│    "website_url": "https://slack.com",                                      │
│    "category": "Communication",                                             │
│    "monthly_cost": 8.00,                                                    │
│    "owner_department": "Engineering",                                       │
│    "status": "active",                                                      │
│    "active_users_count": 0,                                                 │
│    "created_at": "2025-11-28T16:30:00Z",                                    │
│    "updated_at": "2025-11-28T16:30:00Z"                                     │
│  }                                                                          │
└─────────────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────────┐
│  ERROR HANDLING (Custom Error Type)                                        │
│  📁 errors/mod.rs                                                          │
├────────────────────────────────────────────────────────────────────────────┤
│  use axum::{                                                               │
│      response::{IntoResponse, Response},                                   │
│      http::StatusCode,                                                     │
│      Json,                                                                 │
│  };                                                                        │
│  use serde_json::json;                                                     │
│                                                                            │
│  #[derive(Debug)]                                                          │
│  pub enum ApiError {                                                       │
│      NotFound(String),                                                     │
│      ValidationError(String),                                              │
│      DatabaseError(sqlx::Error),                                           │
│      InternalError(String),                                                │
│  }                                                                         │
│                                                                            │
│  // Implement IntoResponse to convert errors to HTTP responses             │
│  impl IntoResponse for ApiError {                                          │
│      fn into_response(self) -> Response {                                  │
│          let (status, error_message) = match self {                        │
│              ApiError::NotFound(msg) => (                                  │
│                  StatusCode::NOT_FOUND,                                    │
│                  msg                                                       │
│              ),                                                            │
│              ApiError::ValidationError(msg) => (                           │
│                  StatusCode::UNPROCESSABLE_ENTITY,                         │
│                  msg                                                       │
│              ),                                                            │
│              ApiError::DatabaseError(e) => (                               │
│                  StatusCode::INTERNAL_SERVER_ERROR,                        │
│                  format!("Database error: {}", e)                          │
│              ),                                                            │
│              ApiError::InternalError(msg) => (                             │
│                  StatusCode::INTERNAL_SERVER_ERROR,                        │
│                  msg                                                       │
│              ),                                                            │
│          };                                                                │
│                                                                            │
│          let body = Json(json!({                                           │
│              "error": error_message,                                       │
│              "status": status.as_u16()                                     │
│          }));                                                              │
│                                                                            │
│          (status, body).into_response()                                    │
│      }                                                                     │
│  }                                                                         │
│                                                                            │
│  // Convert sqlx::Error to ApiError                                        │
│  impl From<sqlx::Error> for ApiError {                                     │
│      fn from(err: sqlx::Error) -> Self {                                   │
│          ApiError::DatabaseError(err)                                      │
│      }                                                                     │
│  }                                                                         │
│                                                                            │
│  ROLE: Type-safe error handling, automatic HTTP error responses            │
│  ADVANTAGE: Compile-time guarantee all errors are handled                  │
└────────────────────────────────────────────────────────────────────────────┘
```

## 🎯 Key Rust/Axum Concepts

### **1. Ownership & Borrowing**
```rust
// Rust's memory safety without garbage collection
pub async fn create_tool(
    pool: &PgPool,              // Borrowed reference (no ownership transfer)
    data: CreateToolRequest     // Owned data (moved into function)
) -> Result<ToolResponse, ApiError> {
    // data is consumed here, cannot be used after
}
```

### **2. Type Safety Everywhere**
```rust
// Compile-time type checking catches errors before runtime
#[derive(Debug, Serialize, Deserialize, sqlx::Type)]
#[sqlx(type_name = "department_type")]
pub enum Department {
    Engineering,
    Sales,
    // ... compiler ensures exhaustive matching
}
```

### **3. Result Type (No Exceptions)**
```rust
// Explicit error handling - no hidden exceptions
pub async fn create_tool() -> Result<ToolResponse, ApiError> {
    let tool = sqlx::query!(...).fetch_one(pool).await?;  // ? propagates errors
    Ok(tool)  // Explicit success
}
```

### **4. Async/Await with Tokio**
```rust
// Non-blocking I/O for high performance
#[tokio::main]
async fn main() {
    let app = Router::new()
        .route("/api/tools", post(create_tool))
        .layer(Extension(pool));
    
    axum::Server::bind(&"0.0.0.0:8000".parse().unwrap())
        .serve(app.into_make_service())
        .await
        .unwrap();
}
```

### **5. Compile-Time SQL Verification**
```rust
// SQLx checks SQL queries at compile time using database schema
sqlx::query_as!(
    ToolResponse,
    "SELECT id, name FROM tools WHERE id = $1",  // Checked against actual DB
    tool_id
)
// If column doesn't exist or types mismatch → compile error!
```

## 📝 Complete CRUD Operations Flow

### **CREATE (POST /api/tools)**
```
Client → Axum Handler (create_tool)
      → Serde deserializes & validates JSON
      → Service layer (business logic)
      → SQLx query! macro (compile-time checked)
      → PostgreSQL database
      → Return ToolResponse (201 Created)
```

### **READ (GET /api/tools/{id})**
```
Client → Axum Handler (get_tool)
      → Extract path parameter
      → Service layer
      → SQLx query_as! (type-safe SELECT)
      → PostgreSQL database
      → Serialize with Serde
      → Return JSON (200 OK)
```

### **UPDATE (PUT /api/tools/{id})**
```
Client → Axum Handler (update_tool)
      → Serde validates UpdateToolRequest
      → Service layer (fetch + update)
      → SQLx UPDATE query
      → PostgreSQL database
      → Return updated ToolResponse (200 OK)
```

### **DELETE (DELETE /api/tools/{id})**
```
Client → Axum Handler (delete_tool)
      → Service layer
      → SQLx DELETE query
      → PostgreSQL database
      → Return 204 No Content
```

### **LIST with FILTERS (GET /api/tools?department=Engineering)**
```
Client → Axum Handler (with Query extractor)
      → Service layer builds dynamic SQL
      → SQLx query_as! with filters
      → PostgreSQL WHERE clause
      → Return Vec<ToolResponse> (200 OK)
```

## 🔥 Rust/Axum Advantages

✅ **Memory Safety** - No null pointer errors, no data races (guaranteed at compile time)  
✅ **Zero-Cost Abstractions** - High-level code compiles to fast machine code  
✅ **Compile-Time SQL Checks** - SQLx verifies queries against actual database schema  
✅ **No Runtime Exceptions** - All errors explicit via Result type  
✅ **Fearless Concurrency** - Thread safety guaranteed by compiler  
✅ **Minimal Dependencies** - Small binary size, fast startup  

## 🆚 Rust vs Other Stacks

| Feature | Rust Axum | Python FastAPI | Java Spring Boot |
|---------|-----------|----------------|------------------|
| **Memory Safety** | ⭐⭐⭐⭐⭐ Compile-time | ⭐⭐⭐ Runtime GC | ⭐⭐⭐⭐ Runtime GC |
| **Performance** | ⭐⭐⭐⭐⭐ Fastest | ⭐⭐⭐⭐ Fast | ⭐⭐⭐⭐⭐ Very fast |
| **Type Safety** | ⭐⭐⭐⭐⭐ Compile-time | ⭐⭐⭐⭐ Runtime | ⭐⭐⭐⭐⭐ Compile-time |
| **Learning Curve** | ⭐⭐⭐⭐⭐ Very steep | ⭐⭐ Easy | ⭐⭐⭐⭐ Steep |
| **Error Handling** | Result type (explicit) | Exceptions | Exceptions |
| **SQL Verification** | Compile-time (SQLx) | Runtime | Runtime |
| **Binary Size** | ~10MB | N/A (interpreted) | ~50MB+ with JVM |
| **Startup Time** | Instant | Fast | Slow (JVM warmup) |
| **Null Safety** | Option<T> (no null) | Optional (runtime) | Optional (runtime) |

## 💡 Why Rust + Axum?

1. **Performance** - Comparable to C/C++, faster than Python/Java
2. **Safety** - No null pointers, no data races, memory safe without GC
3. **Reliability** - If it compiles, it usually works correctly
4. **Modern Async** - Tokio runtime provides excellent async I/O
5. **SQLx Magic** - Compile-time SQL verification catches bugs early
6. **Low Resource Usage** - Small memory footprint, efficient CPU usage

## ⚠️ Rust Trade-offs

- **Steep Learning Curve** - Ownership & borrowing concepts take time to master
- **Slower Development** - Fighting with borrow checker initially
- **Less Ecosystem Maturity** - Fewer libraries than Python/Java/JS
- **Longer Compile Times** - Type checking & SQL verification takes time
- **But** → Once it compiles, it's rock solid! 🪨

---

**This Rust Axum architecture ensures:**
✅ Memory safety without garbage collection  
✅ Compile-time verification of SQL queries  
✅ Zero-cost abstractions for maximum performance  
✅ Explicit error handling (no hidden exceptions)  
✅ Thread-safe concurrent code guaranteed by compiler  
✅ PostgreSQL ENUM support via SQLx type mapping

