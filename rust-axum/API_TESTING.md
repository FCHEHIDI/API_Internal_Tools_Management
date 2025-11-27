# API Testing Guide - Rust + Axum Implementation

Complete testing guide for the Internal Tools Management API including Swagger UI, PowerShell commands, and curl examples.

---

## 🌐 Swagger UI Documentation

### Access Swagger UI
Once the server is running, access the interactive API documentation:

```
http://localhost:8000/docs
```

### Features
- **Interactive Testing:** Test all endpoints directly from the browser
- **Request Examples:** See example request bodies
- **Response Schemas:** View all response structures
- **Try It Out:** Execute real API calls
- **Schema Documentation:** Complete data model documentation

---

## 🚀 Starting the Server

### Development Mode
```powershell
cd rust-axum
cargo run
```

### Release Mode (Optimized)
```powershell
cd rust-axum
cargo build --release
./target/release/internal_tools_api
```

### Expected Output
```
[INFO] Database connection pool created
[INFO] Starting server on 0.0.0.0:8000
[INFO] Swagger UI available at http://localhost:8000/docs
```

---

## 🧪 Testing Endpoints

### 1. Health Check

#### PowerShell
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/health" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET http://localhost:8000/health
```

#### Expected Response
```json
{
  "status": "healthy",
  "timestamp": "2024-01-15T10:30:45Z",
  "database": "connected",
  "response_time_ms": 2
}
```

---

### 2. GET All Tools (No Filters)

#### PowerShell
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET http://localhost:8000/api/tools
```

#### Expected Response
```json
{
  "data": [
    {
      "id": 1,
      "name": "GitHub",
      "description": "Version control and collaboration",
      "vendor": "GitHub Inc.",
      "category": "Development",
      "department": "Engineering",
      "status": "active",
      "monthly_cost": 120.00,
      "users": 50,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z",
      "created_by": "admin",
      "last_updated_by": "admin"
    }
  ],
  "total": 1,
  "filters": {
    "department": null,
    "status": null,
    "vendor": null,
    "category": null,
    "min_cost": null,
    "max_cost": null,
    "page": 1,
    "limit": 10
  }
}
```

---

### 3. GET Tools with Filters

#### Filter by Department
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?department=Engineering" -Method Get | ConvertTo-Json -Depth 10
```

```bash
curl -X GET "http://localhost:8000/api/tools?department=Engineering"
```

#### Filter by Status
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?status=active" -Method Get | ConvertTo-Json -Depth 10
```

```bash
curl -X GET "http://localhost:8000/api/tools?status=active"
```

#### Filter by Cost Range
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?min_cost=100&max_cost=500" -Method Get | ConvertTo-Json -Depth 10
```

```bash
curl -X GET "http://localhost:8000/api/tools?min_cost=100&max_cost=500"
```

#### Multiple Filters
```powershell
$uri = "http://localhost:8000/api/tools?department=Engineering&status=active&min_cost=100"
Invoke-RestMethod -Uri $uri -Method Get | ConvertTo-Json -Depth 10
```

```bash
curl -X GET "http://localhost:8000/api/tools?department=Engineering&status=active&min_cost=100"
```

#### Pagination
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?page=2&limit=5" -Method Get | ConvertTo-Json -Depth 10
```

```bash
curl -X GET "http://localhost:8000/api/tools?page=2&limit=5"
```

---

### 4. GET Single Tool by ID

#### PowerShell
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools/1" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET http://localhost:8000/api/tools/1
```

#### Expected Response
```json
{
  "id": 1,
  "name": "GitHub",
  "description": "Version control and collaboration",
  "vendor": "GitHub Inc.",
  "category": "Development",
  "department": "Engineering",
  "status": "active",
  "monthly_cost": 120.00,
  "users": 50,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "created_by": "admin",
  "last_updated_by": "admin"
}
```

#### Not Found (404)
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools/99999" -Method Get
```

```json
{
  "error": "Tool with id 99999 not found"
}
```

---

### 5. POST Create New Tool

#### PowerShell
```powershell
$body = @{
    name = "Slack"
    description = "Team communication platform"
    vendor = "Slack Technologies"
    category = "Communication"
    department = "Engineering"
    status = "active"
    monthly_cost = 150.00
    users = 75
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools" -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X POST http://localhost:8000/api/tools \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Slack",
    "description": "Team communication platform",
    "vendor": "Slack Technologies",
    "category": "Communication",
    "department": "Engineering",
    "status": "active",
    "monthly_cost": 150.00,
    "users": 75
  }'
```

#### With Optional Fields
```powershell
$body = @{
    name = "Jira"
    description = "Project management tool"
    vendor = "Atlassian"
    category = "Project Management"
    department = "Engineering"
    status = "active"
    monthly_cost = 200.00
    users = 100
    created_by = "john.doe"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools" -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 10
```

#### Expected Response (201 Created)
```json
{
  "id": 15,
  "name": "Slack",
  "description": "Team communication platform",
  "vendor": "Slack Technologies",
  "category": "Communication",
  "department": "Engineering",
  "status": "active",
  "monthly_cost": 150.00,
  "users": 75,
  "created_at": "2024-01-15T10:35:22Z",
  "updated_at": "2024-01-15T10:35:22Z",
  "created_by": null,
  "last_updated_by": null
}
```

---

### 6. PUT Update Tool

#### PowerShell - Full Update
```powershell
$body = @{
    name = "GitHub Enterprise"
    description = "Enterprise version control"
    vendor = "GitHub Inc."
    category = "Development"
    department = "Engineering"
    status = "active"
    monthly_cost = 250.00
    users = 100
    last_updated_by = "admin"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools/1" -Method Put -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 10
```

#### curl - Full Update
```bash
curl -X PUT http://localhost:8000/api/tools/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "GitHub Enterprise",
    "description": "Enterprise version control",
    "vendor": "GitHub Inc.",
    "category": "Development",
    "department": "Engineering",
    "status": "active",
    "monthly_cost": 250.00,
    "users": 100,
    "last_updated_by": "admin"
  }'
```

#### PowerShell - Partial Update (Only Cost and Users)
```powershell
$body = @{
    monthly_cost = 180.00
    users = 85
    last_updated_by = "admin"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools/1" -Method Put -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 10
```

#### curl - Partial Update
```bash
curl -X PUT http://localhost:8000/api/tools/1 \
  -H "Content-Type: application/json" \
  -d '{
    "monthly_cost": 180.00,
    "users": 85,
    "last_updated_by": "admin"
  }'
```

#### Expected Response (200 OK)
```json
{
  "id": 1,
  "name": "GitHub Enterprise",
  "description": "Enterprise version control",
  "vendor": "GitHub Inc.",
  "category": "Development",
  "department": "Engineering",
  "status": "active",
  "monthly_cost": 250.00,
  "users": 100,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-15T10:40:12Z",
  "created_by": "admin",
  "last_updated_by": "admin"
}
```

---

### 7. DELETE Tool

#### PowerShell
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools/15" -Method Delete | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X DELETE http://localhost:8000/api/tools/15
```

#### Expected Response (200 OK)
```json
{
  "message": "Tool deleted successfully"
}
```

#### Not Found (404)
```json
{
  "error": "Tool with id 99999 not found"
}
```

---

### 8. Analytics - Department Costs

#### PowerShell
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/department-costs" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET http://localhost:8000/api/analytics/department-costs
```

#### Expected Response
```json
[
  {
    "department": "Engineering",
    "total_cost": 5420.00,
    "tool_count": 12,
    "avg_cost": 451.67,
    "percentage": 45.2
  },
  {
    "department": "Sales",
    "total_cost": 3200.00,
    "tool_count": 8,
    "avg_cost": 400.00,
    "percentage": 26.7
  },
  {
    "department": "Marketing",
    "total_cost": 2180.00,
    "tool_count": 6,
    "avg_cost": 363.33,
    "percentage": 18.2
  }
]
```

---

### 9. Analytics - Expensive Tools

#### PowerShell - Default (Top 10)
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/expensive-tools" -Method Get | ConvertTo-Json -Depth 10
```

#### PowerShell - Custom Limit
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/expensive-tools?limit=5" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET "http://localhost:8000/api/analytics/expensive-tools?limit=5"
```

#### Expected Response
```json
[
  {
    "id": 3,
    "name": "Salesforce",
    "vendor": "Salesforce.com",
    "category": "CRM",
    "department": "Sales",
    "monthly_cost": 1200.00,
    "users": 50,
    "efficiency_rating": 24.00,
    "status": "active"
  },
  {
    "id": 7,
    "name": "Adobe Creative Cloud",
    "vendor": "Adobe",
    "category": "Design",
    "department": "Marketing",
    "monthly_cost": 999.00,
    "users": 25,
    "efficiency_rating": 39.96,
    "status": "active"
  }
]
```

---

### 10. Analytics - Tools by Category

#### PowerShell
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/tools-by-category" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET http://localhost:8000/api/analytics/tools-by-category
```

#### Expected Response
```json
[
  {
    "category": "Development",
    "tools": [
      {
        "id": 1,
        "name": "GitHub",
        "monthly_cost": 120.00,
        "users": 50,
        "status": "active"
      },
      {
        "id": 5,
        "name": "VS Code",
        "monthly_cost": 0.00,
        "users": 75,
        "status": "active"
      }
    ],
    "total_tools": 2,
    "avg_users": 62.5,
    "most_expensive": "GitHub",
    "least_expensive": "VS Code"
  },
  {
    "category": "Communication",
    "tools": [
      {
        "id": 2,
        "name": "Slack",
        "monthly_cost": 150.00,
        "users": 75,
        "status": "active"
      }
    ],
    "total_tools": 1,
    "avg_users": 75.0,
    "most_expensive": "Slack",
    "least_expensive": "Slack"
  }
]
```

---

### 11. Analytics - Low Usage Tools

#### PowerShell - Default Threshold (5 users)
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/low-usage-tools" -Method Get | ConvertTo-Json -Depth 10
```

#### PowerShell - Custom Threshold
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/low-usage-tools?threshold=10" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET "http://localhost:8000/api/analytics/low-usage-tools?threshold=10"
```

#### Expected Response
```json
[
  {
    "id": 8,
    "name": "Confluence",
    "vendor": "Atlassian",
    "category": "Documentation",
    "department": "Engineering",
    "monthly_cost": 300.00,
    "users": 0,
    "warning_level": "critical",
    "cost_impact": 300.00,
    "status": "active"
  },
  {
    "id": 12,
    "name": "Miro",
    "vendor": "Miro",
    "category": "Collaboration",
    "department": "Design",
    "monthly_cost": 120.00,
    "users": 3,
    "warning_level": "high",
    "cost_impact": 120.00,
    "status": "trial"
  },
  {
    "id": 9,
    "name": "Notion",
    "vendor": "Notion Labs",
    "category": "Productivity",
    "department": "HR",
    "monthly_cost": 80.00,
    "users": 8,
    "warning_level": "medium",
    "cost_impact": 80.00,
    "status": "active"
  }
]
```

**Warning Levels:**
- **critical:** 0 users (no usage at all)
- **high:** < threshold/2 users (very low usage)
- **medium:** < threshold users (below threshold)

---

### 12. Analytics - Vendor Summary

#### PowerShell
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/vendor-summary" -Method Get | ConvertTo-Json -Depth 10
```

#### curl
```bash
curl -X GET http://localhost:8000/api/analytics/vendor-summary
```

#### Expected Response
```json
[
  {
    "vendor": "Atlassian",
    "total_cost": 800.00,
    "tool_count": 3,
    "avg_cost": 266.67,
    "departments": ["Engineering", "Operations"]
  },
  {
    "vendor": "GitHub Inc.",
    "total_cost": 250.00,
    "tool_count": 1,
    "avg_cost": 250.00,
    "departments": ["Engineering"]
  },
  {
    "vendor": "Slack Technologies",
    "total_cost": 150.00,
    "tool_count": 1,
    "avg_cost": 150.00,
    "departments": ["Engineering"]
  }
]
```

---

## 🧪 Batch Testing Script

### PowerShell Script for All Endpoints
```powershell
# Batch API Testing Script
$baseUrl = "http://localhost:8000"

Write-Host "=== Testing Internal Tools API ===" -ForegroundColor Cyan

# 1. Health Check
Write-Host "`n[1] Testing Health Endpoint..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/health" -Method Get | ConvertTo-Json

# 2. Get All Tools
Write-Host "`n[2] Testing GET All Tools..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/tools" -Method Get | ConvertTo-Json -Depth 5

# 3. Get Tools with Filters
Write-Host "`n[3] Testing GET Tools with Filters..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/tools?department=Engineering&status=active" -Method Get | ConvertTo-Json -Depth 5

# 4. Get Single Tool
Write-Host "`n[4] Testing GET Single Tool..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/tools/1" -Method Get | ConvertTo-Json

# 5. Create New Tool
Write-Host "`n[5] Testing POST Create Tool..." -ForegroundColor Yellow
$newTool = @{
    name = "Test Tool"
    description = "Testing API"
    vendor = "Test Vendor"
    category = "Testing"
    department = "Engineering"
    status = "trial"
    monthly_cost = 99.99
    users = 10
} | ConvertTo-Json

$created = Invoke-RestMethod -Uri "$baseUrl/api/tools" -Method Post -Body $newTool -ContentType "application/json"
$created | ConvertTo-Json
$newId = $created.id

# 6. Update Tool
Write-Host "`n[6] Testing PUT Update Tool..." -ForegroundColor Yellow
$updateTool = @{
    monthly_cost = 149.99
    users = 15
} | ConvertTo-Json

Invoke-RestMethod -Uri "$baseUrl/api/tools/$newId" -Method Put -Body $updateTool -ContentType "application/json" | ConvertTo-Json

# 7. Analytics - Department Costs
Write-Host "`n[7] Testing Analytics - Department Costs..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/analytics/department-costs" -Method Get | ConvertTo-Json -Depth 5

# 8. Analytics - Expensive Tools
Write-Host "`n[8] Testing Analytics - Expensive Tools..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/analytics/expensive-tools?limit=5" -Method Get | ConvertTo-Json -Depth 5

# 9. Analytics - Tools by Category
Write-Host "`n[9] Testing Analytics - Tools by Category..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/analytics/tools-by-category" -Method Get | ConvertTo-Json -Depth 5

# 10. Analytics - Low Usage
Write-Host "`n[10] Testing Analytics - Low Usage..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/analytics/low-usage-tools?threshold=10" -Method Get | ConvertTo-Json -Depth 5

# 11. Analytics - Vendor Summary
Write-Host "`n[11] Testing Analytics - Vendor Summary..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/analytics/vendor-summary" -Method Get | ConvertTo-Json -Depth 5

# 12. Delete Tool
Write-Host "`n[12] Testing DELETE Tool..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "$baseUrl/api/tools/$newId" -Method Delete | ConvertTo-Json

Write-Host "`n=== All Tests Complete ===" -ForegroundColor Green
```

**Save as:** `test-api.ps1`

**Run:**
```powershell
.\test-api.ps1
```

---

## 🔍 Error Testing

### Test 404 Not Found
```powershell
# Try to get non-existent tool
try {
    Invoke-RestMethod -Uri "http://localhost:8000/api/tools/99999" -Method Get
} catch {
    $_.Exception.Response.StatusCode  # Should be 404
}
```

### Test 400 Bad Request
```powershell
# Try to create tool with invalid data
$invalidTool = @{
    name = ""  # Empty name
    vendor = "Test"
} | ConvertTo-Json

try {
    Invoke-RestMethod -Uri "http://localhost:8000/api/tools" -Method Post -Body $invalidTool -ContentType "application/json"
} catch {
    $_.Exception.Response.StatusCode  # Should be 400
}
```

---

## 📊 Performance Testing

### Load Testing with Apache Bench
```bash
# Install Apache Bench (Windows)
# Download from: https://www.apachelounge.com/download/

# Test GET endpoint
ab -n 1000 -c 10 http://localhost:8000/api/tools

# Test with authentication header
ab -n 1000 -c 10 -H "Authorization: Bearer token" http://localhost:8000/api/tools
```

### Benchmark Results Expected
- **Requests per second:** 5000+ (Rust is fast!)
- **Time per request:** < 2ms average
- **Failed requests:** 0
- **Concurrency:** 10+ simultaneous connections

---

## 🐛 Debugging Tips

### Enable Debug Logging
```powershell
$env:RUST_LOG="debug"
cargo run
```

### View Request/Response Details
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/tools" -Method Get -Verbose
```

### Test with Postman
1. Import OpenAPI spec from: `http://localhost:8000/docs`
2. Click "Export" → "OpenAPI JSON"
3. Import into Postman
4. All endpoints auto-configured

---

## ✅ Verification Checklist

Test each endpoint and check:

- [ ] **Health:** Returns status, timestamp, database status
- [ ] **GET /api/tools:** Returns tools array with metadata
- [ ] **GET /api/tools/:id:** Returns single tool or 404
- [ ] **POST /api/tools:** Creates tool and returns 201
- [ ] **PUT /api/tools/:id:** Updates tool and returns updated data
- [ ] **DELETE /api/tools/:id:** Deletes tool and returns message
- [ ] **Department Costs:** Returns aggregated costs by department
- [ ] **Expensive Tools:** Returns tools sorted by cost with efficiency
- [ ] **Tools by Category:** Returns grouped tools with insights
- [ ] **Low Usage Tools:** Returns tools below usage threshold
- [ ] **Vendor Summary:** Returns aggregated vendor data
- [ ] **Filters:** Department, status, cost range work correctly
- [ ] **Pagination:** Page and limit parameters work
- [ ] **Partial Updates:** Only provided fields are updated
- [ ] **Error Handling:** 404 and 500 errors return proper JSON

---

**Happy Testing! 🚀**

