"""Health check endpoint for API monitoring."""
from datetime import datetime

from fastapi import APIRouter, status
from sqlalchemy import text

from app.core.database import get_db

router = APIRouter()


@router.get(
    "/health",
    status_code=status.HTTP_200_OK,
    summary="Health Check",
    description="Check API and database connectivity status",
    response_description="Health status of the API and database",
)
async def health_check():
    """
    Health check endpoint.
    
    Returns:
        dict: Health status including:
            - status: Overall API status
            - timestamp: Current server time
            - database: Database connection status
            - version: API version
    """
    health_status = {
        "status": "healthy",
        "timestamp": datetime.utcnow().isoformat(),
        "database": "disconnected",
        "version": "1.0.0",
    }
    
    try:
        # Test database connection
        async for db in get_db():
            result = await db.execute(text("SELECT 1"))
            if result.scalar() == 1:
                health_status["database"] = "connected"
            break
    except Exception as e:
        health_status["status"] = "degraded"
        health_status["database"] = f"error: {str(e)}"
    
    return health_status
