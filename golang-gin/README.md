# Internal Tools Management API - Go/Gin Implementation

Enterprise-grade RESTful API built with Go and Gin framework for managing internal software tools, subscriptions, and cost analytics.

## 🚀 Tech Stack

- **Go** 1.21+
- **Gin** - Fast HTTP web framework
- **PostgreSQL** - Primary database
- **Swagger** - API documentation (via swaggo)
- **godotenv** - Environment configuration

## 📋 Features

### CRUD Operations
- ✅ Create, Read, Update, Delete tools
- ✅ Advanced filtering and pagination
- ✅ Input validation with Gin binding tags
- ✅ Proper error handling with descriptive messages

### Analytics Endpoints
- ✅ Department cost analysis
- ✅ Expensive tools identification
- ✅ Tools by category breakdown
- ✅ Low usage tools detection
- ✅ Vendor summary and efficiency ratings

### Additional Features
- ✅ Comprehensive Swagger documentation
- ✅ CORS support
- ✅ Request logging middleware
- ✅ Health check endpoint
- ✅ Database connection pooling

## 🛠️ Installation

### Prerequisites
- Go 1.21 or higher
- PostgreSQL 14+
- Git

### Setup Steps

1. **Clone the repository**
   ```bash
   cd golang-gin
   ```

2. **Install dependencies**
   ```bash
   go mod download
   ```

3. **Configure environment**
   
   Create a `.env` file in the project root (or use the existing one):
   ```env
   DB_HOST=localhost
   DB_PORT=5432
   DB_USER=dev
   DB_PASSWORD=dev123
   DB_NAME=internal_tools_db
   PORT=8000
   GIN_MODE=debug
   ```

4. **Initialize database**
   
   Run the SQL initialization script:
   ```bash
   psql -U dev -d internal_tools_db -f ../postgresql/init.sql
   ```

5. **Generate Swagger documentation**
   ```bash
   # Install swag CLI if not already installed
   go install github.com/swaggo/swag/cmd/swag@latest
   
   # Generate docs
   swag init
   ```

6. **Run the application**
   ```bash
   go run main.go
   ```

   The server will start on http://localhost:8000

## 📚 API Documentation

### Swagger UI
Access interactive API documentation at:
```
http://localhost:8000/docs/index.html
```

### Available Endpoints

#### Health Check
- `GET /api/health` - Check API and database status

#### Tools Management
- `GET /api/tools` - Get all tools (with filters)
- `GET /api/tools/:id` - Get tool by ID
- `POST /api/tools` - Create new tool
- `PUT /api/tools/:id` - Update tool
- `DELETE /api/tools/:id` - Delete tool

#### Analytics
- `GET /api/analytics/department-costs` - Cost breakdown by department
- `GET /api/analytics/expensive-tools` - Most expensive tools analysis
- `GET /api/analytics/tools-by-category` - Tools grouped by category
- `GET /api/analytics/low-usage-tools` - Underutilized tools detection
- `GET /api/analytics/vendor-summary` - Vendor cost and efficiency analysis

## 🧪 Testing

### Test Health Endpoint
```bash
curl http://localhost:8000/api/health
```

### Test Tools Endpoint
```bash
# Get all tools
curl http://localhost:8000/api/tools

# Get with filters
curl "http://localhost:8000/api/tools?status=active&limit=10"

# Get single tool
curl http://localhost:8000/api/tools/1
```

### Create Tool
```powershell
$body = @{
    name = "Test Tool"
    description = "A test tool from Go"
    vendor = "Test Vendor"
    website_url = "https://example.com"
    category_id = 1
    monthly_cost = 10.50
    owner_department = "Engineering"
    status = "active"
    active_users_count = 5
} | ConvertTo-Json

Invoke-RestMethod -Method POST -Uri http://localhost:8000/api/tools -ContentType "application/json" -Body $body
```

## 🏗️ Project Structure

```
golang-gin/
├── main.go                 # Application entry point
├── go.mod                  # Go module dependencies
├── go.sum                  # Dependency checksums
├── config/
│   └── database.go         # Database configuration
├── models/
│   ├── tool.go            # Tool models and DTOs
│   └── analytics.go        # Analytics models
├── handlers/
│   ├── health.go          # Health check handler
│   ├── tools.go           # CRUD handlers
│   ├── analytics.go        # Analytics handlers (part 1)
│   └── analytics_low_usage.go  # Analytics handlers (part 2)
├── middleware/
│   └── logger.go          # Request logging middleware
├── docs/
│   ├── docs.go            # Generated Swagger docs
│   ├── swagger.json       # Swagger JSON spec
│   └── swagger.yaml       # Swagger YAML spec
└── README.md              # This file
```

## 🎯 Key Go Features Demonstrated

### 1. **Gin Framework**
- Clean routing with handler functions
- Middleware support (CORS, logging)
- Request binding and validation
- JSON response handling

### 2. **Database Integration**
- Standard library `database/sql` with PostgreSQL driver (`lib/pq`)
- **Raw SQL queries** (no ORM) for maximum control and performance
- Connection pooling (10 max open, 2 max idle connections)
- Prepared statements for SQL injection prevention
- Proper error handling with `sql.ErrNoRows` for 404s
- NULL-safe scanning with `sql.NullString` for nullable fields

**Note on ORMs:** This implementation uses raw SQL with `database/sql` for explicit control and learning purposes. Go has excellent ORM options if preferred:
- **GORM** - Full-featured ORM (similar to TypeORM/Sequelize)
- **sqlx** - Lightweight extension of database/sql
- **ent** - Graph-based ORM by Facebook/Meta
- **sqlc** - Generates type-safe Go from SQL queries

### 3. **Type Safety**
- Struct-based models with JSON tags
- Pointer fields for optional values (`*string`, `*int`)
- Strong typing throughout
- No reflection overhead in queries

### 4. **Validation**
- Gin binding tags for automatic validation
  - `binding:"required"` - Required fields
  - `binding:"min=2,max=100"` - Length constraints
  - `binding:"oneof=active deprecated trial"` - Enum validation
- Custom validation logic where needed
- Descriptive error messages

### 5. **Documentation**
- Swagger annotations on handlers
- Auto-generated interactive docs
- Clear API specifications

### 6. **Error Handling**
- Consistent error response structure
- Proper HTTP status codes
- Descriptive error messages
- No panics - explicit error returns (Go idiom)

## 🔒 Validation Rules

### Tool Creation/Update
- **name**: Required, 2-100 characters
- **description**: Required
- **vendor**: Required, max 100 characters
- **website_url**: Optional, must be valid URL
- **category_id**: Required, must exist in database
- **monthly_cost**: Required, >= 0
- **active_users_count**: Optional, >= 0
- **owner_department**: Required, must be one of: Engineering, Sales, Marketing, HR, Finance, Operations, Design
- **status**: Optional, must be one of: active, deprecated, trial

## 🚦 HTTP Status Codes

- `200 OK` - Successful GET/PUT requests
- `201 Created` - Successful POST requests
- `204 No Content` - Successful DELETE requests
- `400 Bad Request` - Invalid input or validation errors
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server-side errors

## 📊 Analytics Features

### Department Costs
- Total cost per department
- Tools count per department
- Average cost per tool
- Cost percentage of total budget
- Summary with most expensive department

### Expensive Tools
- Top N most costly tools
- Cost per user calculation
- Efficiency rating (excellent, good, average, low)
- Potential savings identification

### Tools by Category
- Category-wise aggregations
- Budget percentage breakdown
- Most expensive and efficient categories

### Low Usage Tools
- Configurable usage threshold
- Warning levels (high, medium, low)
- Suggested actions
- Monthly and annual savings potential

### Vendor Summary
- Multi-level aggregations per vendor
- Department distribution
- Vendor efficiency ratings
- Single-tool vendor identification

## 🔧 Development

### Build
```bash
go build -o api-server .
```

### Run with Hot Reload
```bash
# Install air for hot reload
go install github.com/cosmtrek/air@latest

# Run with air
air
```

### Format Code
```bash
go fmt ./...
```

### Run Tests
```bash
go test ./...
```

## 📝 License

MIT License - feel free to use this project for learning or production.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📧 Support

For questions or issues, please open an issue on GitHub.

---

**Built with ❤️ using Go and Gin Framework**
