# Internal Tools Management API - Java Spring Boot

REST API for managing internal SaaS tools with analytics and reporting capabilities.

## 🚀 Technologies

- **Language**: Java 17 (OpenJDK Temurin LTS)
- **Framework**: Spring Boot 3.2.0
- **ORM**: Spring Data JPA with Hibernate
- **Database**: PostgreSQL 15
- **Documentation**: Springdoc OpenAPI 3 (Swagger UI)
- **Build Tool**: Maven
- **Port**: 8080 (configurable)

## 📋 Features

### CRUD Endpoints (Part 1)
- ✅ `GET /api/tools` - List all tools with filters (department, status, category, cost range)
- ✅ `GET /api/tools/{id}` - Get tool details by ID
- ✅ `POST /api/tools` - Create new tool
- ✅ `PUT /api/tools/{id}` - Update existing tool
- ✅ `DELETE /api/tools/{id}` - Delete tool

### Analytics Endpoints (Part 2)
- ✅ `GET /api/analytics/department-costs` - Cost breakdown by department
- ✅ `GET /api/analytics/expensive-tools` - Most expensive tools analysis
- ✅ `GET /api/analytics/tools-by-category` - Tool distribution by category
- ✅ `GET /api/analytics/low-usage-tools` - Underutilized tools identification
- ✅ `GET /api/analytics/vendor-summary` - Vendor consolidation opportunities

### Health Check
- ✅ `GET /api/health` - API health status

## 🏗️ Architecture

```
src/main/java/com/techcorp/internaltools/
├── InternalToolsApplication.java      # Main Spring Boot application
├── config/
│   └── OpenApiConfig.java             # OpenAPI/Swagger configuration
├── controller/
│   ├── HealthController.java          # Health check endpoint
│   ├── ToolController.java            # CRUD operations
│   └── AnalyticsController.java       # Analytics endpoints
├── service/
│   ├── ToolService.java               # Business logic for CRUD
│   └── AnalyticsService.java          # Analytics calculations
├── repository/
│   ├── ToolRepository.java            # Data access for tools
│   └── CategoryRepository.java        # Data access for categories
├── model/
│   ├── Tool.java                      # Tool entity with JPA annotations
│   ├── Category.java                  # Category entity
│   ├── Department.java                # Department ENUM (7 values)
│   └── ToolStatus.java                # Status ENUM (active, deprecated, trial)
├── dto/
│   ├── CreateToolRequest.java         # Create tool DTO with validation
│   ├── UpdateToolRequest.java         # Update tool DTO
│   ├── ToolResponse.java              # Tool response DTO
│   ├── ToolListResponse.java          # Paginated list response
│   └── analytics/                     # Analytics DTOs
│       ├── DepartmentCostsResponse.java
│       ├── ExpensiveToolsResponse.java
│       ├── ToolsByCategoryResponse.java
│       ├── LowUsageToolsResponse.java
│       └── VendorSummaryResponse.java
└── exception/
    ├── GlobalExceptionHandler.java    # Centralized error handling
    ├── ResourceNotFoundException.java # Custom exception
    └── ErrorResponse.java             # Error response DTO
```

## 🚀 Quick Start

### Prerequisites
- Java 17 or higher
- Maven 3.6+
- PostgreSQL 15
- Docker (optional, for database)

### 1. Start PostgreSQL Database

Using Docker Compose (recommended):
```powershell
# Navigate to project root
cd C:\Users\info\API_Internal_Tools_Management

# Start PostgreSQL with existing data
docker-compose --profile postgres up -d
```

Or connect to existing PostgreSQL instance at:
- Host: `localhost:5432`
- Database: `internal_tools`
- Username: `dev`
- Password: `dev123`

### 2. Configure Database Connection

Edit `src/main/resources/application.yml` if needed:
```yaml
spring:
  datasource:
    url: jdbc:postgresql://localhost:5432/internal_tools
    username: dev
    password: dev123
```

### 3. Build and Run

```powershell
# Navigate to java-springboot directory
cd java-springboot

# Build the project
mvn clean install

# Run the application
mvn spring-boot:run
```

Or run the JAR directly:
```powershell
mvn clean package
java -jar target/internal-tools-api-1.0.0.jar
```

### 4. Verify API is Running

```powershell
# Health check
curl http://localhost:8080/api/health

# Get all tools
curl http://localhost:8080/api/tools
```

### 5. Access API Documentation

Open your browser and navigate to:
- **Swagger UI**: http://localhost:8080/swagger-ui.html
- **OpenAPI JSON**: http://localhost:8080/api-docs

## 📖 API Examples

### Create a New Tool

```powershell
$body = @'
{
  "name": "Linear",
  "description": "Issue tracking and project management",
  "vendor": "Linear",
  "websiteUrl": "https://linear.app",
  "categoryId": 2,
  "monthlyCost": 8.00,
  "ownerDepartment": "Engineering",
  "status": "active"
}
'@

Invoke-RestMethod -Uri 'http://localhost:8080/api/tools' -Method Post -Body $body -ContentType 'application/json'
```

### Update an Existing Tool

```powershell
$body = @'
{
  "monthlyCost": 9.50,
  "status": "active",
  "activeUsersCount": 25
}
'@

Invoke-RestMethod -Uri 'http://localhost:8080/api/tools/5' -Method Put -Body $body -ContentType 'application/json'
```

### Get Filtered Tools

```powershell
# Filter by department and status
curl "http://localhost:8080/api/tools?department=Engineering&status=active"

# Filter by cost range
curl "http://localhost:8080/api/tools?min_cost=10&max_cost=50"
```

### Analytics Examples

```powershell
# Department costs
curl "http://localhost:8080/api/analytics/department-costs?sort_by=total_cost&order=desc"

# Top 5 expensive tools
curl "http://localhost:8080/api/analytics/expensive-tools?limit=5"

# Low usage tools (less than 3 users)
curl "http://localhost:8080/api/analytics/low-usage-tools?max_users=3"

# Tools by category
curl "http://localhost:8080/api/analytics/tools-by-category"

# Vendor summary
curl "http://localhost:8080/api/analytics/vendor-summary"
```

## 🔧 Configuration

### Environment Variables

You can override configuration using environment variables:

```powershell
# Database configuration
$env:SPRING_DATASOURCE_URL="jdbc:postgresql://localhost:5432/internal_tools"
$env:SPRING_DATASOURCE_USERNAME="dev"
$env:SPRING_DATASOURCE_PASSWORD="dev123"

# Server port
$env:SERVER_PORT="8080"

# Run application
mvn spring-boot:run
```

### application.yml Properties

Key configuration properties:
- `spring.datasource.*` - Database connection settings
- `spring.jpa.hibernate.ddl-auto` - Schema management (set to `none` for production)
- `spring.jpa.show-sql` - Show SQL queries in logs (disable in production)
- `server.port` - API port (default: 8080)
- `springdoc.*` - OpenAPI/Swagger settings

## ✅ Validation Rules

### Tool Creation/Update
- `name`: Required, 2-100 characters, unique
- `vendor`: Required, max 100 characters
- `monthlyCost`: Required, ≥ 0, max 2 decimals
- `annualCost`: Optional, ≥ 0, max 2 decimals
- `ownerDepartment`: Required, valid enum (Engineering, Sales, Marketing, HR, Finance, Operations, Design)
- `status`: Optional, valid enum (active, deprecated, trial), defaults to `active`
- `websiteUrl`: Optional, must be valid URL format (http/https)
- `categoryId`: Required, must exist in database

## 🎯 Business Logic Highlights

### ENUM Handling
- **PostgreSQL ENUMs**: Uses `@JdbcTypeCode(SqlTypes.NAMED_ENUM)` for proper PostgreSQL ENUM type mapping
- **Department**: 7 values (Engineering, Sales, Marketing, HR, Finance, Operations, Design)
- **ToolStatus**: 3 values (active, deprecated, trial)

### Analytics Calculations
- **Cost Percentages**: Calculated with 1 decimal precision, sum to 100% (±0.1% tolerance)
- **Efficiency Ratings**: Based on cost-per-user ratios vs company averages
- **Active Tools Only**: All analytics filter to `status='active'` unless specified
- **Warning Levels**: Calculated based on cost-per-user thresholds (low: <€20, medium: €20-50, high: >€50)

### Cost Calculations
- **Total Monthly Cost**: `monthly_cost × active_users_count`
- **Cost Per User**: `total_monthly_cost ÷ active_users_count` (with zero-division handling)
- **Potential Savings**: Sum of high + medium warning level tools

## 🧪 Testing

### Manual Testing with Swagger UI
1. Navigate to http://localhost:8080/swagger-ui.html
2. Test each endpoint interactively
3. View request/response schemas

### PowerShell Testing
```powershell
# Test all CRUD operations
$testTool = @{name="Test Tool"; vendor="Test"; categoryId=1; monthlyCost=10.0; ownerDepartment="Engineering"}
$created = Invoke-RestMethod -Uri 'http://localhost:8080/api/tools' -Method Post -Body ($testTool | ConvertTo-Json) -ContentType 'application/json'

# Get created tool
Invoke-RestMethod -Uri "http://localhost:8080/api/tools/$($created.id)"

# Update tool
$update = @{activeUsersCount=5}
Invoke-RestMethod -Uri "http://localhost:8080/api/tools/$($created.id)" -Method Put -Body ($update | ConvertTo-Json) -ContentType 'application/json'

# Delete tool
Invoke-RestMethod -Uri "http://localhost:8080/api/tools/$($created.id)" -Method Delete
```

## 📊 Error Handling

### Standard Error Response
```json
{
  "error": "Validation failed",
  "message": "Invalid request data",
  "details": {
    "name": "Name is required and must be 2-100 characters",
    "monthlyCost": "Must be a positive number"
  }
}
```

### HTTP Status Codes
- `200 OK` - Successful GET/PUT
- `201 Created` - Successful POST
- `204 No Content` - Successful DELETE
- `400 Bad Request` - Validation errors
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server errors

## 🛠️ Development

### Build Commands
```powershell
# Clean build
mvn clean install

# Skip tests
mvn clean install -DskipTests

# Run tests only
mvn test

# Package JAR
mvn package
```

### IDE Setup
- **IntelliJ IDEA**: Import as Maven project, enable Lombok annotation processing
- **VS Code**: Install Java Extension Pack, Spring Boot Extension Pack

## 📝 Design Decisions

### Why Spring Boot?
- **Industry Standard**: Widely adopted for enterprise Java applications
- **Convention over Configuration**: Reduces boilerplate with auto-configuration
- **Rich Ecosystem**: Extensive library support for JPA, validation, OpenAPI
- **Production Ready**: Built-in metrics, health checks, and monitoring

### JPA/Hibernate ORM
- **Type Safety**: Compile-time checking with strongly-typed queries
- **ENUM Support**: Native PostgreSQL ENUM mapping with `@JdbcTypeCode`
- **Relationship Management**: Automatic JOIN handling for Category relationships
- **Transaction Management**: Declarative transactions with `@Transactional`

### DTO Pattern
- **Separation of Concerns**: Entity vs API contracts
- **Validation**: Jakarta Bean Validation on DTOs prevents invalid data
- **Flexibility**: Different DTOs for create/update/response operations
- **Security**: Prevents over-posting attacks

### Exception Handling
- **Centralized**: `@RestControllerAdvice` for consistent error responses
- **Detailed**: Validation errors include field-level details
- **Standard**: Uses HTTP status codes correctly

## 🚨 Known Considerations

### PostgreSQL ENUM Types
- **Mapping**: Uses Hibernate 6.x `@JdbcTypeCode(SqlTypes.NAMED_ENUM)` for proper ENUM support
- **No SQL Injection**: Spring Data JPA handles parameter binding safely
- **Type Safety**: Java ENUMs provide compile-time validation

### Analytics Performance
- **In-Memory Calculations**: Current implementation loads data into memory for calculations
- **Optimization Opportunity**: For large datasets, consider moving complex calculations to SQL/database views
- **Active Filter**: All analytics pre-filter to active tools only

## 📚 Additional Resources

- [Spring Boot Documentation](https://spring.io/projects/spring-boot)
- [Spring Data JPA](https://spring.io/projects/spring-data-jpa)
- [Springdoc OpenAPI](https://springdoc.org/)
- [PostgreSQL ENUM Types](https://www.postgresql.org/docs/current/datatype-enum.html)

## 👥 Contact

For questions or issues, contact the TechCorp Solutions API team.

---

**API Version**: 1.0.0  
**Last Updated**: November 28, 2025  
**Implementation**: Java + Spring Boot (Stack #8)
