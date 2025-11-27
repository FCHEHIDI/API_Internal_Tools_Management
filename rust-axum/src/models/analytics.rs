use serde::Serialize;
use utoipa::ToSchema;

/// Department cost breakdown entry.
///
/// Represents aggregated cost data for a single department.
#[derive(Debug, Serialize, ToSchema)]
pub struct DepartmentCost {
    /// Department name (Engineering, Sales, etc.)
    pub department: String,
    /// Total monthly cost for this department
    pub total_cost: f64,
    /// Number of tools owned by this department
    pub tool_count: i64,
    /// Percentage of total budget (0-100)
    pub percentage: f64,
}

/// Complete department costs analysis response.
#[derive(Debug, Serialize, ToSchema)]
pub struct DepartmentCostsResponse {
    /// Sum of all tools' monthly costs
    pub total_cost: f64,
    /// Array of department cost breakdowns, sorted by cost DESC
    pub departments: Vec<DepartmentCost>,
}

/// Expensive tool entry with efficiency metrics.
///
/// Used to identify high-cost tools and their cost-per-user efficiency.
#[derive(Debug, Serialize, ToSchema)]
pub struct ExpensiveTool {
    pub id: i32,
    pub name: String,
    /// Category name (nullable if not categorized)
    pub category: Option<String>,
    /// Monthly subscription cost
    pub monthly_cost: f64,
    /// Number of active users
    pub active_users_count: i32,
    /// Cost per user (monthly_cost / active_users_count)
    pub efficiency_rating: f64,
    /// Owning department
    pub department: String,
}

/// Response containing most expensive tools.
#[derive(Debug, Serialize, ToSchema)]
pub struct ExpensiveToolsResponse {
    /// Tools sorted by monthly_cost DESC
    pub tools: Vec<ExpensiveTool>,
}

/// Tool summary within a category.
#[derive(Debug, Serialize, ToSchema)]
pub struct CategoryTool {
    pub id: i32,
    pub name: String,
    pub monthly_cost: f64,
    pub status: String,
}

/// Statistical insights for a category.
#[derive(Debug, Serialize, ToSchema)]
pub struct CategoryInsights {
    /// Name of most expensive tool in category
    pub most_expensive: Option<String>,
    /// Name of least expensive tool in category
    pub least_expensive: Option<String>,
    /// Average active users across all tools
    pub avg_users: f64,
}

/// Complete category analysis with tools and insights.
#[derive(Debug, Serialize, ToSchema)]
pub struct CategoryTools {
    pub category_id: i32,
    pub category_name: String,
    /// Number of tools in this category
    pub tool_count: i64,
    /// Average cost of tools in this category
    pub average_cost: f64,
    /// Sum of all tool costs in this category
    pub total_cost: f64,
    /// List of tools in this category
    pub tools: Vec<CategoryTool>,
    /// Statistical insights about this category
    pub insights: CategoryInsights,
}

/// Response with all categories and their tool breakdowns.
#[derive(Debug, Serialize, ToSchema)]
pub struct CategoryToolsResponse {
    /// Categories sorted by total_cost DESC
    pub categories: Vec<CategoryTools>,
}

/// Tool with low usage metrics.
///
/// Identifies underutilized tools that may be candidates for removal.
#[derive(Debug, Serialize, ToSchema, Clone)]
pub struct LowUsageTool {
    pub id: i32,
    pub name: String,
    pub category: Option<String>,
    pub monthly_cost: f64,
    pub active_users_count: i32,
    /// Cost per user efficiency rating
    pub efficiency_rating: f64,
    pub department: String,
    /// Severity level: "critical" (0 users), "high" (< threshold/2), "medium" (< threshold)
    pub warning_level: String,
}

/// Low usage tools analysis response.
#[derive(Debug, Serialize, ToSchema)]
pub struct LowUsageToolsResponse {
    /// The usage threshold used for filtering
    pub threshold: i32,
    /// Tools with usage below threshold
    pub tools: Vec<LowUsageTool>,
    /// Count of underutilized tools found
    pub total_tools: usize,
    /// Sum of monthly costs for all low-usage tools
    pub total_wasted_cost: f64,
}

/// Tool summary within a vendor's portfolio.
#[derive(Debug, Serialize, ToSchema)]
pub struct VendorTool {
    pub name: String,
    pub monthly_cost: f64,
    pub department: String,
}

/// Complete vendor analysis with all tools and metrics.
#[derive(Debug, Serialize, ToSchema)]
pub struct VendorSummary {
    /// Vendor/provider name
    pub vendor: String,
    /// Number of tools from this vendor
    pub tool_count: i64,
    /// Total monthly cost across all vendor's tools
    pub total_cost: f64,
    /// Average cost per tool from this vendor
    pub average_cost: f64,
    /// Comma-separated list of departments using vendor's tools
    pub departments: String,
    /// List of all tools from this vendor
    pub tools: Vec<VendorTool>,
}

/// Vendor summary response with all vendors.
#[derive(Debug, Serialize, ToSchema)]
pub struct VendorSummaryResponse {
    /// Vendors sorted by total_cost DESC
    pub vendors: Vec<VendorSummary>,
}
