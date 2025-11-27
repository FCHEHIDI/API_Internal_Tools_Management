use axum::{extract::{Query, State}, http::StatusCode, Json};
use serde::Deserialize;
use std::sync::Arc;

use crate::{
    db::DbPool,
    models::{
        CategoryInsights, CategoryTools, CategoryToolsResponse, CategoryTool,
        DepartmentCost, DepartmentCostsResponse, ErrorResponse, ExpensiveTool,
        ExpensiveToolsResponse, LowUsageTool, LowUsageToolsResponse,
        VendorSummary, VendorSummaryResponse, VendorTool,
    },
};

#[derive(Debug, Deserialize)]
pub struct ExpensiveToolsQuery {
    pub limit: Option<i32>,
}

#[derive(Debug, Deserialize)]
pub struct LowUsageQuery {
    pub threshold: Option<i32>,
}

/// Get department costs analysis
#[utoipa::path(
    get,
    path = "/api/analytics/department-costs",
    tag = "Analytics",
    responses(
        (status = 200, description = "Department costs", body = DepartmentCostsResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn get_department_costs(
    State(pool): State<Arc<DbPool>>,
) -> Result<Json<DepartmentCostsResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let query = "SELECT owner_department, \
                 SUM(monthly_cost) as total_cost, \
                 COUNT(*) as tool_count \
                 FROM tools \
                 GROUP BY owner_department \
                 ORDER BY total_cost DESC";

    let rows = client.query(query, &[]).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to fetch department costs".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let total_cost: f64 = rows.iter().map(|r| r.get::<_, f64>(1)).sum();

    let departments: Vec<DepartmentCost> = rows
        .iter()
        .map(|row| {
            let dept_cost: f64 = row.get(1);
            DepartmentCost {
                department: row.get(0),
                total_cost: (dept_cost * 100.0).round() / 100.0,
                tool_count: row.get(2),
                percentage: ((dept_cost / total_cost * 100.0) * 10.0).round() / 10.0,
            }
        })
        .collect();

    Ok(Json(DepartmentCostsResponse {
        total_cost: (total_cost * 100.0).round() / 100.0,
        departments,
    }))
}

/// Get expensive tools analysis
#[utoipa::path(
    get,
    path = "/api/analytics/expensive-tools",
    tag = "Analytics",
    params(
        ("limit" = Option<i32>, Query, description = "Number of tools to return")
    ),
    responses(
        (status = 200, description = "Expensive tools", body = ExpensiveToolsResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn get_expensive_tools(
    State(pool): State<Arc<DbPool>>,
    Query(params): Query<ExpensiveToolsQuery>,
) -> Result<Json<ExpensiveToolsResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let limit = params.limit.unwrap_or(10);

    let query = "SELECT t.id, t.name, c.name as category, t.monthly_cost, \
                 t.active_users_count, t.owner_department \
                 FROM tools t \
                 LEFT JOIN categories c ON t.category_id = c.id \
                 ORDER BY t.monthly_cost DESC \
                 LIMIT $1";

    let rows = client.query(query, &[&limit]).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to fetch expensive tools".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let tools: Vec<ExpensiveTool> = rows
        .iter()
        .map(|row| {
            let monthly_cost: f64 = row.get(3);
            let active_users: i32 = row.get(4);
            let efficiency = if active_users > 0 {
                ((monthly_cost / active_users as f64) * 100.0).round() / 100.0
            } else {
                monthly_cost
            };

            ExpensiveTool {
                id: row.get(0),
                name: row.get(1),
                category: row.get(2),
                monthly_cost: (monthly_cost * 100.0).round() / 100.0,
                active_users_count: active_users,
                efficiency_rating: efficiency,
                department: row.get(5),
            }
        })
        .collect();

    Ok(Json(ExpensiveToolsResponse { tools }))
}

/// Get tools by category
#[utoipa::path(
    get,
    path = "/api/analytics/tools-by-category",
    tag = "Analytics",
    responses(
        (status = 200, description = "Tools by category", body = CategoryToolsResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn get_tools_by_category(
    State(pool): State<Arc<DbPool>>,
) -> Result<Json<CategoryToolsResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let cat_query = "SELECT c.id, c.name, \
                     COUNT(t.id) as tool_count, \
                     COALESCE(AVG(t.monthly_cost), 0) as avg_cost, \
                     COALESCE(SUM(t.monthly_cost), 0) as total_cost \
                     FROM categories c \
                     LEFT JOIN tools t ON c.id = t.category_id \
                     GROUP BY c.id, c.name \
                     ORDER BY total_cost DESC";

    let cat_rows = client.query(cat_query, &[]).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to fetch categories".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let mut categories = Vec::new();

    for cat_row in cat_rows {
        let category_id: i32 = cat_row.get(0);
        let category_name: String = cat_row.get(1);
        let tool_count: i64 = cat_row.get(2);
        let avg_cost: f64 = cat_row.get(3);
        let total_cost: f64 = cat_row.get(4);

        // Get tools for this category
        let tools_query = "SELECT id, name, monthly_cost, status \
                           FROM tools \
                           WHERE category_id = $1 \
                           ORDER BY monthly_cost DESC";

        let tool_rows = client.query(tools_query, &[&category_id]).await.map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ErrorResponse {
                    error: "Failed to fetch category tools".to_string(),
                    message: e.to_string(),
                }),
            )
        })?;

        let tools: Vec<CategoryTool> = tool_rows
            .iter()
            .map(|row| CategoryTool {
                id: row.get(0),
                name: row.get(1),
                monthly_cost: row.get(2),
                status: row.get(3),
            })
            .collect();

        // Calculate insights
        let insights_query = "SELECT \
                              MAX(name) FILTER (WHERE rn = 1) as most_expensive, \
                              MAX(name) FILTER (WHERE rn_asc = 1) as least_expensive, \
                              COALESCE(AVG(active_users_count), 0) as avg_users \
                              FROM ( \
                                  SELECT name, active_users_count, \
                                         ROW_NUMBER() OVER (ORDER BY monthly_cost DESC) as rn, \
                                         ROW_NUMBER() OVER (ORDER BY monthly_cost ASC) as rn_asc \
                                  FROM tools \
                                  WHERE category_id = $1 \
                              ) sub";

        let insights_row = client.query_one(insights_query, &[&category_id]).await.ok();

        let insights = if let Some(row) = insights_row {
            CategoryInsights {
                most_expensive: row.get(0),
                least_expensive: row.get(1),
                avg_users: ((row.get::<_, f64>(2) * 10.0).round() / 10.0),
            }
        } else {
            CategoryInsights {
                most_expensive: None,
                least_expensive: None,
                avg_users: 0.0,
            }
        };

        categories.push(CategoryTools {
            category_id,
            category_name,
            tool_count,
            average_cost: (avg_cost * 100.0).round() / 100.0,
            total_cost: (total_cost * 100.0).round() / 100.0,
            tools,
            insights,
        });
    }

    Ok(Json(CategoryToolsResponse { categories }))
}

/// Get low usage tools
#[utoipa::path(
    get,
    path = "/api/analytics/low-usage-tools",
    tag = "Analytics",
    params(
        ("threshold" = Option<i32>, Query, description = "Usage threshold")
    ),
    responses(
        (status = 200, description = "Low usage tools", body = LowUsageToolsResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn get_low_usage_tools(
    State(pool): State<Arc<DbPool>>,
    Query(params): Query<LowUsageQuery>,
) -> Result<Json<LowUsageToolsResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let threshold = params.threshold.unwrap_or(10);

    let query = "SELECT t.id, t.name, c.name as category, t.monthly_cost, \
                 t.active_users_count, t.owner_department \
                 FROM tools t \
                 LEFT JOIN categories c ON t.category_id = c.id \
                 WHERE t.active_users_count < $1 \
                 ORDER BY t.active_users_count ASC, t.monthly_cost DESC";

    let rows = client.query(query, &[&threshold]).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to fetch low usage tools".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let tools: Vec<LowUsageTool> = rows
        .iter()
        .map(|row| {
            let monthly_cost: f64 = row.get(3);
            let active_users: i32 = row.get(4);
            let efficiency = if active_users > 0 {
                ((monthly_cost / active_users as f64) * 100.0).round() / 100.0
            } else {
                monthly_cost
            };

            let warning_level = if active_users == 0 {
                "critical"
            } else if active_users < threshold / 2 {
                "high"
            } else {
                "medium"
            };

            LowUsageTool {
                id: row.get(0),
                name: row.get(1),
                category: row.get(2),
                monthly_cost: (monthly_cost * 100.0).round() / 100.0,
                active_users_count: active_users,
                efficiency_rating: efficiency,
                department: row.get(5),
                warning_level: warning_level.to_string(),
            }
        })
        .collect();

    let total_wasted_cost: f64 = tools.iter().map(|t| t.monthly_cost).sum();

    Ok(Json(LowUsageToolsResponse {
        threshold,
        tools: tools.clone(),
        total_tools: tools.len(),
        total_wasted_cost: (total_wasted_cost * 100.0).round() / 100.0,
    }))
}

/// Get vendor summary
#[utoipa::path(
    get,
    path = "/api/analytics/vendor-summary",
    tag = "Analytics",
    responses(
        (status = 200, description = "Vendor summary", body = VendorSummaryResponse),
        (status = 500, description = "Internal server error", body = ErrorResponse)
    )
)]
pub async fn get_vendor_summary(
    State(pool): State<Arc<DbPool>>,
) -> Result<Json<VendorSummaryResponse>, (StatusCode, Json<ErrorResponse>)> {
    let client = pool.get().await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Database connection failed".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let vendor_query = "SELECT vendor, \
                        COUNT(*) as tool_count, \
                        SUM(monthly_cost) as total_cost, \
                        AVG(monthly_cost) as avg_cost, \
                        STRING_AGG(DISTINCT owner_department, ', ' ORDER BY owner_department) as departments \
                        FROM tools \
                        WHERE vendor IS NOT NULL \
                        GROUP BY vendor \
                        ORDER BY total_cost DESC";

    let vendor_rows = client.query(vendor_query, &[]).await.map_err(|e| {
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            Json(ErrorResponse {
                error: "Failed to fetch vendor summary".to_string(),
                message: e.to_string(),
            }),
        )
    })?;

    let mut vendors = Vec::new();

    for vendor_row in vendor_rows {
        let vendor: String = vendor_row.get(0);
        let tool_count: i64 = vendor_row.get(1);
        let total_cost: f64 = vendor_row.get(2);
        let avg_cost: f64 = vendor_row.get(3);
        let departments: String = vendor_row.get(4);

        // Get tools for this vendor
        let tools_query = "SELECT name, monthly_cost, owner_department \
                           FROM tools \
                           WHERE vendor = $1 \
                           ORDER BY monthly_cost DESC";

        let tool_rows = client.query(tools_query, &[&vendor]).await.map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ErrorResponse {
                    error: "Failed to fetch vendor tools".to_string(),
                    message: e.to_string(),
                }),
            )
        })?;

        let tools: Vec<VendorTool> = tool_rows
            .iter()
            .map(|row| VendorTool {
                name: row.get(0),
                monthly_cost: row.get(1),
                department: row.get(2),
            })
            .collect();

        vendors.push(VendorSummary {
            vendor,
            tool_count,
            total_cost: (total_cost * 100.0).round() / 100.0,
            average_cost: (avg_cost * 100.0).round() / 100.0,
            departments,
            tools,
        });
    }

    Ok(Json(VendorSummaryResponse { vendors }))
}
