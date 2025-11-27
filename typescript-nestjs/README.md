# Internal Tools Management API - TypeScript/NestJS

> **Branch**: `feature/typescript-nestjs`  
> **Location**: `typescript-nestjs/` directory  
> This branch contains ONLY the TypeScript/NestJS implementation.

## 🎯 Technology Stack

- **Runtime**: Node.js 20+ LTS
- **Framework**: NestJS 10+
- **Language**: TypeScript 5+
- **Database**: PostgreSQL 15 with pg driver
- **Validation**: class-validator + class-transformer
- **Documentation**: Swagger/OpenAPI (auto-generated)
- **Testing**: Jest + Supertest
- **Port**: 8000 (configurable)

## 🏗️ Why NestJS?

### Enterprise-Grade Features
- ✅ **Modular Architecture** - Dependency Injection, clear separation of concerns
- ✅ **TypeScript First** - Full type safety across the entire application
- ✅ **Decorator-Based** - Clean, declarative code with decorators
- ✅ **Auto-Generated Docs** - Swagger UI with full API documentation
- ✅ **Built-in Validation** - class-validator integration
- ✅ **Scalable** - Microservices-ready architecture

### When to Choose NestJS
- 🏢 Enterprise applications requiring scalability
- 👥 Teams with Angular/TypeScript experience
- 🔧 Projects needing strong architectural patterns
- 📊 Applications requiring comprehensive documentation
- 🚀 Microservices architecture

## 📋 Prerequisites

- Node.js 20+ LTS
- npm or yarn
- PostgreSQL 15+ (via Docker)

## 🚀 Quick Start

### 1. Navigate to Directory
```bash
cd typescript-nestjs
```

### 2. Install Dependencies
```bash
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
# Edit .env if needed
```

### 4. Start Database (from root)
```bash
cd ..
docker-compose --profile postgres up -d
cd typescript-nestjs
```

### 5. Run Application

**Development mode (with hot reload):**
```bash
npm run start:dev
```

**Production mode:**
```bash
npm run build
npm run start:prod
```

### 6. Access API

- **API Base**: http://localhost:8000/api
- **Health Check**: http://localhost:8000/api/health
- **Swagger Docs**: http://localhost:8000/docs

## 📁 Project Structure

```
src/
├── main.ts                 # Application entry point
├── app.module.ts           # Root module
├── database/               # Database configuration
│   └── database.module.ts  # PostgreSQL connection pool
├── tools/                  # Tools CRUD module
│   ├── dto/                # Data Transfer Objects
│   │   ├── create-tool.dto.ts
│   │   ├── update-tool.dto.ts
│   │   └── filter-tools.dto.ts
│   ├── entities/           # Entity definitions
│   │   └── tool.entity.ts
│   ├── tools.controller.ts # REST endpoints
│   ├── tools.service.ts    # Business logic
│   └── tools.module.ts     # Module definition
├── analytics/              # Analytics module
│   ├── analytics.controller.ts
│   ├── analytics.service.ts
│   └── analytics.module.ts
└── health/                 # Health check module
    ├── health.controller.ts
    ├── health.service.ts
    └── health.module.ts
```

## 🔧 Available Scripts

```bash
npm run start          # Start production server
npm run start:dev      # Start with hot reload
npm run start:debug    # Start in debug mode
npm run build          # Build for production
npm run test           # Run tests
npm run test:watch     # Run tests in watch mode
npm run test:cov       # Run tests with coverage
npm run lint           # Run ESLint
npm run format         # Format code with Prettier
```

## 🛠️ API Endpoints

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

### Health
- `GET /api/health` - Health check with database status

## 📚 Swagger Documentation

NestJS automatically generates comprehensive API documentation:

1. Start the application: `npm run start:dev`
2. Open http://localhost:8000/docs
3. Explore all endpoints with:
   - Request/response schemas
   - Try-it-out functionality
   - Example values
   - Parameter descriptions

## 🎨 Architecture Highlights

### Dependency Injection
```typescript
@Injectable()
export class ToolsService {
  constructor(@Inject(DATABASE_POOL) private pool: Pool) {}
}
```

### Validation with Decorators
```typescript
export class CreateToolDto {
  @IsString()
  @MinLength(2)
  @MaxLength(100)
  name: string;

  @IsNumber()
  @Min(0)
  monthly_cost: number;
}
```

### Auto-Generated Swagger
```typescript
@ApiTags('tools')
@Controller('tools')
export class ToolsController {
  @Get()
  @ApiOperation({ summary: 'Get all tools' })
  @ApiResponse({ status: 200, description: 'List of tools' })
  findAll() {}
}
```

## 🔒 Security Features

- ✅ Input validation (class-validator)
- ✅ CORS enabled
- ✅ Parameterized SQL queries (SQL injection prevention)
- ✅ Type safety (TypeScript)
- ✅ Environment-based configuration

## 📊 Testing

```bash
# Run all tests
npm test

# Run tests in watch mode
npm run test:watch

# Run tests with coverage
npm run test:cov
```

## 🌍 Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `PORT` | Server port | 8000 |
| `NODE_ENV` | Environment | development |
| `POSTGRES_HOST` | PostgreSQL host | localhost |
| `POSTGRES_PORT` | PostgreSQL port | 5432 |
| `POSTGRES_DB` | Database name | internal_tools |
| `POSTGRES_USER` | Database user | dev |
| `POSTGRES_PASSWORD` | Database password | dev123 |

## ✨ NestJS Features Used

- **Modules** - Organize code into feature modules
- **Controllers** - Handle HTTP requests and responses
- **Services** - Business logic and data access
- **Dependency Injection** - Loose coupling, testability
- **Pipes** - Validation and transformation
- **Exception Filters** - Error handling
- **Decorators** - Metadata for routing, validation, docs

## 🚀 Production Deployment

### Build
```bash
npm run build
```

### Run
```bash
NODE_ENV=production npm run start:prod
```

### Docker (optional)
```dockerfile
FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY dist ./dist
CMD ["node", "dist/main"]
```

## 🔄 Comparison with Other Stacks

| Feature | NestJS | Express | FastAPI |
|---------|--------|---------|---------|
| Type Safety | ✅ Full | ⚠️ Optional | ✅ Full |
| Auto Docs | ✅ Swagger | ❌ Manual | ✅ Swagger |
| DI Container | ✅ Built-in | ❌ Manual | ❌ Manual |
| Architecture | ✅ Opinionated | ⚠️ Flexible | ⚠️ Flexible |
| Learning Curve | Medium | Low | Low |
| Performance | High | High | Very High |

## 📖 Resources

- [NestJS Documentation](https://docs.nestjs.com)
- [TypeScript Handbook](https://www.typescriptlang.org/docs)
- [class-validator](https://github.com/typestack/class-validator)
- [Swagger/OpenAPI](https://swagger.io/specification)

## 📝 License

MIT

---

**Built with NestJS** - A progressive Node.js framework for building efficient and scalable server-side applications.
