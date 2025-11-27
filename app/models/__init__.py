"""SQLAlchemy models for the application."""
import enum
from datetime import datetime
from decimal import Decimal
from typing import List

from sqlalchemy import (
    Column,
    Integer,
    String,
    Text,
    Numeric,
    Date,
    DateTime,
    ForeignKey,
    Enum as SQLEnum,
    UniqueConstraint,
    Index,
    CheckConstraint,
)
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func

from app.core.database import Base


# Enums matching PostgreSQL custom types
class DepartmentType(str, enum.Enum):
    """Department enum."""
    ENGINEERING = "Engineering"
    SALES = "Sales"
    MARKETING = "Marketing"
    HR = "HR"
    FINANCE = "Finance"
    OPERATIONS = "Operations"
    DESIGN = "Design"


class ToolStatusType(str, enum.Enum):
    """Tool status enum."""
    ACTIVE = "active"
    DEPRECATED = "deprecated"
    TRIAL = "trial"


class UserRoleType(str, enum.Enum):
    """User role enum."""
    EMPLOYEE = "employee"
    MANAGER = "manager"
    ADMIN = "admin"


class UserStatusType(str, enum.Enum):
    """User status enum."""
    ACTIVE = "active"
    INACTIVE = "inactive"


class AccessStatusType(str, enum.Enum):
    """Access status enum."""
    ACTIVE = "active"
    REVOKED = "revoked"


class RequestStatusType(str, enum.Enum):
    """Request status enum."""
    PENDING = "pending"
    APPROVED = "approved"
    REJECTED = "rejected"


# Models
class Category(Base):
    """Tool category model."""
    __tablename__ = "categories"

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(50), nullable=False, unique=True)
    description = Column(Text)
    color_hex = Column(String(7), default="#6366f1")
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    tools = relationship("Tool", back_populates="category")


class Tool(Base):
    """Tool model."""
    __tablename__ = "tools"

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    description = Column(Text)
    vendor = Column(String(100))
    website_url = Column(String(255))
    category_id = Column(Integer, ForeignKey("categories.id", ondelete="RESTRICT"), nullable=False)
    monthly_cost = Column(Numeric(10, 2), nullable=False)
    active_users_count = Column(Integer, nullable=False, default=0)
    owner_department = Column(SQLEnum(DepartmentType), nullable=False)
    status = Column(SQLEnum(ToolStatusType), default=ToolStatusType.ACTIVE)
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    updated_at = Column(DateTime(timezone=True), server_default=func.now(), onupdate=func.now())

    # Relationships
    category = relationship("Category", back_populates="tools")
    user_accesses = relationship("UserToolAccess", back_populates="tool")
    access_requests = relationship("AccessRequest", back_populates="tool")
    usage_logs = relationship("UsageLog", back_populates="tool")
    cost_tracking = relationship("CostTracking", back_populates="tool")

    # Constraints
    __table_args__ = (
        CheckConstraint("monthly_cost >= 0", name="chk_positive_cost"),
        CheckConstraint("active_users_count >= 0", name="chk_positive_users"),
        Index("idx_tools_category", "category_id"),
        Index("idx_tools_department", "owner_department"),
        Index("idx_tools_cost_desc", "monthly_cost"),
        Index("idx_tools_status", "status"),
        Index("idx_tools_active_users", "active_users_count"),
    )


class User(Base):
    """User model."""
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    email = Column(String(150), nullable=False, unique=True)
    department = Column(SQLEnum(DepartmentType), nullable=False)
    role = Column(SQLEnum(UserRoleType), default=UserRoleType.EMPLOYEE)
    status = Column(SQLEnum(UserStatusType), default=UserStatusType.ACTIVE)
    hire_date = Column(Date)
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    updated_at = Column(DateTime(timezone=True), server_default=func.now(), onupdate=func.now())

    # Relationships
    tool_accesses = relationship("UserToolAccess", foreign_keys="UserToolAccess.user_id", back_populates="user")
    granted_accesses = relationship("UserToolAccess", foreign_keys="UserToolAccess.granted_by", back_populates="granter")
    revoked_accesses = relationship("UserToolAccess", foreign_keys="UserToolAccess.revoked_by", back_populates="revoker")
    access_requests = relationship("AccessRequest", foreign_keys="AccessRequest.user_id", back_populates="user")
    processed_requests = relationship("AccessRequest", foreign_keys="AccessRequest.processed_by", back_populates="processor")
    usage_logs = relationship("UsageLog", back_populates="user")

    # Indexes
    __table_args__ = (
        Index("idx_users_department", "department"),
        Index("idx_users_status", "status"),
    )


class UserToolAccess(Base):
    """User tool access model."""
    __tablename__ = "user_tool_access"

    id = Column(Integer, primary_key=True, autoincrement=True)
    user_id = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    tool_id = Column(Integer, ForeignKey("tools.id", ondelete="CASCADE"), nullable=False)
    granted_at = Column(DateTime(timezone=True), server_default=func.now())
    granted_by = Column(Integer, ForeignKey("users.id", ondelete="RESTRICT"), nullable=False)
    revoked_at = Column(DateTime(timezone=True))
    revoked_by = Column(Integer, ForeignKey("users.id", ondelete="SET NULL"))
    status = Column(SQLEnum(AccessStatusType), default=AccessStatusType.ACTIVE)

    # Relationships
    user = relationship("User", foreign_keys=[user_id], back_populates="tool_accesses")
    tool = relationship("Tool", back_populates="user_accesses")
    granter = relationship("User", foreign_keys=[granted_by], back_populates="granted_accesses")
    revoker = relationship("User", foreign_keys=[revoked_by], back_populates="revoked_accesses")

    # Constraints
    __table_args__ = (
        UniqueConstraint("user_id", "tool_id", "status", name="uq_user_tool_status"),
        CheckConstraint("revoked_at IS NULL OR revoked_at >= granted_at", name="chk_revoke_after_grant"),
        Index("idx_access_user", "user_id"),
        Index("idx_access_tool", "tool_id"),
        Index("idx_access_granted_date", "granted_at"),
        Index("idx_access_status", "status"),
    )


class AccessRequest(Base):
    """Access request model."""
    __tablename__ = "access_requests"

    id = Column(Integer, primary_key=True, autoincrement=True)
    user_id = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    tool_id = Column(Integer, ForeignKey("tools.id", ondelete="CASCADE"), nullable=False)
    business_justification = Column(Text, nullable=False)
    status = Column(SQLEnum(RequestStatusType), default=RequestStatusType.PENDING)
    requested_at = Column(DateTime(timezone=True), server_default=func.now())
    processed_at = Column(DateTime(timezone=True))
    processed_by = Column(Integer, ForeignKey("users.id", ondelete="SET NULL"))
    processing_notes = Column(Text)

    # Relationships
    user = relationship("User", foreign_keys=[user_id], back_populates="access_requests")
    tool = relationship("Tool", back_populates="access_requests")
    processor = relationship("User", foreign_keys=[processed_by], back_populates="processed_requests")

    # Constraints
    __table_args__ = (
        CheckConstraint("processed_at IS NULL OR processed_at >= requested_at", name="chk_process_after_request"),
        Index("idx_requests_status", "status"),
        Index("idx_requests_user", "user_id"),
        Index("idx_requests_date", "requested_at"),
    )


class UsageLog(Base):
    """Usage log model."""
    __tablename__ = "usage_logs"

    id = Column(Integer, primary_key=True, autoincrement=True)
    user_id = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    tool_id = Column(Integer, ForeignKey("tools.id", ondelete="CASCADE"), nullable=False)
    session_date = Column(Date, nullable=False)
    usage_minutes = Column(Integer, default=0)
    actions_count = Column(Integer, default=0)
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    user = relationship("User", back_populates="usage_logs")
    tool = relationship("Tool", back_populates="usage_logs")

    # Indexes
    __table_args__ = (
        Index("idx_usage_date_tool", "session_date", "tool_id"),
        Index("idx_usage_user_date", "user_id", "session_date"),
    )


class CostTracking(Base):
    """Cost tracking model."""
    __tablename__ = "cost_tracking"

    id = Column(Integer, primary_key=True, autoincrement=True)
    tool_id = Column(Integer, ForeignKey("tools.id", ondelete="CASCADE"), nullable=False)
    month_year = Column(Date, nullable=False)
    total_monthly_cost = Column(Numeric(10, 2), nullable=False)
    active_users_count = Column(Integer, nullable=False, default=0)
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    tool = relationship("Tool", back_populates="cost_tracking")

    # Constraints
    __table_args__ = (
        UniqueConstraint("tool_id", "month_year", name="uq_tool_month"),
        CheckConstraint("total_monthly_cost >= 0 AND active_users_count >= 0", name="chk_positive_tracking"),
        Index("idx_cost_month_tool", "month_year", "tool_id"),
    )
