# API Usage Guide - Internal Tools Management API

Complete guide for frontend developers to integrate with the FastAPI backend.

## Table of Contents
- [Base URL & Authentication](#base-url--authentication)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Health Check](#health-check)
- [Tools Endpoints](#tools-endpoints)
- [Analytics Endpoints](#analytics-endpoints)
- [Best Practices](#best-practices)

## Base URL & Authentication

**Development**: `http://127.0.0.1:8000`  
**Production**: `https://your-domain.com`

**Authentication**: Currently no authentication required (to be implemented)

## Response Format

All successful responses return JSON with appropriate HTTP status codes:

- `200 OK` - Successful GET/PUT requests
- `201 Created` - Successful POST requests
- `204 No Content` - Successful DELETE requests
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

## Error Handling

Error responses follow this format:

```json
{
  "detail": "Error message describing what went wrong"
}
```

For validation errors (422):

```json
{
  "detail": [
    {
      "loc": ["body", "monthly_cost"],
      "msg": "field required",
      "type": "value_error.missing"
    }
  ]
}
```

## Health Check

### GET /health

Check API and database connectivity status.

**Request:**
```bash
curl -X GET "http://127.0.0.1:8000/health"
```

**Response:** [See health-check.json](api-examples/health-check.json)

```json
{
  "status": "healthy",
  "timestamp": "2024-11-27T10:30:45.123456",
  "database": "connected",
  "version": "1.0.0"
}
```

---

## Tools Endpoints

### GET /api/tools

List all tools with optional filtering and pagination.

**Query Parameters:**
- `category_id` (integer, optional) - Filter by category ID
- `status` (string, optional) - Filter by status: `active`, `inactive`, `trial`
- `vendor` (string, optional) - Filter by vendor name (partial match)
- `search` (string, optional) - Search in name or description
- `skip` (integer, optional, default: 0) - Pagination offset
- `limit` (integer, optional, default: 100, max: 500) - Max results

**Examples:**

1. **Get all tools**
```bash
curl -X GET "http://127.0.0.1:8000/api/tools"
```

2. **Filter by status**
```bash
curl -X GET "http://127.0.0.1:8000/api/tools?status=active"
```

3. **Search and pagination**
```bash
curl -X GET "http://127.0.0.1:8000/api/tools?search=design&skip=0&limit=10"
```

4. **Multiple filters**
```bash
curl -X GET "http://127.0.0.1:8000/api/tools?category_id=1&status=active&limit=20"
```

**Response:** [See tools-list.json](api-examples/tools-list.json)

```json
[
  {
    "id": 1,
    "name": "GitHub Enterprise",
    "description": "...",
    "vendor": "GitHub",
    "monthly_cost": 21.0,
    "status": "active",
    "category_id": 1,
    "active_users_count": 50,
    "created_at": "2024-01-15T09:00:00",
    "updated_at": "2024-11-20T14:30:00",
    "category": {
      "id": 1,
      "name": "Development Tools",
      "description": "..."
    }
  }
]
```

**Frontend Implementation (JavaScript/TypeScript):**

```typescript
interface Tool {
  id: number;
  name: string;
  description: string;
  vendor: string;
  monthly_cost: number;
  status: 'active' | 'inactive' | 'trial';
  category_id: number;
  active_users_count: number;
  created_at: string;
  updated_at: string;
  category: {
    id: number;
    name: string;
    description: string;
  };
}

async function fetchTools(filters?: {
  categoryId?: number;
  status?: string;
  vendor?: string;
  search?: string;
  skip?: number;
  limit?: number;
}): Promise<Tool[]> {
  const params = new URLSearchParams();
  if (filters?.categoryId) params.append('category_id', filters.categoryId.toString());
  if (filters?.status) params.append('status', filters.status);
  if (filters?.vendor) params.append('vendor', filters.vendor);
  if (filters?.search) params.append('search', filters.search);
  if (filters?.skip) params.append('skip', filters.skip.toString());
  if (filters?.limit) params.append('limit', filters.limit.toString());

  const response = await fetch(`http://127.0.0.1:8000/api/tools?${params}`);
  if (!response.ok) throw new Error('Failed to fetch tools');
  return response.json();
}
```

---

### GET /api/tools/{id}

Get a specific tool by ID.

**Request:**
```bash
curl -X GET "http://127.0.0.1:8000/api/tools/1"
```

**Response:** [See tool-detail.json](api-examples/tool-detail.json)

**Frontend Implementation:**

```typescript
async function fetchToolById(id: number): Promise<Tool> {
  const response = await fetch(`http://127.0.0.1:8000/api/tools/${id}`);
  if (!response.ok) {
    if (response.status === 404) throw new Error('Tool not found');
    throw new Error('Failed to fetch tool');
  }
  return response.json();
}
```

---

### POST /api/tools

Create a new tool.

**Request Body:** [See tool-create-request.json](api-examples/tool-create-request.json)

```json
{
  "name": "New Tool Name",
  "description": "Detailed description",
  "vendor": "Vendor Name",
  "monthly_cost": 99.99,
  "status": "active",
  "category_id": 1
}
```

**Request:**
```bash
curl -X POST "http://127.0.0.1:8000/api/tools" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Tool",
    "description": "Description",
    "vendor": "Vendor",
    "monthly_cost": 99.99,
    "status": "active",
    "category_id": 1
  }'
```

**Response (201):** [See tool-create-response.json](api-examples/tool-create-response.json)

**Frontend Implementation:**

```typescript
interface CreateToolData {
  name: string;
  description: string;
  vendor: string;
  monthly_cost: number;
  status: 'active' | 'inactive' | 'trial';
  category_id: number;
}

async function createTool(data: CreateToolData): Promise<Tool> {
  const response = await fetch('http://127.0.0.1:8000/api/tools', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  });
  
  if (!response.ok) {
    if (response.status === 422) {
      const error = await response.json();
      throw new Error(`Validation error: ${JSON.stringify(error.detail)}`);
    }
    throw new Error('Failed to create tool');
  }
  
  return response.json();
}
```

---

### PUT /api/tools/{id}

Update an existing tool (partial update supported).

**Request Body:** [See tool-update-request.json](api-examples/tool-update-request.json)

```json
{
  "monthly_cost": 149.99,
  "status": "trial"
}
```

**Request:**
```bash
curl -X PUT "http://127.0.0.1:8000/api/tools/1" \
  -H "Content-Type: application/json" \
  -d '{
    "monthly_cost": 149.99,
    "status": "trial"
  }'
```

**Response (200):** Updated tool object

**Frontend Implementation:**

```typescript
interface UpdateToolData {
  name?: string;
  description?: string;
  vendor?: string;
  monthly_cost?: number;
  status?: 'active' | 'inactive' | 'trial';
  category_id?: number;
}

async function updateTool(id: number, data: UpdateToolData): Promise<Tool> {
  const response = await fetch(`http://127.0.0.1:8000/api/tools/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  });
  
  if (!response.ok) {
    if (response.status === 404) throw new Error('Tool not found');
    if (response.status === 422) {
      const error = await response.json();
      throw new Error(`Validation error: ${JSON.stringify(error.detail)}`);
    }
    throw new Error('Failed to update tool');
  }
  
  return response.json();
}
```

---

### DELETE /api/tools/{id}

Delete a tool.

**Request:**
```bash
curl -X DELETE "http://127.0.0.1:8000/api/tools/1"
```

**Response (204):** No content

**Frontend Implementation:**

```typescript
async function deleteTool(id: number): Promise<void> {
  const response = await fetch(`http://127.0.0.1:8000/api/tools/${id}`, {
    method: 'DELETE',
  });
  
  if (!response.ok) {
    if (response.status === 404) throw new Error('Tool not found');
    throw new Error('Failed to delete tool');
  }
}
```

---

## Analytics Endpoints

### GET /api/analytics/department-costs

Get tool costs aggregated by department for a specific month.

**Query Parameters:**
- `year` (integer, required) - Year for analysis (2020-2100)
- `month` (integer, required) - Month for analysis (1-12)

**Request:**
```bash
curl -X GET "http://127.0.0.1:8000/api/analytics/department-costs?year=2024&month=11"
```

**Response:** [See analytics-department-costs.json](api-examples/analytics-department-costs.json)

```json
[
  {
    "department": "Engineering",
    "total_cost": 15420.50,
    "user_count": 45
  }
]
```

**Frontend Implementation:**

```typescript
interface DepartmentCost {
  department: string;
  total_cost: number;
  user_count: number;
}

async function fetchDepartmentCosts(year: number, month: number): Promise<DepartmentCost[]> {
  const response = await fetch(
    `http://127.0.0.1:8000/api/analytics/department-costs?year=${year}&month=${month}`
  );
  if (!response.ok) throw new Error('Failed to fetch department costs');
  return response.json();
}

// Usage in React/Vue component for chart
const costs = await fetchDepartmentCosts(2024, 11);
const chartData = costs.map(item => ({
  label: item.department,
  value: item.total_cost,
  users: item.user_count
}));
```

---

### GET /api/analytics/expensive-tools

Get the top N most expensive tools by monthly cost.

**Query Parameters:**
- `limit` (integer, optional, default: 10, max: 100) - Number of tools to return

**Request:**
```bash
curl -X GET "http://127.0.0.1:8000/api/analytics/expensive-tools?limit=10"
```

**Response:** [See analytics-expensive-tools.json](api-examples/analytics-expensive-tools.json)

```json
[
  {
    "id": 15,
    "name": "Adobe Creative Cloud All Apps",
    "vendor": "Adobe",
    "monthly_cost": 89.99,
    "active_users_count": 12,
    "category_name": "Design Tools"
  }
]
```

**Frontend Implementation:**

```typescript
interface ExpensiveTool {
  id: number;
  name: string;
  vendor: string;
  monthly_cost: number;
  active_users_count: number;
  category_name: string | null;
}

async function fetchExpensiveTools(limit: number = 10): Promise<ExpensiveTool[]> {
  const response = await fetch(
    `http://127.0.0.1:8000/api/analytics/expensive-tools?limit=${limit}`
  );
  if (!response.ok) throw new Error('Failed to fetch expensive tools');
  return response.json();
}
```

---

### GET /api/analytics/tools-by-category

Get tool distribution and costs across categories.

**Request:**
```bash
curl -X GET "http://127.0.0.1:8000/api/analytics/tools-by-category"
```

**Response:** [See analytics-tools-by-category.json](api-examples/analytics-tools-by-category.json)

```json
[
  {
    "category_id": 1,
    "category_name": "Development Tools",
    "tool_count": 8,
    "total_monthly_cost": 245.90
  }
]
```

**Frontend Implementation:**

```typescript
interface CategoryStats {
  category_id: number;
  category_name: string;
  tool_count: number;
  total_monthly_cost: number;
}

async function fetchToolsByCategory(): Promise<CategoryStats[]> {
  const response = await fetch(
    'http://127.0.0.1:8000/api/analytics/tools-by-category'
  );
  if (!response.ok) throw new Error('Failed to fetch tools by category');
  return response.json();
}

// Great for pie charts or donut charts
const categories = await fetchToolsByCategory();
const pieChartData = categories.map(cat => ({
  name: cat.category_name,
  value: cat.total_monthly_cost,
  count: cat.tool_count
}));
```

---

### GET /api/analytics/low-usage-tools

Identify tools with low usage for cost optimization.

**Query Parameters:**
- `year` (integer, required) - Year for analysis
- `month` (integer, required) - Month for analysis
- `threshold` (integer, optional, default: 5, max: 100) - Max usage count threshold

**Request:**
```bash
curl -X GET "http://127.0.0.1:8000/api/analytics/low-usage-tools?year=2024&month=11&threshold=5"
```

**Response:** [See analytics-low-usage-tools.json](api-examples/analytics-low-usage-tools.json)

```json
[
  {
    "id": 15,
    "name": "Adobe Creative Cloud All Apps",
    "vendor": "Adobe",
    "monthly_cost": 89.99,
    "active_users_count": 12,
    "usage_count": 3,
    "cost_per_usage": 29.996666666666666
  }
]
```

**Frontend Implementation:**

```typescript
interface LowUsageTool {
  id: number;
  name: string;
  vendor: string;
  monthly_cost: number;
  active_users_count: number;
  usage_count: number;
  cost_per_usage: number | null;
}

async function fetchLowUsageTools(
  year: number,
  month: number,
  threshold: number = 5
): Promise<LowUsageTool[]> {
  const response = await fetch(
    `http://127.0.0.1:8000/api/analytics/low-usage-tools?year=${year}&month=${month}&threshold=${threshold}`
  );
  if (!response.ok) throw new Error('Failed to fetch low usage tools');
  return response.json();
}

// Display with warning badges
const lowUsage = await fetchLowUsageTools(2024, 11, 5);
const highCostLowUsage = lowUsage.filter(tool => tool.monthly_cost > 50);
```

---

### GET /api/analytics/vendor-summary

Get total spending and tool count by vendor.

**Request:**
```bash
curl -X GET "http://127.0.0.1:8000/api/analytics/vendor-summary"
```

**Response:** [See analytics-vendor-summary.json](api-examples/analytics-vendor-summary.json)

```json
[
  {
    "vendor": "Adobe",
    "tool_count": 2,
    "total_monthly_cost": 119.98,
    "total_users": 25,
    "average_cost_per_tool": 59.99
  }
]
```

**Frontend Implementation:**

```typescript
interface VendorSummary {
  vendor: string;
  tool_count: number;
  total_monthly_cost: number;
  total_users: number;
  average_cost_per_tool: number;
}

async function fetchVendorSummary(): Promise<VendorSummary[]> {
  const response = await fetch(
    'http://127.0.0.1:8000/api/analytics/vendor-summary'
  );
  if (!response.ok) throw new Error('Failed to fetch vendor summary');
  return response.json();
}

// Great for vendor comparison table
const vendors = await fetchVendorSummary();
const topVendors = vendors.slice(0, 5);
```

---

## Best Practices

### 1. Error Handling

Always wrap API calls in try-catch blocks:

```typescript
try {
  const tools = await fetchTools({ status: 'active' });
  setTools(tools);
} catch (error) {
  console.error('Failed to fetch tools:', error);
  setError('Unable to load tools. Please try again.');
}
```

### 2. Loading States

Show loading indicators during API calls:

```typescript
const [isLoading, setIsLoading] = useState(false);
const [error, setError] = useState<string | null>(null);

async function loadTools() {
  setIsLoading(true);
  setError(null);
  try {
    const data = await fetchTools();
    setTools(data);
  } catch (err) {
    setError(err.message);
  } finally {
    setIsLoading(false);
  }
}
```

### 3. Debouncing Search

Debounce search queries to avoid excessive API calls:

```typescript
import { debounce } from 'lodash';

const debouncedSearch = debounce(async (searchTerm: string) => {
  const results = await fetchTools({ search: searchTerm });
  setSearchResults(results);
}, 300);
```

### 4. Caching

Use React Query or SWR for automatic caching:

```typescript
import { useQuery } from '@tanstack/react-query';

function useTools(filters: FilterOptions) {
  return useQuery({
    queryKey: ['tools', filters],
    queryFn: () => fetchTools(filters),
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
}
```

### 5. Optimistic Updates

Update UI immediately, then sync with server:

```typescript
async function handleDeleteTool(id: number) {
  // Optimistic update
  setTools(tools.filter(t => t.id !== id));
  
  try {
    await deleteTool(id);
  } catch (error) {
    // Revert on error
    const updated = await fetchTools();
    setTools(updated);
    alert('Failed to delete tool');
  }
}
```

### 6. Type Safety

Define interfaces for all API responses and use TypeScript:

```typescript
// types.ts
export interface Tool { /* ... */ }
export interface CreateToolData { /* ... */ }
export interface UpdateToolData { /* ... */ }

// api.ts
export async function fetchTools(): Promise<Tool[]> { /* ... */ }
export async function createTool(data: CreateToolData): Promise<Tool> { /* ... */ }
```

### 7. Environment Variables

Use environment variables for API base URL:

```typescript
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';

async function apiRequest<T>(endpoint: string, options?: RequestInit): Promise<T> {
  const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
  if (!response.ok) throw new Error(`API error: ${response.status}`);
  return response.json();
}
```

---

## Interactive Documentation

Visit the auto-generated Swagger UI for interactive testing:

**Swagger UI**: http://127.0.0.1:8000/docs  
**ReDoc**: http://127.0.0.1:8000/redoc

Both provide:
- Interactive API testing
- Request/response schemas
- Example values
- Try-it-out functionality

---

## Need Help?

- Check the [README](../README_PYTHON.md) for setup instructions
- View [example responses](api-examples/) for all endpoints
- Import [Postman collection](API_POSTMAN_COLLECTION.json) for quick testing
- Open an issue on GitHub for bugs or feature requests
