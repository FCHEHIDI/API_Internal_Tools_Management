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

    // Escape single quotes in strings for SQL
    let name_escaped = req.name.replace("'", "''");
    let description_escaped = req.description.replace("'", "''");
    let vendor_escaped = req.vendor.replace("'", "''");
    let owner_dept_escaped = owner_dept.replace("'", "''");
    let status_escaped = status.replace("'", "''");

    // Use direct SQL with escaped strings (not ideal but works)
    let insert_query = format!(
        "INSERT INTO tools (name, description, vendor, category_id, monthly_cost, active_users_count, owner_department, status) \
         VALUES ('{}', '{}', '{}', {}, {}, {}, CAST('{}'::TEXT AS department_type), CAST('{}'::TEXT AS tool_status_type)) \
         RETURNING id",
        name_escaped,
        description_escaped,
        vendor_escaped,
        req.category_id,
        req.monthly_cost,
        active_users,
        owner_dept_escaped,
        status_escaped
    );

    let row = client
        .query_one(&insert_query, &[])
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

    // Build dynamic update query using direct SQL formatting (workaround for tokio-postgres serialization issue)
    let mut updates = Vec::new();

    if let Some(name) = req.name {
        let name_escaped = name.replace("'", "''");
        updates.push(format!("name = '{}'", name_escaped));
    }

    if let Some(description) = req.description {
        let description_escaped = description.replace("'", "''");
        updates.push(format!("description = '{}'", description_escaped));
    }

    if let Some(vendor) = req.vendor {
        let vendor_escaped = vendor.replace("'", "''");
        updates.push(format!("vendor = '{}'", vendor_escaped));
    }

    if let Some(website_url) = req.website_url {
        let website_url_escaped = website_url.replace("'", "''");
        updates.push(format!("website_url = '{}'", website_url_escaped));
    }

    if let Some(category_id) = req.category_id {
        updates.push(format!("category_id = {}", category_id));
    }

    if let Some(monthly_cost) = req.monthly_cost {
        updates.push(format!("monthly_cost = {}", monthly_cost));
    }

    if let Some(active_users_count) = req.active_users_count {
        updates.push(format!("active_users_count = {}", active_users_count));
    }

    if let Some(owner_department) = req.owner_department {
        let owner_dept_escaped = owner_department.replace("'", "''");
        updates.push(format!("owner_department = CAST('{}'::TEXT AS department_type)", owner_dept_escaped));
    }

    if let Some(status) = req.status {
        let status_escaped = status.replace("'", "''");
        updates.push(format!("status = CAST('{}'::TEXT AS tool_status_type)", status_escaped));
    }

    if updates.is_empty() {
        return Ok(Json(MessageResponse {
            message: "No fields to update".to_string(),
        }));
    }

    updates.push("updated_at = CURRENT_TIMESTAMP".to_string());

    let query = format!("UPDATE tools SET {} WHERE id = {}", updates.join(", "), id);

    client.execute(&query, &[]).await.map_err(|e| {
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
