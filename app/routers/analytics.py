"""Analytics endpoints for insights and reporting."""
from typing import List

from fastapi import APIRouter, Depends, Query, status
from sqlalchemy import select, func, desc, and_, extract
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.database import get_db
from app.models import Tool, Category, User, UsageLog, CostTracking
from app.schemas.analytics import (
    DepartmentCostResponse,
    ExpensiveToolResponse,
    ToolsByCategoryResponse,
    LowUsageToolResponse,
    VendorSummaryResponse,
)

router = APIRouter()


@router.get(
    "/analytics/department-costs",
    response_model=List[DepartmentCostResponse],
    status_code=status.HTTP_200_OK,
    summary="Department-wise Tool Costs",
    description="Get total tool costs aggregated by department with user counts",
)
async def get_department_costs(
    year: int = Query(..., description="Year for cost analysis", ge=2020, le=2100),
    month: int = Query(..., description="Month for cost analysis", ge=1, le=12),
    db: AsyncSession = Depends(get_db),
):
    """
    Get department-wise tool costs for a specific month.
    
    This endpoint aggregates total costs by department and includes:
    - Total cost per department
    - Number of users in the department
    - Department name
    
    Args:
        year: Year for analysis
        month: Month for analysis (1-12)
        db: Database session
        
    Returns:
        List[DepartmentCostResponse]: Department cost breakdown
    """
    # Build query to aggregate costs by department
    query = (
        select(
            Tool.owner_department.label("department"),
            func.sum(Tool.monthly_cost).label("total_cost"),
            func.count(Tool.id).label("tool_count"),
        )
        .select_from(Tool)
        .where(Tool.status == "active")
        .group_by(Tool.owner_department)
        .order_by(desc("total_cost"))
    )
    
    result = await db.execute(query)
    rows = result.all()
    
    return [
        DepartmentCostResponse(
            department=row.department,
            total_cost=float(row.total_cost),
            tool_count=row.tool_count,
        )
        for row in rows
    ]


@router.get(
    "/analytics/expensive-tools",
    response_model=List[ExpensiveToolResponse],
    status_code=status.HTTP_200_OK,
    summary="Most Expensive Tools",
    description="Get the top N most expensive tools by monthly cost",
)
async def get_expensive_tools(
    limit: int = Query(10, description="Number of tools to return", ge=1, le=100),
    db: AsyncSession = Depends(get_db),
):
    """
    Get the most expensive tools by monthly cost.
    
    Returns tools ordered by their monthly cost in descending order,
    including active user count and category information.
    
    Args:
        limit: Number of tools to return (default 10, max 100)
        db: Database session
        
    Returns:
        List[ExpensiveToolResponse]: Most expensive tools
    """
    query = (
        select(
            Tool.id,
            Tool.name,
            Tool.vendor,
            Tool.monthly_cost,
            Tool.active_users_count,
            Category.name.label("category_name"),
        )
        .select_from(Tool)
        .outerjoin(Category, Tool.category_id == Category.id)
        .where(Tool.status == "active")
        .order_by(desc(Tool.monthly_cost))
        .limit(limit)
    )
    
    result = await db.execute(query)
    rows = result.all()
    
    return [
        ExpensiveToolResponse(
            id=row.id,
            name=row.name,
            vendor=row.vendor,
            monthly_cost=float(row.monthly_cost),
            active_users_count=row.active_users_count,
            category_name=row.category_name,
        )
        for row in rows
    ]


@router.get(
    "/analytics/tools-by-category",
    response_model=List[ToolsByCategoryResponse],
    status_code=status.HTTP_200_OK,
    summary="Tools Distribution by Category",
    description="Get the number of tools in each category with total costs",
)
async def get_tools_by_category(
    db: AsyncSession = Depends(get_db),
):
    """
    Get tool distribution across categories.
    
    Aggregates the number of tools and total monthly costs by category,
    ordered by total cost in descending order.
    
    Args:
        db: Database session
        
    Returns:
        List[ToolsByCategoryResponse]: Category breakdown
    """
    query = (
        select(
            Category.id,
            Category.name,
            func.count(Tool.id).label("tool_count"),
            func.coalesce(func.sum(Tool.monthly_cost), 0).label("total_monthly_cost"),
        )
        .select_from(Category)
        .outerjoin(Tool, Category.id == Tool.category_id)
        .group_by(Category.id, Category.name)
        .order_by(desc(func.coalesce(func.sum(Tool.monthly_cost), 0)))
    )
    
    result = await db.execute(query)
    rows = result.all()
    
    return [
        ToolsByCategoryResponse(
            category_id=row.id,
            category_name=row.name,
            tool_count=row.tool_count,
            total_monthly_cost=float(row.total_monthly_cost or 0),
        )
        for row in rows
    ]


@router.get(
    "/analytics/low-usage-tools",
    response_model=List[LowUsageToolResponse],
    status_code=status.HTTP_200_OK,
    summary="Low Usage Tools",
    description="Identify tools with low usage relative to their cost",
)
async def get_low_usage_tools(
    year: int = Query(..., description="Year for usage analysis", ge=2020, le=2100),
    month: int = Query(..., description="Month for usage analysis", ge=1, le=12),
    threshold: int = Query(5, description="Maximum usage count threshold", ge=0, le=100),
    db: AsyncSession = Depends(get_db),
):
    """
    Identify tools with low usage for cost optimization.
    
    Returns tools with usage count below the threshold for a specific month,
    ordered by monthly cost (highest first) to prioritize savings opportunities.
    
    Args:
        year: Year for analysis
        month: Month for analysis (1-12)
        threshold: Maximum usage count to consider as "low usage"
        db: Database session
        
    Returns:
        List[LowUsageToolResponse]: Low usage tools
    """
    # Subquery to count usage per tool for the specified month
    usage_subquery = (
        select(
            UsageLog.tool_id,
            func.count(UsageLog.id).label("usage_count"),
        )
        .where(
            and_(
                extract("year", UsageLog.session_date) == year,
                extract("month", UsageLog.session_date) == month,
            )
        )
        .group_by(UsageLog.tool_id)
        .subquery()
    )
    
    # Main query
    query = (
        select(
            Tool.id,
            Tool.name,
            Tool.vendor,
            Tool.monthly_cost,
            Tool.active_users_count,
            func.coalesce(usage_subquery.c.usage_count, 0).label("usage_count"),
        )
        .select_from(Tool)
        .outerjoin(usage_subquery, Tool.id == usage_subquery.c.tool_id)
        .where(
            and_(
                Tool.status == "active",
                func.coalesce(usage_subquery.c.usage_count, 0) <= threshold,
            )
        )
        .order_by(desc(Tool.monthly_cost))
    )
    
    result = await db.execute(query)
    rows = result.all()
    
    return [
        LowUsageToolResponse(
            id=row.id,
            name=row.name,
            vendor=row.vendor,
            monthly_cost=float(row.monthly_cost),
            active_users_count=row.active_users_count,
            usage_count=row.usage_count,
            cost_per_usage=float(row.monthly_cost / row.usage_count) if row.usage_count > 0 else None,
        )
        for row in rows
    ]


@router.get(
    "/analytics/vendor-summary",
    response_model=List[VendorSummaryResponse],
    status_code=status.HTTP_200_OK,
    summary="Vendor Cost Summary",
    description="Get total spending and tool count by vendor",
)
async def get_vendor_summary(
    db: AsyncSession = Depends(get_db),
):
    """
    Get vendor spending summary.
    
    Aggregates total monthly costs and tool counts by vendor,
    ordered by total cost in descending order to identify major vendors.
    
    Args:
        db: Database session
        
    Returns:
        List[VendorSummaryResponse]: Vendor summary
    """
    query = (
        select(
            Tool.vendor,
            func.count(Tool.id).label("tool_count"),
            func.sum(Tool.monthly_cost).label("total_monthly_cost"),
            func.sum(Tool.active_users_count).label("total_users"),
        )
        .select_from(Tool)
        .where(Tool.status == "active")
        .group_by(Tool.vendor)
        .order_by(desc("total_monthly_cost"))
    )
    
    result = await db.execute(query)
    rows = result.all()
    
    return [
        VendorSummaryResponse(
            vendor=row.vendor,
            tool_count=row.tool_count,
            total_monthly_cost=float(row.total_monthly_cost),
            total_users=row.total_users,
            average_cost_per_tool=float(row.total_monthly_cost / row.tool_count) if row.tool_count > 0 else 0,
        )
        for row in rows
    ]
