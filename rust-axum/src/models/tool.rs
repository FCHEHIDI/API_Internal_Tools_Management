use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use utoipa::ToSchema;

/// Represents a software tool in the system.
///
/// # Fields
/// * `id` - Unique identifier for the tool
/// * `name` - Tool name (2-100 characters)
/// * `description` - Optional detailed description
/// * `vendor` - Optional vendor/provider name
/// * `website_url` - Optional tool website URL
/// * `category_id` - Foreign key to categories table
/// * `monthly_cost` - Monthly subscription cost in USD
/// * `active_users_count` - Number of active users
/// * `owner_department` - Department responsible for the tool
/// * `status` - Current status (active, deprecated, trial)
/// * `created_at` - Timestamp when tool was created
/// * `updated_at` - Timestamp when tool was last updated
/// * `category` - Optional category name (from JOIN)
#[derive(Debug, Serialize, Deserialize, ToSchema, Clone)]
pub struct Tool {
    pub id: i32,
    pub name: String,
    pub description: Option<String>,
    pub vendor: Option<String>,
    pub website_url: Option<String>,
    pub category_id: i32,
    pub monthly_cost: f64,
    pub active_users_count: i32,
    pub owner_department: Option<String>,
    pub status: String,
    pub created_at: DateTime<Utc>,
    pub updated_at: DateTime<Utc>,
    pub category: Option<String>,
}

/// Request payload for creating a new tool.
///
/// # Validation Rules
/// * `name` - Required, 2-100 characters
/// * `description` - Required
/// * `vendor` - Required, max 100 characters
/// * `website_url` - Optional, must be valid URL if provided
/// * `category_id` - Required, must exist in database
/// * `monthly_cost` - Required, must be >= 0
/// * `active_users_count` - Optional, defaults to 0
/// * `owner_department` - Required, must be valid department
/// * `status` - Optional, defaults to "active", must be: active, deprecated, or trial
#[derive(Debug, Deserialize, ToSchema)]
pub struct CreateToolRequest {
    pub name: String,
    pub description: String,
    pub vendor: String,
    pub website_url: Option<String>,
    pub category_id: i32,
    pub monthly_cost: f64,
    pub active_users_count: Option<i32>,
    pub owner_department: Option<String>,
    pub status: Option<String>,
}

/// Request payload for updating an existing tool.
///
/// All fields are optional - only provided fields will be updated.
/// Supports partial updates for efficient modification.
///
/// # Example
/// ```rust,ignore
/// use internal_tools_api::models::UpdateToolRequest;
///
/// // Update only the name and cost
/// let request = UpdateToolRequest {
///     name: Some("New Name".to_string()),
///     monthly_cost: Some(19.99),
///     description: None,
///     vendor: None,
///     website_url: None,
///     category_id: None,
///     active_users_count: None,
///     owner_department: None,
///     status: None,
/// };
/// ```
#[derive(Debug, Deserialize, ToSchema)]
pub struct UpdateToolRequest {
    pub name: Option<String>,
    pub description: Option<String>,
    pub vendor: Option<String>,
    pub website_url: Option<String>,
    pub category_id: Option<i32>,
    pub monthly_cost: Option<f64>,
    pub active_users_count: Option<i32>,
    pub owner_department: Option<String>,
    pub status: Option<String>,
}

/// Response containing a list of tools with pagination metadata.
///
/// # Fields
/// * `data` - Array of tool objects
/// * `total` - Total count of tools in database (before filtering)
/// * `filtered` - Count of tools after filters applied
/// * `filters_applied` - JSON object showing which filters were used
#[derive(Debug, Serialize, ToSchema)]
pub struct ToolsListResponse {
    pub data: Vec<Tool>,
    pub total: i64,
    pub filtered: usize,
    pub filters_applied: serde_json::Value,
}

/// Standard error response structure.
///
/// Used for all error cases (4xx, 5xx status codes).
/// Provides consistent error format across all endpoints.
#[derive(Debug, Serialize, ToSchema)]
pub struct ErrorResponse {
    pub error: String,
    pub message: String,
}

/// Generic success message response.
///
/// Used for operations that don't return data (e.g., DELETE, UPDATE).
#[derive(Debug, Serialize, ToSchema)]
pub struct MessageResponse {
    pub message: String,
}

/// Health check response with system status information.
///
/// # Fields
/// * `status` - Overall system status ("healthy" or "unhealthy")
/// * `timestamp` - Current server timestamp (UTC)
/// * `database` - Database connection status ("connected" or "disconnected")
/// * `response_time` - Database ping time in milliseconds
#[derive(Debug, Serialize, ToSchema)]
pub struct HealthResponse {
    pub status: String,
    pub timestamp: DateTime<Utc>,
    pub database: String,
    pub response_time: u128,
}
