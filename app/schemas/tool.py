"""Pydantic schemas for request/response validation."""
from datetime import datetime
from decimal import Decimal
from typing import Optional, Dict, Any
from pydantic import BaseModel, Field, HttpUrl, ConfigDict

from app.models import DepartmentType, ToolStatusType


# Tool Schemas
class ToolBase(BaseModel):
    """Base tool schema."""
    name: str = Field(..., min_length=2, max_length=100, description="Tool name")
    description: Optional[str] = Field(None, description="Tool description")
    vendor: str = Field(..., max_length=100, description="Vendor name")
    website_url: Optional[HttpUrl] = Field(None, description="Tool website URL")
    category_id: int = Field(..., gt=0, description="Category ID")
    monthly_cost: Decimal = Field(..., ge=0, max_digits=10, decimal_places=2, description="Monthly cost per user")
    owner_department: DepartmentType = Field(..., description="Owner department")


class ToolCreate(ToolBase):
    """Schema for creating a tool."""
    pass


class ToolUpdate(BaseModel):
    """Schema for updating a tool."""
    name: Optional[str] = Field(None, min_length=2, max_length=100)
    description: Optional[str] = None
    vendor: Optional[str] = Field(None, max_length=100)
    website_url: Optional[HttpUrl] = None
    category_id: Optional[int] = Field(None, gt=0)
    monthly_cost: Optional[Decimal] = Field(None, ge=0, max_digits=10, decimal_places=2)
    owner_department: Optional[DepartmentType] = None
    status: Optional[ToolStatusType] = None


class ToolResponse(ToolBase):
    """Schema for tool response."""
    id: int
    status: ToolStatusType
    active_users_count: int
    category: Optional[str] = None  # Category name
    total_monthly_cost: Optional[Decimal] = None
    created_at: datetime
    updated_at: datetime
    usage_metrics: Optional[Dict[str, Any]] = None

    model_config = ConfigDict(from_attributes=True)


class ToolListResponse(BaseModel):
    """Schema for list of tools response."""
    data: list[ToolResponse]
    total: int
    filtered: int
    filters_applied: Dict[str, Any]


# Tool Detail Response (for GET /api/tools/:id)
class ToolDetailResponse(ToolResponse):
    """Schema for detailed tool response."""
    pass
