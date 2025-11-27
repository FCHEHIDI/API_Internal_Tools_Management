# 🐍 Python + FastAPI Implementation

**Technology Stack:** Python 3.11+ | FastAPI 0.104+ | SQLAlchemy 2.0+ | PostgreSQL

---

## 🎯 Why This Stack?

### FastAPI Advantages
- ✅ **Fastest Python framework** - Comparable to Node.js/Go performance
- ✅ **Automatic API docs** - Built-in Swagger UI and ReDoc
- ✅ **Type safety** - Pydantic models with runtime validation
- ✅ **Async/await** - Native async support for I/O operations
- ✅ **Modern Python** - Leverages Python 3.11+ features

### When to Choose
- 🚀 Rapid development cycles
- 📊 Data-heavy applications
- 🤖 ML/AI integration needed
- 👥 Python-experienced team

---

## 📋 Requirements

- Python 3.11 or higher
- PostgreSQL 15+ (via Docker)
- pip or poetry for dependencies

---

## 🚀 Quick Start

### 1. Start Database
```bash
# From project root
docker-compose --profile postgres up -d
```

### 2. Create Virtual Environment
```bash
# Windows PowerShell
python -m venv venv
.\venv\Scripts\Activate.ps1

# Linux/Mac
python3 -m venv venv
source venv/bin/activate
```

### 3. Install Dependencies
```bash
pip install -r requirements.txt
```

### 4. Configure Environment
```bash
# Copy example env file
cp .env.example .env

# Edit .env with your settings (already configured for Docker)
```

### 5. Run Database Migrations (Optional)
```bash
# Using Alembic
alembic upgrade head
```

### 6. Start API
```bash
# Development mode with auto-reload
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

### 7. Access Documentation
- **Swagger UI:** http://localhost:8000/docs
- **ReDoc:** http://localhost:8000/redoc
- **OpenAPI JSON:** http://localhost:8000/openapi.json

---

## 🧪 Testing (TDD)

### Run All Tests
```bash
pytest
```

### Run with Coverage
```bash
pytest --cov=app --cov-report=html --cov-report=term
```

### Run Specific Test File
```bash
pytest tests/test_tools.py -v
```

### Run Integration Tests Only
```bash
pytest tests/integration/ -v
```

### Watch Mode (Development)
```bash
pytest-watch
```

---

## 📁 Project Structure

```
app/
├── api/
│   └── v1/
│       ├── endpoints/
│       │   ├── __init__.py
│       │   ├── tools.py          # Part 1: CRUD endpoints
│       │   └── analytics.py      # Part 2: Analytics endpoints
│       └── router.py              # Main router aggregation
├── core/
│   ├── __init__.py
│   ├── config.py                  # Settings management
│   ├── database.py                # DB session & engine
│   └── security.py                # Security utilities
├── models/
│   ├── __init__.py
│   ├── tool.py                    # SQLAlchemy models
│   ├── category.py
│   └── user.py
├── schemas/
│   ├── __init__.py
│   ├── tool.py                    # Pydantic request/response schemas
│   └── analytics.py
├── services/
│   ├── __init__.py
│   ├── tool_service.py            # Business logic layer
│   └── analytics_service.py
└── main.py                        # Application entry point

tests/
├── __init__.py
├── conftest.py                    # Pytest fixtures
├── test_tools.py                  # Part 1 tests
├── test_analytics.py              # Part 2 tests
└── integration/
    ├── __init__.py
    └── test_database.py

alembic/                           # Database migrations (optional)
├── versions/
└── env.py

requirements.txt                   # Python dependencies
requirements-dev.txt               # Development dependencies
.env.example                       # Environment template
pytest.ini                         # Pytest configuration
```

---

## 📦 Dependencies

### Production Dependencies
```txt
fastapi[all]==0.104.1              # Web framework
sqlalchemy[asyncio]==2.0.23        # ORM with async support
asyncpg==0.29.0                    # PostgreSQL async driver
pydantic==2.5.2                    # Data validation
pydantic-settings==2.1.0           # Settings management
uvicorn[standard]==0.24.0          # ASGI server
python-dotenv==1.0.0               # Environment variables
```

### Development Dependencies
```txt
pytest==7.4.3                      # Testing framework
pytest-asyncio==0.21.1             # Async test support
pytest-cov==4.1.0                  # Coverage reporting
httpx==0.25.2                      # HTTP client for testing
faker==20.1.0                      # Test data generation
black==23.12.0                     # Code formatter
mypy==1.7.1                        # Static type checker
ruff==0.1.7                        # Fast linter
```

---

## 🔧 Configuration

### Environment Variables (.env)
```bash
# Application
APP_NAME="Internal Tools API"
APP_VERSION="1.0.0"
DEBUG=true
HOST=0.0.0.0
PORT=8000

# Database
DATABASE_URL=postgresql+asyncpg://dev:dev123@localhost:5432/internal_tools
DB_ECHO=false                      # Set to true to log SQL queries

# CORS (adjust for production)
CORS_ORIGINS=["http://localhost:3000", "http://localhost:8000"]

# API
API_V1_PREFIX=/api
DOCS_URL=/docs
REDOC_URL=/redoc
```

---

## 🏗️ Architecture Patterns

### 1. Layered Architecture
```
Controllers (API) → Services (Business Logic) → Models (Data Access)
```

### 2. Dependency Injection
```python
# Database session injected via FastAPI Depends
@router.get("/tools")
async def get_tools(db: AsyncSession = Depends(get_db)):
    pass
```

### 3. Repository Pattern (Optional)
```python
# Abstract data access
class ToolRepository:
    async def get_all(self, filters: dict) -> List[Tool]:
        pass
```

### 4. Schema Validation
```python
# Pydantic models for request/response
class ToolCreate(BaseModel):
    name: str = Field(..., min_length=2, max_length=100)
    monthly_cost: Decimal = Field(..., ge=0, decimal_places=2)
```

---

## 🧪 TDD Workflow

### 1. Write Test First
```python
# tests/test_tools.py
async def test_create_tool_success(client, db_session):
    payload = {
        "name": "New Tool",
        "monthly_cost": 10.00,
        "category_id": 1
    }
    response = await client.post("/api/tools", json=payload)
    assert response.status_code == 201
```

### 2. Run Test (Fails)
```bash
pytest tests/test_tools.py::test_create_tool_success -v
```

### 3. Implement Feature
```python
# app/api/v1/endpoints/tools.py
@router.post("/tools", status_code=201)
async def create_tool(tool: ToolCreate, db: AsyncSession = Depends(get_db)):
    # Implementation
    pass
```

### 4. Run Test (Passes)
```bash
pytest tests/test_tools.py::test_create_tool_success -v
```

---

## 📊 Database Operations

### Async Session
```python
from app.core.database import get_db
from sqlalchemy.ext.asyncio import AsyncSession

async def get_tools(db: AsyncSession):
    result = await db.execute(select(Tool))
    return result.scalars().all()
```

### Complex Queries
```python
# Analytics with aggregations
query = (
    select(
        Tool.owner_department,
        func.sum(Tool.monthly_cost).label("total_cost"),
        func.count(Tool.id).label("tools_count")
    )
    .where(Tool.status == "active")
    .group_by(Tool.owner_department)
)
result = await db.execute(query)
```

---

## 🚀 Performance Optimization

### 1. Connection Pooling
```python
# app/core/database.py
engine = create_async_engine(
    DATABASE_URL,
    pool_size=10,
    max_overflow=20,
    pool_pre_ping=True
)
```

### 2. Query Optimization
```python
# Eager loading relationships
result = await db.execute(
    select(Tool).options(selectinload(Tool.category))
)
```

### 3. Response Caching (Optional)
```python
from fastapi_cache import FastAPICache
from fastapi_cache.decorator import cache

@router.get("/tools")
@cache(expire=60)  # Cache for 60 seconds
async def get_tools():
    pass
```

---

## 🔒 Security Best Practices

### Input Validation
```python
class ToolCreate(BaseModel):
    name: str = Field(..., min_length=2, max_length=100)
    website_url: Optional[HttpUrl] = None  # Validates URL format
    monthly_cost: Decimal = Field(..., ge=0, le=999999.99)
```

### SQL Injection Prevention
```python
# SQLAlchemy ORM prevents SQL injection by default
query = select(Tool).where(Tool.id == tool_id)  # Safe
```

### Error Handling
```python
@app.exception_handler(ValidationError)
async def validation_exception_handler(request, exc):
    return JSONResponse(
        status_code=400,
        content={"error": "Validation failed", "details": exc.errors()}
    )
```

---

## 📈 Monitoring & Logging

### Structured Logging
```python
import logging

logger = logging.getLogger(__name__)

@router.post("/tools")
async def create_tool(tool: ToolCreate):
    logger.info(f"Creating tool: {tool.name}")
    # Implementation
    logger.info(f"Tool created successfully: ID {new_tool.id}")
```

### Health Check Endpoint
```python
@router.get("/health")
async def health_check(db: AsyncSession = Depends(get_db)):
    try:
        await db.execute(text("SELECT 1"))
        return {"status": "healthy", "database": "connected"}
    except Exception as e:
        return {"status": "unhealthy", "error": str(e)}
```

---

## 🐛 Debugging

### Enable SQL Logging
```python
# In .env
DB_ECHO=true
```

### Debug Mode
```bash
# Start with debugger
uvicorn app.main:app --reload --log-level debug
```

### VS Code Debug Configuration
```json
{
  "name": "FastAPI",
  "type": "python",
  "request": "launch",
  "module": "uvicorn",
  "args": ["app.main:app", "--reload"],
  "jinja": true
}
```

---

## 📚 Additional Resources

- [FastAPI Documentation](https://fastapi.tiangolo.com/)
- [SQLAlchemy 2.0 Tutorial](https://docs.sqlalchemy.org/en/20/tutorial/)
- [Pydantic Documentation](https://docs.pydantic.dev/latest/)
- [pytest Documentation](https://docs.pytest.org/)

---

## ✅ Implementation Checklist

### Part 1: CRUD (8h)
- [ ] Setup project structure
- [ ] Configure database connection
- [ ] Create SQLAlchemy models
- [ ] Implement GET /api/tools (with filters)
- [ ] Implement GET /api/tools/:id
- [ ] Implement POST /api/tools
- [ ] Implement PUT /api/tools/:id
- [ ] Write unit tests (TDD)
- [ ] Write integration tests
- [ ] Add error handling
- [ ] Generate OpenAPI docs

### Part 2: Analytics (8h)
- [ ] Implement department-costs endpoint
- [ ] Implement expensive-tools endpoint
- [ ] Implement tools-by-category endpoint
- [ ] Implement low-usage-tools endpoint
- [ ] Implement vendor-summary endpoint
- [ ] Handle edge cases (division by zero, etc.)
- [ ] Write analytics tests
- [ ] Optimize complex queries
- [ ] Update documentation

---

## 🤝 Contributing

Follow PEP 8 and use provided dev tools:
```bash
# Format code
black app/ tests/

# Lint code
ruff check app/ tests/

# Type check
mypy app/
```

---

**Happy coding! 🚀**
