use axum::{
    extract::{Path, Query, State},
    http::StatusCode,
    Json,
};
use serde::Deserialize;
use std::sync::Arc;

use crate::{
    db::DbPool,
    models::{CreateToolRequest, ErrorResponse, MessageResponse, Tool, ToolsListResponse, UpdateToolRequest},
};

#[derive(Debug, Deserialize)]
pub struct ToolsQuery {
    pub status: Option<String>,
    pub category_id: Option<i32>,
    pub vendor: Option<String>,
    pub search: Option<String>,
    pub limit: Option<i64>,
    pub skip: Option<i64>,
}

/// Get all tools with optional filters
#[utoipa::path(
    get,
    path = "/api/tools",
    tag = "Tools",
    params(
        ("status" = Option<String>, Query, description = "Filter by status"),
        ("category_id" = Option<i32>, Query, description = "Filter by category ID"),
        ("vendor" = Option<String>, Query, description = "Filter by vendor"),
        ("search" = Option<String>, Query, description = "Search in tool name"),
        ("limit" = Option<i64>, Query, description = "Number of results"),
        ("skip" = Option<i64>, Query, description = "Number of results to skip"),
    ),
    responses(
        (status = 200, description = "List of tools", body = ToolsListResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn get_tools(
    State(pool): State<Arc<DbPool>>,
    Query(params): Query<ToolsQuery>,
) -> Result<Json<ToolsListResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let limit = params.limit.unwrap_or(50);
    let skip = params.skip.unwrap_or(0);

    // Build query without dynamic parameters for simplicity
    let query = format!(
        "SELECT t.id, t.name, t.description, t.vendor, t.website_url, \
         t.category_id, CAST(t.monthly_cost AS DOUBLE PRECISION) as monthly_cost, t.active_users_count, \
         CAST(t.owner_department AS TEXT) as owner_department, CAST(t.status AS TEXT) as status, \
         to_char(t.created_at, 'YYYY-MM-DD\"T\"HH24:MI:SS\"Z\"') as created_at, \
         to_char(t.updated_at, 'YYYY-MM-DD\"T\"HH24:MI:SS\"Z\"') as updated_at, \
         c.name as category \
         FROM tools t \
         LEFT JOIN categories c ON t.category_id = c.id \
         WHERE 1=1 {} {} {} {} \
         ORDER BY t.id LIMIT $1 OFFSET $2",
        params.status.as_ref().map(|s| format!(" AND t.status = '{}'", s)).unwrap_or_default(),
        params.category_id.map(|id| format!(" AND t.category_id = {}", id)).unwrap_or_default(),
        params.vendor.as_ref().map(|v| format!(" AND t.vendor ILIKE '%{}%'", v.replace("'", "''"))).unwrap_or_default(),
        params.search.as_ref().map(|s| format!(" AND t.name ILIKE '%{}%'", s.replace("'", "''"))).unwrap_or_default(),
    );

    let count_query = format!(
        "SELECT COUNT(*) FROM tools t WHERE 1=1 {} {} {} {}",
        params.status.as_ref().map(|s| format!(" AND t.status = '{}'", s)).unwrap_or_default(),
        params.category_id.map(|id| format!(" AND t.category_id = {}", id)).unwrap_or_default(),
        params.vendor.as_ref().map(|v| format!(" AND t.vendor ILIKE '%{}%'", v.replace("'", "''"))).unwrap_or_default(),
        params.search.as_ref().map(|s| format!(" AND t.name ILIKE '%{}%'", s.replace("'", "''"))).unwrap_or_default(),
    );

    // Get total count
    let total: i64 = client
        .query_one(&count_query, &[])
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ErrorResponse {
                    error: "Failed to count tools".to_string(),
                    message: e.to_string(),
                }),
            )
        })?
        .get(0);

    // Execute main query
    let rows = client.query(&query, &[&limit, &skip]).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to fetch tools".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let tools: Vec<Tool> = rows
        .iter()
        .map(|row| {
            let created_at_str: String = row.get(10);
            let updated_at_str: String = row.get(11);
            
            Tool {
                id: row.get(0),
                name: row.get(1),
                description: row.get(2),
                vendor: row.get(3),
                website_url: row.get(4),
                category_id: row.get(5),
                monthly_cost: row.get(6),
                active_users_count: row.get(7),
                owner_department: row.get(8),
                status: row.get(9),
                created_at: chrono::DateTime::parse_from_rfc3339(&format!("{}Z", created_at_str.trim_end_matches('Z')))
                    .ok()
                    .map(|dt| dt.with_timezone(&chrono::Utc))
                    .unwrap_or_else(|| chrono::Utc::now()),
                updated_at: chrono::DateTime::parse_from_rfc3339(&format!("{}Z", updated_at_str.trim_end_matches('Z')))
                    .ok()
                    .map(|dt| dt.with_timezone(&chrono::Utc))
                    .unwrap_or_else(|| chrono::Utc::now()),
                category: row.get(12),
            }
        })
        .collect();

    let filters_applied = serde_json::json!({
        "status": params.status,
        "category_id": params.category_id,
        "vendor": params.vendor,
        "search": params.search,
    });

    Ok(Json(ToolsListResponse {
        data: tools.clone(),
        total,
        filtered: tools.len(),
        filters_applied,
    }))
}

/// Get single tool by ID
#[utoipa::path(
    get,
    path = "/api/tools/{id}",
    tag = "Tools",
    params(
        ("id" = i32, Path, description = "Tool ID")
    ),
    responses(
        (status = 200, description = "Tool details", body = Tool),
        (status = 404, description = "Tool not found", body = ErrorResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn get_tool(
    State(pool): State<Arc<DbPool>>,
    Path(id): Path<i32>,
) -> Result<Json<Tool>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let query = "SELECT t.id, t.name, t.description, t.vendor, t.website_url, \
                 t.category_id, CAST(t.monthly_cost AS DOUBLE PRECISION) as monthly_cost, t.active_users_count, \
                 CAST(t.owner_department AS TEXT) as owner_department, CAST(t.status AS TEXT) as status, \
                 to_char(t.created_at, 'YYYY-MM-DD\"T\"HH24:MI:SS\"Z\"') as created_at, \
                 to_char(t.updated_at, 'YYYY-MM-DD\"T\"HH24:MI:SS\"Z\"') as updated_at, \
                 c.name as category \
                 FROM tools t \
                 LEFT JOIN categories c ON t.category_id = c.id \
                 WHERE t.id = $1";

    let row = client.query_opt(query, &[&id]).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to fetch tool".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    match row {
        Some(row) => {
            let created_at_str: String = row.get(10);
            let updated_at_str: String = row.get(11);
            
            Ok(Json(Tool {
                id: row.get(0),
                name: row.get(1),
                description: row.get(2),
                vendor: row.get(3),
                website_url: row.get(4),
                category_id: row.get(5),
                monthly_cost: row.get(6),
                active_users_count: row.get(7),
                owner_department: row.get(8),
                status: row.get(9),
                created_at: chrono::DateTime::parse_from_rfc3339(&format!("{}Z", created_at_str.trim_end_matches('Z')))
                    .ok()
                    .map(|dt| dt.with_timezone(&chrono::Utc))
                    .unwrap_or_else(|| chrono::Utc::now()),
                updated_at: chrono::DateTime::parse_from_rfc3339(&format!("{}Z", updated_at_str.trim_end_matches('Z')))
                    .ok()
                    .map(|dt| dt.with_timezone(&chrono::Utc))
                    .unwrap_or_else(|| chrono::Utc::now()),
                category: row.get(12),
            }))
        }
        None => Err((
            StatusCode::NOT_FOUND,
            Json(ErrorResponse {
                error: "Tool not found".to_string(),
                message: format!("Tool with ID {} does not exist", id),
            }),
        )),
    }
}

/// Create new tool
#[utoipa::path(
    post,
    path = "/api/tools",
    tag = "Tools",
    request_body = CreateToolRequest,
    responses(
        (status = 201, description = "Tool created", body = Tool),
        (status = 400, description = "Invalid request", body = ErrorResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn create_tool(
    State(pool): State<Arc<DbPool>>,
    Json(req): Json<CreateToolRequest>,
) -> Result<(StatusCode, Json<Tool>), (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let status = req.status.unwrap_or_else(|| "active".to_string());
    let active_users = req.active_users_count.unwrap_or(0);
    let owner_dept = req.owner_department.unwrap_or_else(|| "Engineering".to_string());

    // Use execute instead of query_one, then fetch the created row
    let insert_query = "INSERT INTO tools (name, description, vendor, category_id, \
                        monthly_cost, active_users_count, owner_department, status) \
                        VALUES ($1, $2, $3, $4, $5, $6, $7, $8) \
                        RETURNING id";

    let row = client
        .query_one(
            insert_query,
            &[
                &req.name,
                &req.description,
                &req.vendor,
                &req.category_id,
                &req.monthly_cost,
                &active_users,
                &owner_dept,
                &status,
            ],
        )
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ErrorResponse {
                    error: "Failed to create tool".to_string(),
                    message: e.to_string(),
                }),
            )
        })?;
    
    let new_id: i32 = row.get(0);

    // Now fetch the complete row
    let select_query = "SELECT t.id, t.name, t.description, t.vendor, t.website_url, \
                        t.category_id, CAST(t.monthly_cost AS DOUBLE PRECISION) as monthly_cost, t.active_users_count, \
                        CAST(t.owner_department AS TEXT) as owner_department, CAST(t.status AS TEXT) as status, \
                        to_char(t.created_at, 'YYYY-MM-DD\"T\"HH24:MI:SS\"Z\"') as created_at, \
                        to_char(t.updated_at, 'YYYY-MM-DD\"T\"HH24:MI:SS\"Z\"') as updated_at \
                        FROM tools t WHERE t.id = $1";
    
    let row = client
        .query_one(select_query, &[&new_id])
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ErrorResponse {
                    error: "Failed to create tool".to_string(),
                    message: e.to_string(),
                }),
            )
        })?;

    // Get category name
    let category: Option<String> = client
        .query_opt("SELECT name FROM categories WHERE id = $1", &[&(req.category_id as i64)])
        .await
        .ok()
        .flatten()
        .map(|r| r.get(0));

    let created_at_str: String = row.get(10);
    let updated_at_str: String = row.get(11);

    Ok((
        StatusCode::CREATED,
        Json(Tool {
            id: row.get(0),
            name: row.get(1),
            description: row.get(2),
            vendor: row.get(3),
            website_url: row.get(4),
            category_id: row.get(5),
            monthly_cost: row.get(6),
            active_users_count: row.get(7),
            owner_department: row.get(8),
            status: row.get(9),
            created_at: chrono::DateTime::parse_from_rfc3339(&format!("{}Z", created_at_str.trim_end_matches('Z')))
                .ok()
                .map(|dt| dt.with_timezone(&chrono::Utc))
                .unwrap_or_else(|| chrono::Utc::now()),
            updated_at: chrono::DateTime::parse_from_rfc3339(&format!("{}Z", updated_at_str.trim_end_matches('Z')))
                .ok()
                .map(|dt| dt.with_timezone(&chrono::Utc))
                .unwrap_or_else(|| chrono::Utc::now()),
            category,
        }),
    ))
}

/// Update existing tool
#[utoipa::path(
    put,
    path = "/api/tools/{id}",
    tag = "Tools",
    params(
        ("id" = i32, Path, description = "Tool ID")
    ),
    request_body = UpdateToolRequest,
    responses(
        (status = 200, description = "Tool updated", body = MessageResponse),
        (status = 404, description = "Tool not found", body = ErrorResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn update_tool(
    State(pool): State<Arc<DbPool>>,
    Path(id): Path<i32>,
    Json(req): Json<UpdateToolRequest>,
) -> Result<Json<MessageResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    // Check if tool exists
    let exists: bool = client
        .query_opt("SELECT 1 FROM tools WHERE id = $1", &[&id])
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ErrorResponse {
                    error: "Failed to check tool existence".to_string(),
                    message: e.to_string(),
                }),
            )
        })?
        .is_some();

    if !exists {
        return Err((
            StatusCode::NOT_FOUND,
            Json(ErrorResponse {
                error: "Tool not found".to_string(),
                message: format!("Tool with ID {} does not exist", id),
            }),
        ));
    }

    // Build dynamic update query
    let mut updates = Vec::new();
    let mut params: Vec<Box<dyn tokio_postgres::types::ToSql + Send + Sync>> = Vec::new();
    let mut param_index = 1;

    if let Some(name) = req.name {
        updates.push(format!("name = ${}", param_index));
        params.push(Box::new(name));
        param_index += 1;
    }

    if let Some(description) = req.description {
        updates.push(format!("description = ${}", param_index));
        params.push(Box::new(description));
        param_index += 1;
    }

    if let Some(vendor) = req.vendor {
        updates.push(format!("vendor = ${}", param_index));
        params.push(Box::new(vendor));
        param_index += 1;
    }

    if let Some(website_url) = req.website_url {
        updates.push(format!("website_url = ${}", param_index));
        params.push(Box::new(website_url));
        param_index += 1;
    }

    if let Some(category_id) = req.category_id {
        updates.push(format!("category_id = ${}", param_index));
        params.push(Box::new(category_id as i64));
        param_index += 1;
    }

    if let Some(monthly_cost) = req.monthly_cost {
        updates.push(format!("monthly_cost = ${}", param_index));
        params.push(Box::new(monthly_cost));
        param_index += 1;
    }

    if let Some(active_users_count) = req.active_users_count {
        updates.push(format!("active_users_count = ${}", param_index));
        params.push(Box::new(active_users_count as i64));
        param_index += 1;
    }

    if let Some(owner_department) = req.owner_department {
        updates.push(format!("owner_department = ${}", param_index));
        params.push(Box::new(owner_department));
        param_index += 1;
    }

    if let Some(status) = req.status {
        updates.push(format!("status = ${}", param_index));
        params.push(Box::new(status));
        param_index += 1;
    }

    if updates.is_empty() {
        return Ok(Json(MessageResponse {
            message: "No fields to update".to_string(),
        }));
    }

    updates.push("updated_at = CURRENT_TIMESTAMP".to_string());
    params.push(Box::new(id));

    let query = format!("UPDATE tools SET {} WHERE id = ${}", updates.join(", "), param_index);

    let params_refs: Vec<&(dyn tokio_postgres::types::ToSql + Sync)> = params
        .iter()
        .map(|p| p.as_ref() as &(dyn tokio_postgres::types::ToSql + Sync))
        .collect();

    client.execute(&query, &params_refs).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to update tool".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    Ok(Json(MessageResponse {
        message: "Tool updated successfully".to_string(),
    }))
}

/// Delete tool
#[utoipa::path(
    delete,
    path = "/api/tools/{id}",
    tag = "Tools",
    params(
        ("id" = i32, Path, description = "Tool ID")
    ),
    responses(
        (status = 200, description = "Tool deleted", body = MessageResponse),
        (status = 404, description = "Tool not found", body = ErrorResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn delete_tool(
    State(pool): State<Arc<DbPool>>,
    Path(id): Path<i32>,
) -> Result<Json<MessageResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let result = client
        .execute("DELETE FROM tools WHERE id = $1", &[&id])
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ErrorResponse {
                    error: "Failed to delete tool".to_string(),
                    message: e.to_string(),
                }),
            )
        })?;

    if result == 0 {
        return Err((
            StatusCode::NOT_FOUND,
            Json(ErrorResponse {
                error: "Tool not found".to_string(),
                message: format!("Tool with ID {} does not exist", id),
            }),
        ));
    }

    Ok(Json(MessageResponse {
        message: "Tool deleted successfully".to_string(),
    }))
}
