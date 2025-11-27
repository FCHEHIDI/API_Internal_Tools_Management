"""Pytest configuration and fixtures."""
import asyncio
import sys
from typing import AsyncGenerator, Generator

import pytest
from httpx import AsyncClient, ASGITransport
from sqlalchemy.ext.asyncio import AsyncSession, create_async_engine, async_sessionmaker
from sqlalchemy.pool import NullPool

# Fix Windows asyncio event loop
if sys.platform == 'win32':
    asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())

from app.main import app
from app.core.database import get_db, Base
from app.models import Category, Tool, User

# Test database URL (use main DB for now, can switch to test DB later)
TEST_DATABASE_URL = "postgresql+psycopg://dev:dev123@localhost:5432/internal_tools"


@pytest.fixture(scope="session")
def event_loop() -> Generator:
    """Create event loop for async tests."""
    if sys.platform == 'win32':
        asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())
    
    loop = asyncio.get_event_loop_policy().new_event_loop()
    yield loop
    loop.close()


@pytest.fixture(scope="function")
async def test_engine():
    """Create test database engine."""
    engine = create_async_engine(
        TEST_DATABASE_URL,
        poolclass=NullPool,
        echo=False,
    )
    
    yield engine
    
    await engine.dispose()


@pytest.fixture(scope="function")
async def test_session(test_engine) -> AsyncGenerator[AsyncSession, None]:
    """Create test database session."""
    async_session = async_sessionmaker(
        test_engine,
        class_=AsyncSession,
        expire_on_commit=False,
    )
    
    async with async_session() as session:
        yield session


@pytest.fixture(scope="function")
async def test_client(test_session: AsyncSession) -> AsyncGenerator[AsyncClient, None]:
    """Create test HTTP client with database override."""
    async def override_get_db() -> AsyncGenerator[AsyncSession, None]:
        yield test_session
    
    app.dependency_overrides[get_db] = override_get_db
    
    async with AsyncClient(
        transport=ASGITransport(app=app),
        base_url="http://test"
    ) as client:
        yield client
    
    app.dependency_overrides.clear()


@pytest.fixture
async def sample_categories(test_session: AsyncSession) -> list[Category]:
    """Create sample categories for testing."""
    # Use random IDs to avoid conflicts
    import random
    base_id = random.randint(10000, 99999)
    
    categories = [
        Category(
            id=base_id,
            name=f"Test Development Tools {base_id}",
            description="Tools for software development testing"
        ),
        Category(
            id=base_id + 1,
            name=f"Test Design Tools {base_id}",
            description="Tools for UI/UX design testing"
        ),
    ]
    
    test_session.add_all(categories)
    await test_session.commit()
    
    for cat in categories:
        await test_session.refresh(cat)
    
    return categories


@pytest.fixture
async def sample_tools(test_session: AsyncSession, sample_categories: list[Category]) -> list[Tool]:
    """Create sample tools for testing."""
    import random
    base_id = random.randint(10000, 99999)
    
    tools = [
        Tool(
            id=base_id,
            name="Test GitHub Enterprise",
            description="Source code management for testing",
            vendor="GitHub",
            monthly_cost=21.00,
            status="active",
            category_id=sample_categories[0].id,
            active_users_count=50,
            owner_department="Engineering",
        ),
        Tool(
            id=base_id + 1,
            name="Test Figma Pro",
            description="Design platform for testing",
            vendor="Figma",
            monthly_cost=12.00,
            status="active",
            category_id=sample_categories[1].id,
            active_users_count=15,
            owner_department="Design",
        ),
    ]
    
    test_session.add_all(tools)
    await test_session.commit()
    
    for tool in tools:
        await test_session.refresh(tool)
    
    return tools
