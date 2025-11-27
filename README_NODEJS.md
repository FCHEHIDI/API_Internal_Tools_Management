# Internal Tools Management API - Node.js/Express

## Technology Stack

- **Runtime**: Node.js v22+
- **Framework**: Express.js v4
- **Database**: PostgreSQL 15
- **Validation**: express-validator
- **Testing**: Jest + Supertest
- **Port**: 8000 (configurable)

## Quick Start

### 1. Start Database

```bash
docker-compose --profile postgres up -d
```

### 2. Install Dependencies

```bash
npm install
```

### 3. Configure Environment

```bash
cp .env.example .env
# Edit .env with your database credentials
```

### 4. Start Server

```bash
# Development mode with auto-reload
npm run dev

# Production mode
npm start
```

### 5. Access API

- **API Base**: http://localhost:8000
- **Health Check**: http://localhost:8000/health
- **API Documentation**: http://localhost:8000/docs (coming soon)

## Project Structure

```
src/
├── app.js              # Express app configuration
├── server.js           # Server entry point
├── config/             # Configuration files
│   └── index.js        # Environment config
├── database/           # Database connections
│   └── connection.js   # PostgreSQL pool
├── middleware/         # Express middleware
│   └── errorHandler.js # Error handling
├── routes/             # API routes
│   ├── health.js       # Health check
│   ├── tools.js        # Tools CRUD
│   └── analytics.js    # Analytics endpoints
├── controllers/        # Request handlers
├── models/             # Data models
├── validators/         # Input validation
└── utils/              # Utility functions
```

## Available Scripts

```bash
npm start          # Start production server
npm run dev        # Start development server with nodemon
npm test           # Run tests with coverage
npm run test:watch # Run tests in watch mode
npm run lint       # Run ESLint
npm run format     # Format code with Prettier
```

## API Endpoints

### Tools CRUD
- `GET /api/tools` - List tools (with filters, pagination)
- `GET /api/tools/:id` - Get tool details
- `POST /api/tools` - Create new tool
- `PUT /api/tools/:id` - Update tool
- `DELETE /api/tools/:id` - Delete tool

### Analytics
- `GET /api/analytics/department-costs` - Department cost breakdown
- `GET /api/analytics/expensive-tools` - Most expensive tools
- `GET /api/analytics/tools-by-category` - Category distribution
- `GET /api/analytics/low-usage-tools` - Underutilized tools
- `GET /api/analytics/vendor-summary` - Vendor analysis

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `NODE_ENV` | Environment (development/production/test) | development |
| `PORT` | Server port | 8000 |
| `DB_HOST` | PostgreSQL host | localhost |
| `DB_PORT` | PostgreSQL port | 5432 |
| `DB_NAME` | Database name | internal_tools |
| `DB_USER` | Database user | postgres |
| `DB_PASSWORD` | Database password | postgres |
| `DB_POOL_MIN` | Minimum pool connections | 2 |
| `DB_POOL_MAX` | Maximum pool connections | 10 |
| `CORS_ORIGIN` | CORS allowed origins | * |

## Testing

```bash
# Run all tests
npm test

# Run tests in watch mode
npm run test:watch

# Run specific test file
npm test -- health.test.js
```

## Features

✅ RESTful API with Express.js  
✅ PostgreSQL database with connection pooling  
✅ Input validation with express-validator  
✅ Error handling middleware  
✅ CORS enabled  
✅ Security headers with Helmet  
✅ Request logging with Morgan  
✅ Response compression  
✅ Environment-based configuration  
✅ Graceful shutdown  

## Development

The API uses the same PostgreSQL database as the Python implementation. No changes to the database schema are needed.

## License

MIT
