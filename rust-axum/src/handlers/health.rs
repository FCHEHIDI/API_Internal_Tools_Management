use axum::{extract::State, http::StatusCode, Json};
use chrono::Utc;
use std::sync::Arc;
use std::time::Instant;

use crate::{db::DbPool, models::HealthResponse};

/// Health check endpoint
#[utoipa::path(
    get,
    path = "/api/health",
    tag = "Health",
    responses(
        (status = 200, description = "Service is healthy", body = HealthResponse)
    )
)]
pub async fn health_check(
    State(pool): State<Arc<DbPool>>,
) -> Result<Json<HealthResponse>, (StatusCode, Json<serde_json::Value>)> {
    let start = Instant::now();
    
    // Test database connection
    let db_status = match pool.get().await {
        Ok(_) => "connected",
        Err(_) => "disconnected",
    };
    
    let response_time = start.elapsed().as_millis();
    
    Ok(Json(HealthResponse {
        status: "healthy".to_string(),
        timestamp: Utc::now(),
        database: db_status.to_string(),
        response_time,
    }))
}
