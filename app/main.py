"""Main FastAPI application entry point."""
import sys
from contextlib import asynccontextmanager

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

# Fix for Windows ProactorEventLoop issue with psycopg
if sys.platform == 'win32':
    import asyncio
    asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())

from app.core.config import settings
from app.core.database import engine


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Manage application startup and shutdown events."""
    # Startup
    print("🚀 Starting Internal Tools Management API...")
    print(f"📊 Database: {settings.DATABASE_URL.split('@')[-1]}")  # Hide credentials
    
    # Test database connection
    try:
        async with engine.connect():
            print("✓ Database connection successful")
    except Exception as e:
        print(f"✗ Database connection failed: {e}")
    
    yield
    
    # Shutdown
    print("\n🛑 Shutting down Internal Tools Management API...")
    await engine.dispose()
    print("✓ Database connections closed")


# Create FastAPI application
app = FastAPI(
    title=settings.APP_NAME,
    version=settings.APP_VERSION,
    description="""
    Internal Tools Management API
    
    ## Features
    
    * **CRUD Operations**: Manage tools, categories, and users
    * **Analytics**: Get insights on tool usage, costs, and departments
    * **Access Management**: Track user access requests and permissions
    * **Cost Tracking**: Monitor monthly costs and vendor spending
    
    ## Endpoints
    
    * **Health Check**: `/health` - API and database status
    * **Tools**: `/api/tools` - Full CRUD operations for tools
    * **Analytics**: `/api/analytics` - 5 comprehensive analytics endpoints
    """,
    docs_url=settings.DOCS_URL,
    redoc_url=settings.REDOC_URL,
    openapi_url=settings.OPENAPI_URL,
    lifespan=lifespan,
)

# Configure CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.CORS_ORIGINS,
    allow_credentials=settings.CORS_ALLOW_CREDENTIALS,
    allow_methods=settings.CORS_ALLOW_METHODS,
    allow_headers=settings.CORS_ALLOW_HEADERS,
)

# Import and include routers
from app.routers import health, tools, analytics

app.include_router(health.router, tags=["Health"])
app.include_router(tools.router, prefix=settings.API_V1_PREFIX, tags=["Tools"])
app.include_router(analytics.router, prefix=settings.API_V1_PREFIX, tags=["Analytics"])


@app.get("/")
async def root():
    """Root endpoint with API information."""
    return {
        "message": "Welcome to Internal Tools Management API",
        "version": settings.APP_VERSION,
        "docs": settings.DOCS_URL,
        "redoc": settings.REDOC_URL,
        "health": "/health",
    }


if __name__ == "__main__":
    import uvicorn
    
    uvicorn.run(
        "app.main:app",
        host=settings.HOST,
        port=settings.PORT,
        reload=settings.DEBUG,
    )
