"""Pydantic schemas for analytics endpoints."""
from decimal import Decimal
from typing import List, Optional
from pydantic import BaseModel, Field


# Department Costs Analytics
class DepartmentCostItem(BaseModel):
    """Department cost item."""
    department: str
    total_cost: Decimal = Field(..., decimal_places=2)
    tools_count: int
    total_users: int
    average_cost_per_tool: Decimal = Field(..., decimal_places=2)
    cost_percentage: Decimal = Field(..., decimal_places=1)


class DepartmentCostSummary(BaseModel):
    """Department cost summary."""
    total_company_cost: Decimal = Field(..., decimal_places=2)
    departments_count: int
    most_expensive_department: str


class DepartmentCostsResponse(BaseModel):
    """Department costs response."""
    data: List[DepartmentCostItem]
    summary: DepartmentCostSummary


# Expensive Tools Analytics
class ExpensiveToolItem(BaseModel):
    """Expensive tool item."""
    id: int
    name: str
    monthly_cost: Decimal = Field(..., decimal_places=2)
    active_users_count: int
    cost_per_user: Decimal = Field(..., decimal_places=2)
    department: str
    vendor: str
    efficiency_rating: str


class ExpensiveToolsAnalysis(BaseModel):
    """Expensive tools analysis."""
    total_tools_analyzed: int
    avg_cost_per_user_company: Decimal = Field(..., decimal_places=2)
    potential_savings_identified: Decimal = Field(..., decimal_places=2)


class ExpensiveToolsResponse(BaseModel):
    """Expensive tools response."""
    data: List[ExpensiveToolItem]
    analysis: ExpensiveToolsAnalysis


# Tools by Category Analytics
class CategoryItem(BaseModel):
    """Category item."""
    category_name: str
    tools_count: int
    total_cost: Decimal = Field(..., decimal_places=2)
    total_users: int
    percentage_of_budget: Decimal = Field(..., decimal_places=1)
    average_cost_per_user: Decimal = Field(..., decimal_places=2)


class CategoryInsights(BaseModel):
    """Category insights."""
    most_expensive_category: str
    most_efficient_category: str


class ToolsByCategoryResponse(BaseModel):
    """Tools by category response."""
    data: List[CategoryItem]
    insights: CategoryInsights


# Low Usage Tools Analytics
class LowUsageToolItem(BaseModel):
    """Low usage tool item."""
    id: int
    name: str
    monthly_cost: Decimal = Field(..., decimal_places=2)
    active_users_count: int
    cost_per_user: Decimal = Field(..., decimal_places=2)
    department: str
    vendor: str
    warning_level: str
    potential_action: str


class SavingsAnalysis(BaseModel):
    """Savings analysis."""
    total_underutilized_tools: int
    potential_monthly_savings: Decimal = Field(..., decimal_places=2)
    potential_annual_savings: Decimal = Field(..., decimal_places=2)


class LowUsageToolsResponse(BaseModel):
    """Low usage tools response."""
    data: List[LowUsageToolItem]
    savings_analysis: SavingsAnalysis


# Vendor Summary Analytics
class VendorItem(BaseModel):
    """Vendor item."""
    vendor: str
    tools_count: int
    total_monthly_cost: Decimal = Field(..., decimal_places=2)
    total_users: int
    departments: str
    average_cost_per_user: Decimal = Field(..., decimal_places=2)
    vendor_efficiency: str


class VendorInsights(BaseModel):
    """Vendor insights."""
    most_expensive_vendor: str
    most_efficient_vendor: str
    single_tool_vendors: int


class VendorSummaryResponse(BaseModel):
    """Vendor summary response."""
    data: List[VendorItem]
    vendor_insights: VendorInsights
