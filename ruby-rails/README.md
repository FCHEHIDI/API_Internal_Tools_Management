# Internal Tools API - Ruby on Rails

Ruby on Rails 8.1 API for managing internal SaaS tools with comprehensive analytics.

## Features

- **CRUD Operations**: Complete tool management (Create, Read, Update, Delete)
- **Advanced Analytics**: 5 powerful analytics endpoints for cost optimization
- **OpenAPI/Swagger Documentation**: Interactive API docs at `/api-docs`
- **Docker Support**: Fully containerized with PostgreSQL
- **Modern Rails**: Rails 8.1 with ActiveRecord, Puma server

## Tech Stack

- **Ruby**: 3.3.10
- **Rails**: 8.1.1
- **Database**: PostgreSQL 15
- **API Documentation**: rswag (OpenAPI 3.0)
- **Container**: Docker + Docker Compose

## Quick Start

### Prerequisites

- Docker and Docker Compose installed
- PostgreSQL container running (internal-tools-postgres)

### Running with Docker (Recommended)

1. **Start PostgreSQL** (if not already running):
```bash
docker-compose up -d postgres
```

2. **Start Ruby on Rails API**:
```bash
docker-compose up -d ruby-rails
```

The API will be available at: **http://localhost:3000**

3. **View Logs**:
```bash
docker logs -f internal-tools-ruby-rails
```

### Running Locally (Without Docker)

1. **Install dependencies**:
```bash
bundle install
```

2. **Configure database** (set environment variables):
```bash
export DATABASE_HOST=localhost
export DATABASE_PORT=5432
export DATABASE_NAME=internal_tools
export DATABASE_USERNAME=dev
export DATABASE_PASSWORD=dev123
```

3. **Run migrations** (if needed):
```bash
rails db:migrate
```

4. **Start server**:
```bash
rails server -b 0.0.0.0
```

## API Documentation

### Interactive Swagger UI
Visit: **http://localhost:3000/api-docs**

### OpenAPI Spec
Available at: `swagger/v1/swagger.yaml`

## API Endpoints

### Tools Management (CRUD)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tools` | List all tools with filters |
| POST | `/api/tools` | Create a new tool |
| GET | `/api/tools/:id` | Get tool details |
| PUT/PATCH | `/api/tools/:id` | Update a tool |
| DELETE | `/api/tools/:id` | Delete a tool |

#### Query Parameters (GET /api/tools)
- `department`: Filter by department (Engineering, Sales, Marketing, HR, Finance, Operations)
- `status`: Filter by status (active, inactive, trial)
- `category_id`: Filter by category ID
- `search`: Search by tool name
- `min_cost` & `max_cost`: Filter by cost range
- `sort_by`: Sort field (default: created_at)
- `order`: Sort order (asc/desc, default: desc)
- `limit`: Results limit (max 100, default: 20)

### Analytics Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/analytics/department-costs` | Budget analysis by department |
| GET | `/api/analytics/expensive-tools` | Most expensive tools with efficiency ratings |
| GET | `/api/analytics/tools-by-category` | Tool distribution by category |
| GET | `/api/analytics/low-usage-tools` | Underutilized tools analysis |
| GET | `/api/analytics/vendor-summary` | Vendor consolidation opportunities |

## Example Requests

### Create a Tool
```bash
curl -X POST http://localhost:3000/api/tools \
  -H "Content-Type: application/json" \
  -d '{
    "tool": {
      "name": "Slack",
      "vendor": "Slack Technologies",
      "category_id": 1,
      "monthly_cost": 500.00,
      "active_users_count": 50,
      "owner_department": "Engineering",
      "status": "active",
      "website_url": "https://slack.com",
      "description": "Team communication platform"
    }
  }'
```

### Get Department Costs
```bash
curl http://localhost:3000/api/analytics/department-costs
```

### List Tools with Filters
```bash
curl "http://localhost:3000/api/tools?department=Engineering&status=active&limit=10"
```

## Docker Commands

```bash
# Start the API
docker-compose up -d ruby-rails

# Stop the API
docker-compose down ruby-rails

# View logs
docker logs -f internal-tools-ruby-rails

# Restart the API
docker restart internal-tools-ruby-rails

# Execute Rails console
docker exec -it internal-tools-ruby-rails rails console

# Run database migrations
docker exec -it internal-tools-ruby-rails rails db:migrate

# Generate Swagger documentation
docker exec -it internal-tools-ruby-rails rake rswag:specs:swaggerize
```

## Development

### Running Tests
```bash
# Inside container
docker exec -it internal-tools-ruby-rails rspec

# Locally
bundle exec rspec
```

### Generate API Documentation
```bash
# Inside container
docker exec -it internal-tools-ruby-rails rake rswag:specs:swaggerize

# Locally
rake rswag:specs:swaggerize
```

### Rails Console
```bash
# Inside container
docker exec -it internal-tools-ruby-rails rails console

# Locally
rails console
```

## Database Schema

### Categories Table
- `id`: Primary key
- `name`: Category name (unique, 2-50 chars)
- `description`: Category description
- `color_hex`: Hex color code (#RRGGBB)
- `created_at`: Creation timestamp

### Tools Table
- `id`: Primary key
- `name`: Tool name (unique, 2-100 chars)
- `description`: Tool description
- `vendor`: Vendor name (required)
- `website_url`: Tool website
- `category_id`: Foreign key to categories
- `monthly_cost`: Monthly cost (decimal)
- `active_users_count`: Number of active users
- `owner_department`: Department (enum)
- `status`: Tool status (active/inactive/trial)
- `created_at`, `updated_at`: Timestamps

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DATABASE_HOST` | PostgreSQL host | localhost |
| `DATABASE_PORT` | PostgreSQL port | 5432 |
| `DATABASE_NAME` | Database name | internal_tools |
| `DATABASE_USERNAME` | Database user | dev |
| `DATABASE_PASSWORD` | Database password | dev123 |
| `RAILS_ENV` | Rails environment | development |

## Troubleshooting

### Container won't start
```bash
# Check logs
docker logs internal-tools-ruby-rails

# Remove and recreate
docker rm -f internal-tools-ruby-rails
docker-compose up -d ruby-rails
```

### Database connection errors
```bash
# Ensure PostgreSQL is running
docker ps | grep postgres

# Check environment variables
docker exec -it internal-tools-ruby-rails env | grep DATABASE
```

### Port already in use
```bash
# Stop conflicting process or change port in docker-compose.yml
# Default port: 3000
```

## Project Structure

```
ruby-rails/
├── app/
│   ├── controllers/
│   │   └── api/
│   │       ├── analytics_controller.rb    # Analytics endpoints
│   │       └── tools_controller.rb        # CRUD endpoints
│   └── models/
│       ├── category.rb                    # Category model
│       └── tool.rb                        # Tool model with validations
├── config/
│   ├── database.yml                       # Database configuration
│   ├── routes.rb                          # API routes
│   └── initializers/
│       ├── rswag_api.rb                  # Swagger API config
│       └── rswag_ui.rb                   # Swagger UI config
├── db/
│   └── migrate/                           # Database migrations
├── spec/
│   ├── requests/api/                      # API specs for rswag
│   ├── rails_helper.rb                    # RSpec Rails config
│   └── swagger_helper.rb                  # Swagger config
└── swagger/
    └── v1/
        └── swagger.yaml                   # OpenAPI 3.0 specification
```

## License

This project is part of the Internal Tools Management multi-stack API comparison.

