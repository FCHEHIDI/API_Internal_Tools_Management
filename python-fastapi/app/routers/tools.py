"""Tools CRUD endpoints."""
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select, and_
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm import selectinload

from app.core.database import get_db
from app.models import Tool, Category
from app.schemas.tool import ToolCreate, ToolUpdate, ToolResponse

router = APIRouter()


@router.get(
    "/tools",
    response_model=List[ToolResponse],
    status_code=status.HTTP_200_OK,
    summary="List All Tools",
    description="Get a list of all tools with optional filtering by category, status, and vendor",
)
async def list_tools(
    category_id: Optional[int] = Query(None, description="Filter by category ID"),
    status_filter: Optional[str] = Query(None, alias="status", description="Filter by status (active/inactive/trial)"),
    vendor: Optional[str] = Query(None, description="Filter by vendor name"),
    search: Optional[str] = Query(None, description="Search in tool name or description"),
    skip: int = Query(0, ge=0, description="Number of records to skip"),
    limit: int = Query(100, ge=1, le=500, description="Maximum number of records to return"),
    db: AsyncSession = Depends(get_db),
):
    """
    Retrieve a list of tools with optional filtering.
    
    Args:
        category_id: Filter by category ID
        status_filter: Filter by status (active/inactive/trial)
        vendor: Filter by vendor name
        search: Search in tool name or description
        skip: Number of records to skip for pagination
        limit: Maximum number of records to return
        db: Database session
        
    Returns:
        List[ToolResponse]: List of tools matching the filters
    """
    # Build query with eager loading of category
    query = select(Tool).options(selectinload(Tool.category))
    
    # Apply filters
    filters = []
    if category_id is not None:
        filters.append(Tool.category_id == category_id)
    if status_filter:
        filters.append(Tool.status == status_filter.lower())
    if vendor:
        filters.append(Tool.vendor.ilike(f"%{vendor}%"))
    if search:
        filters.append(
            (Tool.name.ilike(f"%{search}%")) | (Tool.description.ilike(f"%{search}%"))
        )
    
    if filters:
        query = query.where(and_(*filters))
    
    # Apply pagination
    query = query.offset(skip).limit(limit)
    
    # Execute query
    result = await db.execute(query)
    tools = result.scalars().all()
    
    # Transform tools to include category name
    return [
        ToolResponse(
            **{**tool.__dict__, "category": tool.category.name if tool.category else None}
        )
        for tool in tools
    ]


@router.get(
    "/tools/{tool_id}",
    response_model=ToolResponse,
    status_code=status.HTTP_200_OK,
    summary="Get Tool by ID",
    description="Retrieve a specific tool by its ID",
)
async def get_tool(
    tool_id: int,
    db: AsyncSession = Depends(get_db),
):
    """
    Retrieve a single tool by ID.
    
    Args:
        tool_id: The tool ID
        db: Database session
        
    Returns:
        ToolResponse: The requested tool
        
    Raises:
        HTTPException: 404 if tool not found
    """
    query = select(Tool).options(selectinload(Tool.category)).where(Tool.id == tool_id)
    result = await db.execute(query)
    tool = result.scalar_one_or_none()
    
    if not tool:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Tool with ID {tool_id} not found",
        )
    
    return ToolResponse(
        **{**tool.__dict__, "category": tool.category.name if tool.category else None}
    )


@router.post(
    "/tools",
    response_model=ToolResponse,
    status_code=status.HTTP_201_CREATED,
    summary="Create New Tool",
    description="Create a new tool in the system",
)
async def create_tool(
    tool_data: ToolCreate,
    db: AsyncSession = Depends(get_db),
):
    """
    Create a new tool.
    
    Args:
        tool_data: Tool creation data
        db: Database session
        
    Returns:
        ToolResponse: The created tool
        
    Raises:
        HTTPException: 404 if category not found, 400 if validation fails
    """
    # Verify category exists
    if tool_data.category_id:
        category_query = select(Category).where(Category.id == tool_data.category_id)
        category_result = await db.execute(category_query)
        category = category_result.scalar_one_or_none()
        
        if not category:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"Category with ID {tool_data.category_id} not found",
            )
    
    # Create tool
    new_tool = Tool(**tool_data.model_dump())
    db.add(new_tool)
    await db.commit()
    await db.refresh(new_tool)
    
    # Load category relationship
    await db.refresh(new_tool, ["category"])
    
    return ToolResponse(**{**new_tool.__dict__, "category": new_tool.category.name if new_tool.category else None})


@router.put(
    "/tools/{tool_id}",
    response_model=ToolResponse,
    status_code=status.HTTP_200_OK,
    summary="Update Tool",
    description="Update an existing tool's information",
)
async def update_tool(
    tool_id: int,
    tool_data: ToolUpdate,
    db: AsyncSession = Depends(get_db),
):
    """
    Update an existing tool.
    
    Args:
        tool_id: The tool ID to update
        tool_data: Tool update data (only provided fields will be updated)
        db: Database session
        
    Returns:
        ToolResponse: The updated tool
        
    Raises:
        HTTPException: 404 if tool or category not found
    """
    # Get existing tool
    query = select(Tool).where(Tool.id == tool_id)
    result = await db.execute(query)
    tool = result.scalar_one_or_none()
    
    if not tool:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Tool with ID {tool_id} not found",
        )
    
    # Verify category exists if updating
    if tool_data.category_id is not None:
        category_query = select(Category).where(Category.id == tool_data.category_id)
        category_result = await db.execute(category_query)
        category = category_result.scalar_one_or_none()
        
        if not category:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"Category with ID {tool_data.category_id} not found",
            )
    
    # Update tool with provided fields
    update_data = tool_data.model_dump(exclude_unset=True)
    for field, value in update_data.items():
        setattr(tool, field, value)
    
    await db.commit()
    await db.refresh(tool)
    
    # Load category relationship
    await db.refresh(tool, ["category"])
    
    return ToolResponse(**{**tool.__dict__, "category": tool.category.name if tool.category else None})


@router.delete(
    "/tools/{tool_id}",
    status_code=status.HTTP_204_NO_CONTENT,
    summary="Delete Tool",
    description="Delete a tool from the system",
)
async def delete_tool(
    tool_id: int,
    db: AsyncSession = Depends(get_db),
):
    """
    Delete a tool.
    
    Args:
        tool_id: The tool ID to delete
        db: Database session
        
    Raises:
        HTTPException: 404 if tool not found
    """
    # Get existing tool
    query = select(Tool).where(Tool.id == tool_id)
    result = await db.execute(query)
    tool = result.scalar_one_or_none()
    
    if not tool:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Tool with ID {tool_id} not found",
        )
    
    await db.delete(tool)
    await db.commit()
