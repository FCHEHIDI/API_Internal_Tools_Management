mod db;
mod handlers;
mod models;

use axum::{
    routing::get,
    Router,
};
use std::sync::Arc;
use tower_http::cors::{Any, CorsLayer};
use tower_http::trace::TraceLayer;
use utoipa::OpenApi;
use utoipa_swagger_ui::SwaggerUi;

use handlers::{
    create_tool, delete_tool, get_department_costs, get_expensive_tools, get_low_usage_tools,
    get_tool, get_tools, get_tools_by_category, get_vendor_summary, health_check, update_tool,
};
use models::*;

#[derive(OpenApi)]
#[openapi(
    paths(
        handlers::health_check,
        handlers::get_tools,
        handlers::get_tool,
        handlers::create_tool,
        handlers::update_tool,
        handlers::delete_tool,
        handlers::get_department_costs,
        handlers::get_expensive_tools,
        handlers::get_tools_by_category,
        handlers::get_low_usage_tools,
        handlers::get_vendor_summary,
    ),
    components(schemas(
        Tool,
        CreateToolRequest,
        UpdateToolRequest,
        ToolsListResponse,
        ErrorResponse,
        MessageResponse,
        HealthResponse,
        DepartmentCost,
        DepartmentCostsResponse,
        ExpensiveTool,
        ExpensiveToolsResponse,
        CategoryTool,
        CategoryInsights,
        CategoryTools,
        CategoryToolsResponse,
        LowUsageTool,
        LowUsageToolsResponse,
        VendorTool,
        VendorSummary,
        VendorSummaryResponse,
    )),
    tags(
        (name = "Health", description = "Health check endpoints"),
        (name = "Tools", description = "Tool management endpoints"),
        (name = "Analytics", description = "Analytics and reporting endpoints")
    ),
    info(
        title = "Internal Tools Management API",
        version = "1.0.0",
        description = "API for managing internal software tools and subscriptions - Rust + Axum implementation",
    )
)]
struct ApiDoc;

#[tokio::main]
async fn main() {
    // Load environment variables
    dotenvy::dotenv().ok();

    // Initialize tracing
    tracing_subscriber::fmt()
        .with_target(false)
        .compact()
        .init();

    // Create database pool
    let pool = db::create_pool().expect("Failed to create database pool");
    let pool = Arc::new(pool);

    // Test database connection
    match pool.get().await {
        Ok(_) => tracing::info!("✅ Database connection successful"),
        Err(e) => {
            tracing::error!("❌ Database connection failed: {}", e);
            std::process::exit(1);
        }
    }

    // CORS configuration
    let cors = CorsLayer::new()
        .allow_origin(Any)
        .allow_methods(Any)
        .allow_headers(Any);

    // Build API routes
    let api_routes = Router::new()
        .route("/health", get(health_check))
        .route("/tools", get(get_tools).post(create_tool))
        .route("/tools/:id", get(get_tool).put(update_tool).delete(delete_tool))
        .route("/analytics/department-costs", get(get_department_costs))
        .route("/analytics/expensive-tools", get(get_expensive_tools))
        .route("/analytics/tools-by-category", get(get_tools_by_category))
        .route("/analytics/low-usage-tools", get(get_low_usage_tools))
        .route("/analytics/vendor-summary", get(get_vendor_summary))
        .with_state(pool);

    // Combine with Swagger UI
    let app = Router::new()
        .merge(SwaggerUi::new("/docs").url("/api-docs/openapi.json", ApiDoc::openapi()))
        .nest("/api", api_routes)
        .layer(cors)
        .layer(TraceLayer::new_for_http());

    // Start server
    let port = std::env::var("PORT").unwrap_or_else(|_| "8000".to_string());
    let addr = format!("0.0.0.0:{}", port);
    let listener = tokio::net::TcpListener::bind(&addr)
        .await
        .expect("Failed to bind to address");

    tracing::info!("🚀 Server starting on http://{}", addr);
    tracing::info!("📚 Swagger docs available at http://{}/docs", addr);

    axum::serve(listener, app)
        .await
        .expect("Failed to start server");
}

