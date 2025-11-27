"""Tests for analytics endpoints."""
import pytest
from httpx import AsyncClient


@pytest.mark.asyncio
async def test_department_costs_requires_params(test_client: AsyncClient):
    """Test department costs endpoint requires year and month."""
    response = await test_client.get("/api/analytics/department-costs")
    
    assert response.status_code == 422
    data = response.json()
    
    assert "detail" in data


@pytest.mark.asyncio
async def test_department_costs_with_params(test_client: AsyncClient):
    """Test department costs with valid parameters."""
    response = await test_client.get(
        "/api/analytics/department-costs?year=2024&month=11"
    )
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    # Each item should have required fields
    for item in data:
        assert "department" in item
        assert "total_cost" in item
        assert "tool_count" in item
        assert isinstance(item["total_cost"], (int, float))
        assert isinstance(item["tool_count"], int)


@pytest.mark.asyncio
async def test_department_costs_invalid_month(test_client: AsyncClient):
    """Test department costs with invalid month."""
    response = await test_client.get(
        "/api/analytics/department-costs?year=2024&month=13"
    )
    
    assert response.status_code == 422


@pytest.mark.asyncio
async def test_expensive_tools_default_limit(test_client: AsyncClient):
    """Test expensive tools with default limit."""
    response = await test_client.get("/api/analytics/expensive-tools")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    # Default limit is 10, should not exceed
    assert len(data) <= 10


@pytest.mark.asyncio
async def test_expensive_tools_custom_limit(test_client: AsyncClient):
    """Test expensive tools with custom limit."""
    response = await test_client.get("/api/analytics/expensive-tools?limit=5")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    assert len(data) <= 5
    
    # Each tool should have required fields
    for tool in data:
        assert "id" in tool
        assert "name" in tool
        assert "vendor" in tool
        assert "monthly_cost" in tool
        assert "active_users_count" in tool


@pytest.mark.asyncio
async def test_expensive_tools_ordered_by_cost(test_client: AsyncClient):
    """Test expensive tools are ordered by cost descending."""
    response = await test_client.get("/api/analytics/expensive-tools?limit=5")
    
    assert response.status_code == 200
    data = response.json()
    
    if len(data) >= 2:
        # Verify descending order
        costs = [float(tool["monthly_cost"]) for tool in data]
        assert costs == sorted(costs, reverse=True)


@pytest.mark.asyncio
async def test_tools_by_category(test_client: AsyncClient):
    """Test tools distribution by category."""
    response = await test_client.get("/api/analytics/tools-by-category")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    
    # Each category should have required fields
    for category in data:
        assert "category_id" in category
        assert "category_name" in category
        assert "tool_count" in category
        assert "total_monthly_cost" in category
        assert isinstance(category["tool_count"], int)
        assert isinstance(category["total_monthly_cost"], (int, float))


@pytest.mark.asyncio
async def test_tools_by_category_ordered(test_client: AsyncClient):
    """Test tools by category are ordered by total cost."""
    response = await test_client.get("/api/analytics/tools-by-category")
    
    assert response.status_code == 200
    data = response.json()
    
    if len(data) >= 2:
        # Verify descending order by total cost
        costs = [float(cat["total_monthly_cost"]) for cat in data]
        assert costs == sorted(costs, reverse=True)


@pytest.mark.asyncio
async def test_low_usage_tools_requires_params(test_client: AsyncClient):
    """Test low usage tools requires year and month."""
    response = await test_client.get("/api/analytics/low-usage-tools")
    
    assert response.status_code == 422


@pytest.mark.asyncio
async def test_low_usage_tools_with_params(test_client: AsyncClient):
    """Test low usage tools with valid parameters."""
    response = await test_client.get(
        "/api/analytics/low-usage-tools?year=2024&month=11&threshold=5"
    )
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    
    # Each tool should have required fields
    for tool in data:
        assert "id" in tool
        assert "name" in tool
        assert "vendor" in tool
        assert "monthly_cost" in tool
        assert "active_users_count" in tool
        assert "usage_count" in tool
        assert isinstance(tool["usage_count"], int)
        # Usage count should be <= threshold
        assert tool["usage_count"] <= 5


@pytest.mark.asyncio
async def test_low_usage_tools_cost_per_usage(test_client: AsyncClient):
    """Test low usage tools includes cost per usage calculation."""
    response = await test_client.get(
        "/api/analytics/low-usage-tools?year=2024&month=11&threshold=10"
    )
    
    assert response.status_code == 200
    data = response.json()
    
    for tool in data:
        if tool["usage_count"] > 0:
            # Should have cost_per_usage
            assert "cost_per_usage" in tool
            expected = tool["monthly_cost"] / tool["usage_count"]
            assert abs(tool["cost_per_usage"] - expected) < 0.01
        else:
            # Zero usage should have null cost_per_usage
            assert tool.get("cost_per_usage") is None


@pytest.mark.asyncio
async def test_vendor_summary(test_client: AsyncClient):
    """Test vendor summary endpoint."""
    response = await test_client.get("/api/analytics/vendor-summary")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    
    # Each vendor should have required fields
    for vendor in data:
        assert "vendor" in vendor
        assert "tool_count" in vendor
        assert "total_monthly_cost" in vendor
        assert "total_users" in vendor
        assert "average_cost_per_tool" in vendor
        assert isinstance(vendor["tool_count"], int)
        assert isinstance(vendor["total_monthly_cost"], (int, float))
        assert isinstance(vendor["total_users"], int)


@pytest.mark.asyncio
async def test_vendor_summary_ordered(test_client: AsyncClient):
    """Test vendor summary is ordered by total cost."""
    response = await test_client.get("/api/analytics/vendor-summary")
    
    assert response.status_code == 200
    data = response.json()
    
    if len(data) >= 2:
        # Verify descending order by total cost
        costs = [float(vendor["total_monthly_cost"]) for vendor in data]
        assert costs == sorted(costs, reverse=True)


@pytest.mark.asyncio
async def test_vendor_summary_average_calculation(test_client: AsyncClient):
    """Test vendor summary calculates average correctly."""
    response = await test_client.get("/api/analytics/vendor-summary")
    
    assert response.status_code == 200
    data = response.json()
    
    for vendor in data:
        if vendor["tool_count"] > 0:
            expected_avg = vendor["total_monthly_cost"] / vendor["tool_count"]
            assert abs(vendor["average_cost_per_tool"] - expected_avg) < 0.01


@pytest.mark.asyncio
async def test_all_analytics_endpoints_accessible(test_client: AsyncClient):
    """Test all analytics endpoints are accessible."""
    endpoints = [
        "/api/analytics/department-costs?year=2024&month=11",
        "/api/analytics/expensive-tools",
        "/api/analytics/tools-by-category",
        "/api/analytics/low-usage-tools?year=2024&month=11&threshold=5",
        "/api/analytics/vendor-summary",
    ]
    
    for endpoint in endpoints:
        response = await test_client.get(endpoint)
        assert response.status_code == 200, f"Failed for {endpoint}"
        assert isinstance(response.json(), list), f"Not a list for {endpoint}"
