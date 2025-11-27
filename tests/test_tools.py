"""Tests for tools CRUD endpoints."""
import pytest
from httpx import AsyncClient
from app.models import Tool


@pytest.mark.asyncio
async def test_get_root(test_client: AsyncClient):
    """Test root endpoint returns API information."""
    response = await test_client.get("/")
    
    assert response.status_code == 200
    data = response.json()
    
    assert "message" in data
    assert "version" in data
    assert "docs" in data
    assert data["docs"] == "/docs"


@pytest.mark.asyncio
async def test_list_tools_empty(test_client: AsyncClient):
    """Test listing tools when database is empty."""
    response = await test_client.get("/api/tools")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)


@pytest.mark.asyncio
async def test_list_tools_with_data(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test listing tools returns all tools."""
    response = await test_client.get("/api/tools")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    assert len(data) >= 2  # At least our sample tools


@pytest.mark.asyncio
async def test_list_tools_filter_by_status(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test filtering tools by status."""
    response = await test_client.get("/api/tools?status=active")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    # All returned tools should have active status
    for tool in data:
        if tool["id"] in [1001, 1002]:  # Our test tools
            assert tool["status"] == "active"


@pytest.mark.asyncio
async def test_list_tools_filter_by_vendor(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test filtering tools by vendor."""
    response = await test_client.get("/api/tools?vendor=GitHub")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    # Should find at least our GitHub test tool
    github_tools = [t for t in data if "GitHub" in t["vendor"]]
    assert len(github_tools) >= 1


@pytest.mark.asyncio
async def test_list_tools_search(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test searching tools by name."""
    response = await test_client.get("/api/tools?search=GitHub")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    # Should find tools matching the search
    matching_tools = [t for t in data if "GitHub" in t["name"] or "GitHub" in t.get("description", "")]
    assert len(matching_tools) >= 1


@pytest.mark.asyncio
async def test_list_tools_pagination(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test pagination parameters."""
    response = await test_client.get("/api/tools?skip=0&limit=1")
    
    assert response.status_code == 200
    data = response.json()
    
    assert isinstance(data, list)
    assert len(data) >= 1  # At least one tool


@pytest.mark.asyncio
async def test_get_tool_by_id(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test getting a specific tool by ID."""
    tool_id = sample_tools[0].id
    response = await test_client.get(f"/api/tools/{tool_id}")
    
    assert response.status_code == 200
    data = response.json()
    
    assert data["id"] == tool_id
    assert "name" in data
    assert "vendor" in data
    assert "monthly_cost" in data
    assert "category" in data


@pytest.mark.asyncio
async def test_get_tool_not_found(test_client: AsyncClient):
    """Test getting a non-existent tool returns 404."""
    response = await test_client.get("/api/tools/999999")
    
    assert response.status_code == 404
    data = response.json()
    
    assert "detail" in data


@pytest.mark.asyncio
async def test_create_tool(test_client: AsyncClient, sample_categories: list):
    """Test creating a new tool."""
    new_tool = {
        "name": "New Test Tool",
        "description": "A tool created by tests",
        "vendor": "Test Vendor",
        "monthly_cost": 99.99,
        "status": "active",
        "owner_department": "Engineering",
        "category_id": sample_categories[0].id,
    }
    
    response = await test_client.post("/api/tools", json=new_tool)
    
    assert response.status_code == 201
    data = response.json()
    
    assert data["name"] == new_tool["name"]
    assert data["vendor"] == new_tool["vendor"]
    assert float(data["monthly_cost"]) == new_tool["monthly_cost"]
    assert "id" in data


@pytest.mark.asyncio
async def test_create_tool_invalid_category(test_client: AsyncClient):
    """Test creating a tool with invalid category returns 400."""
    new_tool = {
        "name": "Invalid Tool",
        "description": "Tool with bad category",
        "vendor": "Test",
        "monthly_cost": 10.00,
        "status": "active",
        "category_id": 999999,  # Non-existent category
    }
    
    response = await test_client.post("/api/tools", json=new_tool)
    
    # Should return 400 or 404 for invalid foreign key
    assert response.status_code in [400, 404, 422]


@pytest.mark.asyncio
async def test_create_tool_missing_required_fields(test_client: AsyncClient):
    """Test creating a tool without required fields returns 422."""
    new_tool = {
        "name": "Incomplete Tool",
        # Missing required fields
    }
    
    response = await test_client.post("/api/tools", json=new_tool)
    
    assert response.status_code == 422
    data = response.json()
    
    assert "detail" in data


@pytest.mark.asyncio
async def test_update_tool(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test updating an existing tool."""
    tool_id = sample_tools[0].id
    update_data = {
        "monthly_cost": 25.00,
        "status": "trial",
    }
    
    response = await test_client.put(f"/api/tools/{tool_id}", json=update_data)
    
    assert response.status_code == 200
    data = response.json()
    
    assert data["id"] == tool_id
    assert float(data["monthly_cost"]) == update_data["monthly_cost"]
    assert data["status"] == update_data["status"]


@pytest.mark.asyncio
async def test_update_tool_not_found(test_client: AsyncClient):
    """Test updating a non-existent tool returns 404."""
    update_data = {
        "monthly_cost": 100.00,
    }
    
    response = await test_client.put("/api/tools/999999", json=update_data)
    
    assert response.status_code == 404


@pytest.mark.asyncio
async def test_update_tool_partial(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test partial update of a tool."""
    tool_id = sample_tools[0].id
    original_name = sample_tools[0].name
    
    update_data = {
        "monthly_cost": 30.00,
    }
    
    response = await test_client.put(f"/api/tools/{tool_id}", json=update_data)
    
    assert response.status_code == 200
    data = response.json()
    
    # Cost should be updated
    assert float(data["monthly_cost"]) == 30.00
    # Name should remain unchanged
    assert data["name"] == original_name


@pytest.mark.asyncio
async def test_delete_tool(test_client: AsyncClient, sample_tools: list[Tool]):
    """Test deleting a tool."""
    tool_id = sample_tools[0].id
    
    response = await test_client.delete(f"/api/tools/{tool_id}")
    
    assert response.status_code == 204
    
    # Verify tool is deleted
    get_response = await test_client.get(f"/api/tools/{tool_id}")
    assert get_response.status_code == 404


@pytest.mark.asyncio
async def test_delete_tool_not_found(test_client: AsyncClient):
    """Test deleting a non-existent tool returns 404."""
    response = await test_client.delete("/api/tools/999999")
    
    assert response.status_code == 404
