# Internal Tools Management API - Node.js Implementation

## Quick Reference

**Base URL**: `http://localhost:8000`  
**Format**: JSON  
**Authentication**: None (internal API)

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [CRUD Endpoints](#crud-endpoints)
3. [Analytics Endpoints](#analytics-endpoints)
4. [Error Handling](#error-handling)
5. [Examples](#examples)

---

## Getting Started

### Prerequisites

- Node.js v18+ installed
- PostgreSQL database running
- Environment variables configured

### Installation

```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Run tests
npm test
```

### Health Check

```bash
curl http://localhost:8000/health
```

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-11-27T12:00:00.000Z",
  "database": "connected",
  "responseTime": "5ms"
}
```

---

## CRUD Endpoints

### 1. List Tools

**GET** `/api/tools`

Retrieve a list of tools with optional filtering and pagination.

**Query Parameters:**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `category_id` | integer | Filter by category ID | `?category_id=1` |
| `status` | string | Filter by status | `?status=active` |
| `vendor` | string | Filter by vendor name | `?vendor=GitHub` |
| `search` | string | Search in name/description | `?search=design` |
| `skip` | integer | Pagination offset (default: 0) | `?skip=20` |
| `limit` | integer | Results per page (default: 100) | `?limit=50` |

**Example Request:**

```bash
curl "http://localhost:8000/api/tools?status=active&limit=10"
```

**Response (200 OK):**

```json
[
  {
    "id": 1,
    "name": "Slack",
    "description": "Team collaboration platform",
    "vendor": "Slack Technologies",
    "website_url": null,
    "category_id": 1,
    "monthly_cost": "8.00",
    "active_users_count": 25,
    "owner_department": "Engineering",
    "status": "active",
    "created_at": "2025-11-01T09:00:00.000Z",
    "updated_at": "2025-11-01T09:00:00.000Z",
    "category": "Communication"
  }
]
```

---

### 2. Get Tool by ID

**GET** `/api/tools/:id`

Retrieve detailed information about a specific tool.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | Tool ID |

**Example Request:**

```bash
curl http://localhost:8000/api/tools/1
```

**Response (200 OK):**

```json
{
  "id": 1,
  "name": "Slack",
  "description": "Team collaboration platform",
  "vendor": "Slack Technologies",
  "website_url": "https://slack.com",
  "category_id": 1,
  "monthly_cost": "8.00",
  "active_users_count": 25,
  "owner_department": "Engineering",
  "status": "active",
  "created_at": "2025-11-01T09:00:00.000Z",
  "updated_at": "2025-11-01T09:00:00.000Z",
  "category": "Communication"
}
```

**Error Response (404 Not Found):**

```json
{
  "error": "Tool with ID 999 not found"
}
```

---

### 3. Create Tool

**POST** `/api/tools`

Create a new tool entry.

**Request Body:**

```json
{
  "name": "Linear",
  "description": "Issue tracking and project management",
  "vendor": "Linear",
  "website_url": "https://linear.app",
  "category_id": 2,
  "monthly_cost": 8.00,
  "owner_department": "Engineering",
  "status": "active"
}
```

**Required Fields:**

- `name` (string, 2-100 chars)
- `vendor` (string, max 100 chars)
- `category_id` (integer, must exist)
- `monthly_cost` (number, ≥ 0)
- `owner_department` (string, valid enum)

**Optional Fields:**

- `description` (string)
- `website_url` (string, valid URL)
- `status` (string, default: "active")

**Example Request:**

```bash
curl -X POST http://localhost:8000/api/tools \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Linear",
    "vendor": "Linear",
    "category_id": 2,
    "monthly_cost": 8.00,
    "owner_department": "Engineering"
  }'
```

**Response (201 Created):**

```json
{
  "id": 21,
  "name": "Linear",
  "description": null,
  "vendor": "Linear",
  "website_url": null,
  "category_id": 2,
  "monthly_cost": "8.00",
  "active_users_count": 0,
  "owner_department": "Engineering",
  "status": "active",
  "created_at": "2025-11-27T14:30:00.000Z",
  "updated_at": "2025-11-27T14:30:00.000Z"
}
```

**Error Response (400 Bad Request):**

```json
{
  "error": "Missing required fields"
}
```

---

### 4. Update Tool

**PUT** `/api/tools/:id`

Update an existing tool. Only provided fields will be updated.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | Tool ID |

**Request Body:**

```json
{
  "monthly_cost": 10.00,
  "status": "deprecated",
  "description": "Updated description"
}
```

**Example Request:**

```bash
curl -X PUT http://localhost:8000/api/tools/5 \
  -H "Content-Type: application/json" \
  -d '{
    "monthly_cost": 10.00,
    "status": "deprecated"
  }'
```

**Response (200 OK):**

```json
{
  "id": 5,
  "name": "Confluence",
  "description": "Team documentation",
  "vendor": "Atlassian",
  "website_url": "https://confluence.atlassian.com",
  "category_id": 2,
  "monthly_cost": "10.00",
  "active_users_count": 9,
  "owner_department": "Engineering",
  "status": "deprecated",
  "created_at": "2025-11-01T09:00:00.000Z",
  "updated_at": "2025-11-27T14:45:00.000Z"
}
```

**Error Response (404 Not Found):**

```json
{
  "error": "Tool with ID 999 not found"
}
```

---

### 5. Delete Tool

**DELETE** `/api/tools/:id`

Delete a tool permanently.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | Tool ID |

**Example Request:**

```bash
curl -X DELETE http://localhost:8000/api/tools/21
```

**Response (204 No Content)**

No body returned on successful deletion.

**Error Response (404 Not Found):**

```json
{
  "error": "Tool with ID 999 not found"
}
```

---

## Analytics Endpoints

### 1. Department Costs

**GET** `/api/analytics/department-costs`

Get tool costs aggregated by department.

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `year` | integer | Yes | Year for analysis (2020-2100) |
| `month` | integer | Yes | Month for analysis (1-12) |

**Example Request:**

```bash
curl "http://localhost:8000/api/analytics/department-costs?year=2025&month=11"
```

**Response (200 OK):**

```json
[
  {
    "department": "Engineering",
    "total_cost": "890.50",
    "tool_count": "12"
  },
  {
    "department": "Sales",
    "total_cost": "456.75",
    "tool_count": "6"
  }
]
```

---

### 2. Expensive Tools

**GET** `/api/analytics/expensive-tools`

Get the most expensive tools by monthly cost.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `limit` | integer | 10 | Number of tools to return (1-100) |

**Example Request:**

```bash
curl "http://localhost:8000/api/analytics/expensive-tools?limit=5"
```

**Response (200 OK):**

```json
[
  {
    "id": 15,
    "name": "Enterprise CRM",
    "vendor": "BigCorp",
    "monthly_cost": "199.99",
    "active_users_count": 12,
    "category_name": "Sales"
  }
]
```

---

### 3. Tools by Category

**GET** `/api/analytics/tools-by-category`

Get tool distribution across categories with total costs.

**Example Request:**

```bash
curl http://localhost:8000/api/analytics/tools-by-category
```

**Response (200 OK):**

```json
[
  {
    "category_id": 2,
    "category_name": "Development",
    "tool_count": "8",
    "total_monthly_cost": "650.00"
  },
  {
    "category_id": 1,
    "category_name": "Communication",
    "tool_count": "5",
    "total_monthly_cost": "240.50"
  }
]
```

---

### 4. Low Usage Tools

**GET** `/api/analytics/low-usage-tools`

Identify tools with low active user counts.

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `year` | integer | Yes | - | Year for analysis |
| `month` | integer | Yes | - | Month for analysis |
| `threshold` | integer | No | 5 | Maximum user count threshold |

**Example Request:**

```bash
curl "http://localhost:8000/api/analytics/low-usage-tools?year=2025&month=11&threshold=3"
```

**Response (200 OK):**

```json
[
  {
    "id": 23,
    "name": "Specialized Analytics",
    "monthly_cost": "89.99",
    "active_users_count": 2,
    "department": "Marketing",
    "vendor": "SmallVendor"
  }
]
```

---

### 5. Vendor Summary

**GET** `/api/analytics/vendor-summary`

Get aggregated statistics by vendor.

**Example Request:**

```bash
curl http://localhost:8000/api/analytics/vendor-summary
```

**Response (200 OK):**

```json
[
  {
    "vendor": "Google",
    "tools_count": "4",
    "total_monthly_cost": "234.50",
    "total_users": "67"
  },
  {
    "vendor": "Microsoft",
    "tools_count": "3",
    "total_monthly_cost": "180.00",
    "total_users": "45"
  }
]
```

---

## Error Handling

The API uses standard HTTP status codes and returns errors in JSON format.

### Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 204 | No Content (successful deletion) |
| 400 | Bad Request (validation error) |
| 404 | Not Found |
| 500 | Internal Server Error |

### Error Response Format

```json
{
  "error": "Error message describing what went wrong"
}
```

### Common Errors

**Missing Required Fields (400):**

```json
{
  "error": "Missing required fields"
}
```

**Invalid Category (404):**

```json
{
  "error": "Referenced resource does not exist"
}
```

**Resource Not Found (404):**

```json
{
  "error": "Tool with ID 999 not found"
}
```

**Database Connection Error (503):**

```json
{
  "status": "unhealthy",
  "database": "disconnected",
  "error": "Connection timeout"
}
```

---

## Examples

### Complete CRUD Workflow

```bash
# 1. List all tools
curl http://localhost:8000/api/tools

# 2. Create a new tool
curl -X POST http://localhost:8000/api/tools \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Figma",
    "vendor": "Figma",
    "category_id": 3,
    "monthly_cost": 12.00,
    "owner_department": "Design"
  }'

# 3. Get the created tool (assuming ID is 22)
curl http://localhost:8000/api/tools/22

# 4. Update the tool
curl -X PUT http://localhost:8000/api/tools/22 \
  -H "Content-Type: application/json" \
  -d '{"monthly_cost": 15.00}'

# 5. Delete the tool
curl -X DELETE http://localhost:8000/api/tools/22
```

### Analytics Workflow

```bash
# Get department costs
curl "http://localhost:8000/api/analytics/department-costs?year=2025&month=11"

# Get top 10 expensive tools
curl "http://localhost:8000/api/analytics/expensive-tools?limit=10"

# Get category distribution
curl http://localhost:8000/api/analytics/tools-by-category

# Find low usage tools
curl "http://localhost:8000/api/analytics/low-usage-tools?year=2025&month=11&threshold=5"

# Get vendor summary
curl http://localhost:8000/api/analytics/vendor-summary
```

### PowerShell Examples

```powershell
# List tools
Invoke-RestMethod -Uri "http://localhost:8000/api/tools" | ConvertTo-Json

# Create tool
$body = @{
    name = "New Tool"
    vendor = "Vendor Name"
    category_id = 1
    monthly_cost = 25.00
    owner_department = "Engineering"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/tools" `
    -Method Post `
    -Body $body `
    -ContentType "application/json"

# Get analytics
Invoke-RestMethod -Uri "http://localhost:8000/api/analytics/expensive-tools?limit=5" | ConvertTo-Json -Depth 3
```

---

## Testing

Run the comprehensive test suite:

```bash
# Run all tests with coverage
npm test

# Run tests in watch mode
npm run test:watch

# Run tests without coverage (faster)
npm run test:quick
```

**Test Coverage:**

- 43 comprehensive tests
- All CRUD operations tested
- All analytics endpoints tested
- Error scenarios covered
- Edge cases validated

---

## Best Practices

### Pagination

Always use pagination for large datasets:

```bash
# Get first page (100 items)
curl "http://localhost:8000/api/tools?limit=100&skip=0"

# Get second page
curl "http://localhost:8000/api/tools?limit=100&skip=100"
```

### Filtering

Combine multiple filters for precise results:

```bash
curl "http://localhost:8000/api/tools?status=active&vendor=Google&limit=20"
```

### Error Handling

Always check HTTP status codes and handle errors appropriately:

```javascript
try {
  const response = await fetch('http://localhost:8000/api/tools/123');
  if (!response.ok) {
    const error = await response.json();
    console.error('API Error:', error.error);
  }
  const data = await response.json();
} catch (error) {
  console.error('Network Error:', error);
}
```

---

## Additional Resources

- **GitHub Repository**: [API_Internal_Tools_Management](https://github.com/FCHEHIDI/API_Internal_Tools_Management)
- **Branch**: `feature/nodejs-express`
- **README**: See `README_NODEJS.md` for setup instructions
- **Tests**: See `tests/` directory for test examples

---

## Support

For issues or questions:
1. Check the test files for usage examples
2. Review error messages carefully
3. Ensure database is running and accessible
4. Verify environment variables are set correctly

---

*Last Updated: November 27, 2025*
