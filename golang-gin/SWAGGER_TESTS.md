# Swagger API Tests - Go/Gin Implementation

## 🎯 Overview

This document provides comprehensive test cases for all API endpoints using the Swagger UI and curl commands.

**Swagger UI:** http://localhost:8000/docs/index.html
**Base URL:** http://localhost:8000

---

## 🏥 Health Check

### GET /api/health

**Purpose:** Verify server and database connectivity

#### Swagger Test Steps:
1. Navigate to http://localhost:8000/docs/index.html
2. Expand "GET /api/health"
3. Click "Try it out"
4. Click "Execute"

#### Expected Response (200 OK):
```json
{
  "status": "healthy",
  "timestamp": "2025-11-27T16:14:17+01:00",
  "database": "connected",
  "responseTime": 0
}
```

#### PowerShell Test:
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method Get | ConvertTo-Json
```

#### Curl Test:
```bash
curl -X GET "http://localhost:8000/api/health" -H "accept: application/json"
```

---

## 🛠️ CRUD Operations

### 1. GET /api/tools - List All Tools

**Purpose:** Retrieve paginated list of tools with optional filters

#### Test Case 1.1: Get all tools (default pagination)

**Swagger Steps:**
1. Expand "GET /api/tools"
2. Click "Try it out"
3. Leave all parameters empty
4. Click "Execute"

**Expected Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Slack",
      "description": "Team communication platform",
      "vendor": "Slack Technologies",
      "website_url": "https://slack.com",
      "category_id": 1,
      "monthly_cost": 8,
      "active_users_count": 50,
      "owner_department": "Engineering",
      "status": "active",
      "created_at": "2025-11-27T11:14:00.756108Z",
      "updated_at": "2025-11-27T11:14:00.756108Z",
      "category": "Communication"
    }
  ],
  "total": 15,
  "filtered": 15,
  "filters_applied": {}
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools" -Method Get | ConvertTo-Json -Depth 5
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/tools" -H "accept: application/json"
```

#### Test Case 1.2: Filter by status

**Parameters:**
- `status`: "active"

**Expected:** Only active tools returned

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?status=active" -Method Get | ConvertTo-Json -Depth 5
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/tools?status=active" -H "accept: application/json"
```

#### Test Case 1.3: Filter by category

**Parameters:**
- `category_id`: 1

**Expected:** Only tools in category 1 returned

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?category_id=1" -Method Get | ConvertTo-Json -Depth 5
```

#### Test Case 1.4: Search by name

**Parameters:**
- `search`: "Slack"

**Expected:** Tools with "Slack" in name

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?search=Slack" -Method Get | ConvertTo-Json -Depth 5
```

#### Test Case 1.5: Pagination

**Parameters:**
- `limit`: 5
- `skip`: 0

**Expected:** First 5 tools

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?limit=5&skip=0" -Method Get | ConvertTo-Json -Depth 5
```

#### Test Case 1.6: Combined filters

**Parameters:**
- `status`: "active"
- `category_id`: 2
- `limit`: 10

**Expected:** First 10 active tools in category 2

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools?status=active&category_id=2&limit=10" -Method Get | ConvertTo-Json -Depth 5
```

---

### 2. GET /api/tools/:id - Get Single Tool

**Purpose:** Retrieve detailed information about a specific tool

#### Test Case 2.1: Get existing tool

**Swagger Steps:**
1. Expand "GET /api/tools/{id}"
2. Click "Try it out"
3. Enter `id`: 2
4. Click "Execute"

**Expected Response (200 OK):**
```json
{
  "id": 2,
  "name": "Zoom",
  "description": "Video conferencing and webinars",
  "vendor": "Zoom Video Communications",
  "website_url": "https://zoom.us",
  "category_id": 1,
  "monthly_cost": 14.99,
  "active_users_count": 25,
  "owner_department": "Operations",
  "status": "active",
  "created_at": "2025-11-27T11:14:00.756108Z",
  "updated_at": "2025-11-27T11:14:00.756108Z",
  "category": "Communication"
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools/2" -Method Get | ConvertTo-Json -Depth 3
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/tools/2" -H "accept: application/json"
```

#### Test Case 2.2: Get non-existent tool

**Parameters:**
- `id`: 99999

**Expected Response (404 Not Found):**
```json
{
  "error": "Tool not found",
  "message": "Tool with ID 99999 does not exist"
}
```

**PowerShell:**
```powershell
try {
    Invoke-RestMethod -Uri "http://localhost:8000/api/tools/99999" -Method Get
} catch {
    $_.ErrorDetails.Message | ConvertFrom-Json | ConvertTo-Json
}
```

---

### 3. POST /api/tools - Create New Tool

**Purpose:** Add a new tool to the system

#### Test Case 3.1: Create valid tool

**Swagger Steps:**
1. Expand "POST /api/tools"
2. Click "Try it out"
3. Enter request body (see below)
4. Click "Execute"

**Request Body:**
```json
{
  "name": "Notion",
  "description": "All-in-one workspace",
  "vendor": "Notion Labs",
  "website_url": "https://notion.so",
  "category_id": 4,
  "monthly_cost": 10,
  "active_users_count": 30,
  "owner_department": "Engineering",
  "status": "active"
}
```

**Expected Response (201 Created):**
```json
{
  "id": 16,
  "name": "Notion",
  "description": "All-in-one workspace",
  "vendor": "Notion Labs",
  "website_url": "https://notion.so",
  "category_id": 4,
  "monthly_cost": 10,
  "active_users_count": 30,
  "owner_department": "Engineering",
  "status": "active",
  "created_at": "2025-11-27T16:30:00Z",
  "updated_at": "2025-11-27T16:30:00Z",
  "category": "Productivity"
}
```

**PowerShell:**
```powershell
$body = @{
    name = "Notion"
    description = "All-in-one workspace"
    vendor = "Notion Labs"
    website_url = "https://notion.so"
    category_id = 4
    monthly_cost = 10
    active_users_count = 30
    owner_department = "Engineering"
    status = "active"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools" -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 3
```

**Curl:**
```bash
curl -X POST "http://localhost:8000/api/tools" \
  -H "accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Notion",
    "description": "All-in-one workspace",
    "vendor": "Notion Labs",
    "website_url": "https://notion.so",
    "category_id": 4,
    "monthly_cost": 10,
    "active_users_count": 30,
    "owner_department": "Engineering",
    "status": "active"
  }'
```

#### Test Case 3.2: Create with missing required fields

**Request Body:**
```json
{
  "name": "Test Tool"
}
```

**Expected Response (400 Bad Request):**
```json
{
  "error": "Invalid request data"
}
```

#### Test Case 3.3: Create with invalid department

**Request Body:**
```json
{
  "name": "Test Tool",
  "description": "Test",
  "vendor": "Test Inc",
  "category_id": 1,
  "monthly_cost": 10,
  "owner_department": "InvalidDept",
  "status": "active"
}
```

**Expected Response (400 Bad Request):**
```json
{
  "error": "Invalid request data"
}
```

#### Test Case 3.4: Create with invalid status

**Request Body:**
```json
{
  "name": "Test Tool",
  "description": "Test",
  "vendor": "Test Inc",
  "category_id": 1,
  "monthly_cost": 10,
  "owner_department": "Engineering",
  "status": "invalid_status"
}
```

**Expected Response (400 Bad Request)**

---

### 4. PUT /api/tools/:id - Update Tool

**Purpose:** Update an existing tool (partial updates supported)

#### Test Case 4.1: Update tool name and cost

**Swagger Steps:**
1. Expand "PUT /api/tools/{id}"
2. Click "Try it out"
3. Enter `id`: 2
4. Enter request body
5. Click "Execute"

**Request Body:**
```json
{
  "name": "Zoom Pro",
  "monthly_cost": 19.99
}
```

**Expected Response (200 OK):**
```json
{
  "id": 2,
  "name": "Zoom Pro",
  "monthly_cost": 19.99,
  "message": "Tool updated successfully"
}
```

**PowerShell:**
```powershell
$body = @{
    name = "Zoom Pro"
    monthly_cost = 19.99
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools/2" -Method Put -Body $body -ContentType "application/json" | ConvertTo-Json
```

**Curl:**
```bash
curl -X PUT "http://localhost:8000/api/tools/2" \
  -H "accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Zoom Pro",
    "monthly_cost": 19.99
  }'
```

#### Test Case 4.2: Update only status

**Request Body:**
```json
{
  "status": "deprecated"
}
```

**Expected Response (200 OK)**

**PowerShell:**
```powershell
$body = @{
    status = "deprecated"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools/3" -Method Put -Body $body -ContentType "application/json" | ConvertTo-Json
```

#### Test Case 4.3: Update non-existent tool

**Parameters:**
- `id`: 99999

**Expected Response (404 Not Found):**
```json
{
  "error": "Tool not found"
}
```

#### Test Case 4.4: Update with invalid data

**Request Body:**
```json
{
  "monthly_cost": -10
}
```

**Expected Response (400 Bad Request)**

---

### 5. DELETE /api/tools/:id - Delete Tool

**Purpose:** Remove a tool from the system

#### Test Case 5.1: Delete existing tool

**Swagger Steps:**
1. Expand "DELETE /api/tools/{id}"
2. Click "Try it out"
3. Enter `id`: 15
4. Click "Execute"

**Expected Response (200 OK):**
```json
{
  "message": "Tool deleted successfully"
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tools/15" -Method Delete | ConvertTo-Json
```

**Curl:**
```bash
curl -X DELETE "http://localhost:8000/api/tools/15" -H "accept: application/json"
```

#### Test Case 5.2: Delete non-existent tool

**Parameters:**
- `id`: 99999

**Expected Response (404 Not Found):**
```json
{
  "error": "Tool not found"
}
```

**PowerShell:**
```powershell
try {
    Invoke-RestMethod -Uri "http://localhost:8000/api/tools/99999" -Method Delete
} catch {
    $_.ErrorDetails.Message | ConvertFrom-Json | ConvertTo-Json
}
```

---

## 📊 Analytics Endpoints

### 6. GET /api/analytics/department-costs

**Purpose:** Get cost breakdown by department

#### Test Case 6.1: Get all department costs

**Swagger Steps:**
1. Expand "GET /api/analytics/department-costs"
2. Click "Try it out"
3. Click "Execute"

**Expected Response (200 OK):**
```json
{
  "total_cost": 500,
  "departments": [
    {
      "department": "Engineering",
      "total_cost": 200,
      "tool_count": 8,
      "percentage": 40
    },
    {
      "department": "Sales",
      "total_cost": 150,
      "tool_count": 4,
      "percentage": 30
    }
  ]
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/department-costs" -Method Get | ConvertTo-Json -Depth 3
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/analytics/department-costs" -H "accept: application/json"
```

---

### 7. GET /api/analytics/expensive-tools

**Purpose:** Get most expensive tools with efficiency ratings

#### Test Case 7.1: Get top 5 expensive tools

**Swagger Steps:**
1. Expand "GET /api/analytics/expensive-tools"
2. Click "Try it out"
3. Enter `limit`: 5
4. Click "Execute"

**Parameters:**
- `limit`: 5

**Expected Response (200 OK):**
```json
{
  "tools": [
    {
      "id": 9,
      "name": "Adobe Creative Cloud",
      "category": "Design",
      "monthly_cost": 79.99,
      "active_users_count": 5,
      "efficiency_rating": 16,
      "department": "Design"
    }
  ]
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/expensive-tools?limit=5" -Method Get | ConvertTo-Json -Depth 3
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/analytics/expensive-tools?limit=5" -H "accept: application/json"
```

#### Test Case 7.2: Get top 10 expensive tools

**Parameters:**
- `limit`: 10

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/expensive-tools?limit=10" -Method Get | ConvertTo-Json -Depth 3
```

---

### 8. GET /api/analytics/tools-by-category

**Purpose:** Get tools grouped by category with statistics

#### Test Case 8.1: Get all categories with tools

**Swagger Steps:**
1. Expand "GET /api/analytics/tools-by-category"
2. Click "Try it out"
3. Click "Execute"

**Expected Response (200 OK):**
```json
{
  "categories": [
    {
      "category_id": 1,
      "category_name": "Communication",
      "tool_count": 3,
      "average_cost": 12.33,
      "total_cost": 37,
      "tools": [
        {
          "id": 1,
          "name": "Slack",
          "monthly_cost": 8,
          "status": "active"
        }
      ],
      "insights": {
        "most_expensive": "Zoom",
        "least_expensive": "Slack",
        "avg_users": 30
      }
    }
  ]
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/tools-by-category" -Method Get | ConvertTo-Json -Depth 5
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/analytics/tools-by-category" -H "accept: application/json"
```

---

### 9. GET /api/analytics/low-usage-tools

**Purpose:** Identify underutilized tools

#### Test Case 9.1: Get tools with less than 10 users

**Swagger Steps:**
1. Expand "GET /api/analytics/low-usage-tools"
2. Click "Try it out"
3. Enter `threshold`: 10
4. Click "Execute"

**Parameters:**
- `threshold`: 10 (default)

**Expected Response (200 OK):**
```json
{
  "threshold": 10,
  "tools": [
    {
      "id": 9,
      "name": "Adobe Creative Cloud",
      "category": "Design",
      "monthly_cost": 79.99,
      "active_users_count": 5,
      "efficiency_rating": 16,
      "department": "Design",
      "warning_level": "critical"
    }
  ],
  "total_tools": 3,
  "total_wasted_cost": 150
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/low-usage-tools?threshold=10" -Method Get | ConvertTo-Json -Depth 3
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/analytics/low-usage-tools?threshold=10" -H "accept: application/json"
```

#### Test Case 9.2: Get tools with less than 5 users

**Parameters:**
- `threshold`: 5

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/low-usage-tools?threshold=5" -Method Get | ConvertTo-Json -Depth 3
```

---

### 10. GET /api/analytics/vendor-summary

**Purpose:** Get comprehensive vendor statistics

#### Test Case 10.1: Get all vendor summaries

**Swagger Steps:**
1. Expand "GET /api/analytics/vendor-summary"
2. Click "Try it out"
3. Click "Execute"

**Expected Response (200 OK):**
```json
{
  "vendors": [
    {
      "vendor": "Atlassian",
      "tool_count": 2,
      "total_cost": 13,
      "average_cost": 6.5,
      "departments": "Engineering, Operations",
      "tools": [
        {
          "name": "Jira",
          "monthly_cost": 7.5,
          "department": "Engineering"
        },
        {
          "name": "Confluence",
          "monthly_cost": 5.5,
          "department": "Engineering"
        }
      ]
    }
  ]
}
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/vendor-summary" -Method Get | ConvertTo-Json -Depth 5
```

**Curl:**
```bash
curl -X GET "http://localhost:8000/api/analytics/vendor-summary" -H "accept: application/json"
```

---

## 🧪 Comprehensive Test Script

### PowerShell Test Suite

Save as `test-all-endpoints.ps1`:

```powershell
# Go/Gin API Test Suite
$baseUrl = "http://localhost:8000"

Write-Host "🧪 Starting Go/Gin API Tests..." -ForegroundColor Cyan

# Test 1: Health Check
Write-Host "`n✅ Test 1: Health Check" -ForegroundColor Green
Invoke-RestMethod "$baseUrl/api/health" | ConvertTo-Json

# Test 2: Get All Tools
Write-Host "`n✅ Test 2: Get All Tools" -ForegroundColor Green
$tools = Invoke-RestMethod "$baseUrl/api/tools"
Write-Host "Total tools: $($tools.total)"

# Test 3: Get Single Tool
Write-Host "`n✅ Test 3: Get Single Tool (ID: 2)" -ForegroundColor Green
Invoke-RestMethod "$baseUrl/api/tools/2" | ConvertTo-Json

# Test 4: Filter by Status
Write-Host "`n✅ Test 4: Filter by Status (active)" -ForegroundColor Green
$activeTools = Invoke-RestMethod "$baseUrl/api/tools?status=active"
Write-Host "Active tools: $($activeTools.filtered)"

# Test 5: Search Tools
Write-Host "`n✅ Test 5: Search Tools (Slack)" -ForegroundColor Green
$searchResults = Invoke-RestMethod "$baseUrl/api/tools?search=Slack"
Write-Host "Search results: $($searchResults.filtered)"

# Test 6: Department Costs
Write-Host "`n✅ Test 6: Department Costs" -ForegroundColor Green
Invoke-RestMethod "$baseUrl/api/analytics/department-costs" | ConvertTo-Json -Depth 2

# Test 7: Expensive Tools
Write-Host "`n✅ Test 7: Top 3 Expensive Tools" -ForegroundColor Green
Invoke-RestMethod "$baseUrl/api/analytics/expensive-tools?limit=3" | ConvertTo-Json -Depth 3

# Test 8: Tools by Category
Write-Host "`n✅ Test 8: Tools by Category" -ForegroundColor Green
$categories = Invoke-RestMethod "$baseUrl/api/analytics/tools-by-category"
Write-Host "Categories: $($categories.categories.Count)"

# Test 9: Low Usage Tools
Write-Host "`n✅ Test 9: Low Usage Tools" -ForegroundColor Green
Invoke-RestMethod "$baseUrl/api/analytics/low-usage-tools?threshold=10" | ConvertTo-Json -Depth 2

# Test 10: Vendor Summary
Write-Host "`n✅ Test 10: Vendor Summary" -ForegroundColor Green
$vendors = Invoke-RestMethod "$baseUrl/api/analytics/vendor-summary"
Write-Host "Vendors: $($vendors.vendors.Count)"

Write-Host "`n🎉 All tests completed!" -ForegroundColor Cyan
```

Run with:
```powershell
.\test-all-endpoints.ps1
```

---

## 📝 Test Results Checklist

- [ ] Health check returns 200 with database connected
- [ ] GET /api/tools returns paginated list
- [ ] GET /api/tools filters work (status, category, vendor, search)
- [ ] GET /api/tools/:id returns single tool
- [ ] GET /api/tools/:id returns 404 for invalid ID
- [ ] POST /api/tools creates new tool with 201
- [ ] POST /api/tools validates required fields
- [ ] POST /api/tools validates enum values (status, department)
- [ ] PUT /api/tools/:id updates tool partially
- [ ] PUT /api/tools/:id returns 404 for invalid ID
- [ ] DELETE /api/tools/:id deletes tool
- [ ] DELETE /api/tools/:id returns 404 for invalid ID
- [ ] GET /api/analytics/department-costs returns aggregated data
- [ ] GET /api/analytics/expensive-tools returns sorted list
- [ ] GET /api/analytics/expensive-tools respects limit parameter
- [ ] GET /api/analytics/tools-by-category groups correctly
- [ ] GET /api/analytics/low-usage-tools filters by threshold
- [ ] GET /api/analytics/low-usage-tools assigns warning levels
- [ ] GET /api/analytics/vendor-summary aggregates by vendor
- [ ] All endpoints return proper JSON structure
- [ ] All endpoints handle errors gracefully
- [ ] Swagger UI loads successfully
- [ ] Swagger UI "Try it out" works for all endpoints

---

## 🎯 Performance Benchmarks

Expected response times (approximate):
- Health check: < 5ms
- GET /api/tools: < 50ms
- GET /api/tools/:id: < 10ms
- POST /api/tools: < 20ms
- PUT /api/tools/:id: < 20ms
- DELETE /api/tools/:id: < 15ms
- Analytics endpoints: < 100ms

---

**Documentation Version:** 1.0
**Last Updated:** November 27, 2025
**Status:** ✅ Ready for Testing
