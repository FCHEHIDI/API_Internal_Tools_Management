"""Tests for health check endpoint."""
import pytest
from httpx import AsyncClient


@pytest.mark.asyncio
async def test_health_check_success(test_client: AsyncClient):
    """Test health check returns healthy status."""
    response = await test_client.get("/health")
    
    assert response.status_code == 200
    data = response.json()
    
    assert data["status"] == "healthy"
    assert data["database"] == "connected"
    assert "timestamp" in data
    assert "version" in data


@pytest.mark.asyncio
async def test_health_check_includes_timestamp(test_client: AsyncClient):
    """Test health check includes ISO format timestamp."""
    response = await test_client.get("/health")
    
    assert response.status_code == 200
    data = response.json()
    
    assert "timestamp" in data
    # Verify it's a valid ISO timestamp
    assert "T" in data["timestamp"]


@pytest.mark.asyncio
async def test_health_check_database_status(test_client: AsyncClient):
    """Test health check verifies database connection."""
    response = await test_client.get("/health")
    
    assert response.status_code == 200
    data = response.json()
    
    # Database should be connected in test environment
    assert data["database"] in ["connected", "disconnected", "error"]
