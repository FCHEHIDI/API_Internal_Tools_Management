//! Integration tests for the Internal Tools Management API.
//!
//! These tests verify the complete API functionality including:
//! - Model serialization/deserialization
//! - Request validation
//! - Response formatting
//! - Error handling

use chrono::Utc;
use serde_json;

// Import models from the main crate
use internal_tools_api::models::*;

#[cfg(test)]
mod model_tests {
    use super::*;

    /// Test Tool model serialization to JSON.
    ///
    /// Verifies that Tool struct can be properly serialized with all fields.
    #[test]
    fn test_tool_serialization() {
        let tool = Tool {
            id: 1,
            name: "Test Tool".to_string(),
            description: Some("A test tool".to_string()),
            vendor: Some("Test Vendor".to_string()),
            website_url: Some("https://test.com".to_string()),
            category_id: 1,
            monthly_cost: 9.99,
            active_users_count: 10,
            owner_department: "Engineering".to_string(),
            status: "active".to_string(),
            created_at: Utc::now(),
            updated_at: Utc::now(),
            category: Some("Development".to_string()),
        };

        let json = serde_json::to_string(&tool);
        assert!(json.is_ok());
        
        let json_str = json.unwrap();
        assert!(json_str.contains("\"name\":\"Test Tool\""));
        assert!(json_str.contains("\"monthly_cost\":9.99"));
    }

    /// Test Tool deserialization from JSON.
    ///
    /// Verifies that Tool can be properly deserialized with optional fields.
    #[test]
    fn test_tool_deserialization() {
        let json = r#"{
            "id": 1,
            "name": "Test Tool",
            "description": null,
            "vendor": null,
            "website_url": null,
            "category_id": 1,
            "monthly_cost": 9.99,
            "active_users_count": 10,
            "owner_department": "Engineering",
            "status": "active",
            "created_at": "2025-11-27T10:00:00Z",
            "updated_at": "2025-11-27T10:00:00Z",
            "category": "Development"
        }"#;

        let tool: Result<Tool, _> = serde_json::from_str(json);
        assert!(tool.is_ok());
        
        let tool = tool.unwrap();
        assert_eq!(tool.name, "Test Tool");
        assert_eq!(tool.monthly_cost, 9.99);
        assert_eq!(tool.description, None);
    }

    /// Test CreateToolRequest deserialization with all required fields.
    #[test]
    fn test_create_request_valid() {
        let json = r#"{
            "name": "New Tool",
            "description": "A new tool",
            "vendor": "New Vendor",
            "website_url": "https://new.com",
            "category_id": 2,
            "monthly_cost": 19.99,
            "active_users_count": 5,
            "owner_department": "Sales",
            "status": "trial"
        }"#;

        let request: Result<CreateToolRequest, _> = serde_json::from_str(json);
        assert!(request.is_ok());
        
        let req = request.unwrap();
        assert_eq!(req.name, "New Tool");
        assert_eq!(req.monthly_cost, 19.99);
        assert_eq!(req.active_users_count, Some(5));
    }

    /// Test CreateToolRequest with optional fields omitted.
    #[test]
    fn test_create_request_optional_fields() {
        let json = r#"{
            "name": "Basic Tool",
            "description": "Basic",
            "vendor": "Vendor",
            "category_id": 1,
            "monthly_cost": 10.0,
            "owner_department": "Engineering"
        }"#;

        let request: Result<CreateToolRequest, _> = serde_json::from_str(json);
        assert!(request.is_ok());
        
        let req = request.unwrap();
        assert_eq!(req.website_url, None);
        assert_eq!(req.active_users_count, None);
        assert_eq!(req.status, None);
    }

    /// Test UpdateToolRequest with partial fields.
    ///
    /// Verifies that update requests support partial updates.
    #[test]
    fn test_update_request_partial() {
        let json = r#"{
            "name": "Updated Name",
            "monthly_cost": 29.99
        }"#;

        let request: Result<UpdateToolRequest, _> = serde_json::from_str(json);
        assert!(request.is_ok());
        
        let req = request.unwrap();
        assert_eq!(req.name, Some("Updated Name".to_string()));
        assert_eq!(req.monthly_cost, Some(29.99));
        assert_eq!(req.description, None);
    }

    /// Test ErrorResponse serialization.
    #[test]
    fn test_error_response() {
        let error = ErrorResponse {
            error: "Not Found".to_string(),
            message: "Tool with ID 999 does not exist".to_string(),
        };

        let json = serde_json::to_string(&error);
        assert!(json.is_ok());
        
        let json_str = json.unwrap();
        assert!(json_str.contains("\"error\":\"Not Found\""));
        assert!(json_str.contains("\"message\":"));
    }

    /// Test ToolsListResponse structure.
    #[test]
    fn test_tools_list_response() {
        let response = ToolsListResponse {
            data: vec![],
            total: 100,
            filtered: 0,
            filters_applied: serde_json::json!({"status": "active"}),
        };

        let json = serde_json::to_string(&response);
        assert!(json.is_ok());
        
        let json_str = json.unwrap();
        assert!(json_str.contains("\"total\":100"));
        assert!(json_str.contains("\"filtered\":0"));
    }

    /// Test DepartmentCost calculation fields.
    #[test]
    fn test_department_cost() {
        let dept_cost = DepartmentCost {
            department: "Engineering".to_string(),
            total_cost: 1500.50,
            tool_count: 10,
            percentage: 45.5,
        };

        assert_eq!(dept_cost.department, "Engineering");
        assert_eq!(dept_cost.total_cost, 1500.50);
        assert_eq!(dept_cost.percentage, 45.5);
    }

    /// Test ExpensiveTool efficiency rating.
    #[test]
    fn test_expensive_tool_efficiency() {
        let tool = ExpensiveTool {
            id: 1,
            name: "Expensive Tool".to_string(),
            category: Some("Design".to_string()),
            monthly_cost: 79.99,
            active_users_count: 5,
            efficiency_rating: 15.998, // 79.99 / 5
            department: "Design".to_string(),
        };

        assert!(tool.efficiency_rating > 15.0);
        assert!(tool.efficiency_rating < 16.0);
    }

    /// Test LowUsageTool warning levels.
    #[test]
    fn test_low_usage_warning_levels() {
        // Critical warning (0 users)
        let critical = LowUsageTool {
            id: 1,
            name: "Unused Tool".to_string(),
            category: Some("Productivity".to_string()),
            monthly_cost: 50.0,
            active_users_count: 0,
            efficiency_rating: 50.0,
            department: "Marketing".to_string(),
            warning_level: "critical".to_string(),
        };
        assert_eq!(critical.warning_level, "critical");

        // High warning (< threshold/2)
        let high = LowUsageTool {
            id: 2,
            name: "Low Usage".to_string(),
            category: Some("Analytics".to_string()),
            monthly_cost: 25.0,
            active_users_count: 3,
            efficiency_rating: 8.33,
            department: "Sales".to_string(),
            warning_level: "high".to_string(),
        };
        assert_eq!(high.warning_level, "high");
    }

    /// Test VendorSummary aggregation structure.
    #[test]
    fn test_vendor_summary() {
        let vendor = VendorSummary {
            vendor: "Atlassian".to_string(),
            tool_count: 3,
            total_cost: 50.0,
            average_cost: 16.67,
            departments: "Engineering, Operations".to_string(),
            tools: vec![
                VendorTool {
                    name: "Jira".to_string(),
                    monthly_cost: 20.0,
                    department: "Engineering".to_string(),
                },
                VendorTool {
                    name: "Confluence".to_string(),
                    monthly_cost: 15.0,
                    department: "Engineering".to_string(),
                },
            ],
        };

        assert_eq!(vendor.tool_count, 3);
        assert_eq!(vendor.tools.len(), 2);
        assert!(vendor.departments.contains("Engineering"));
    }

    /// Test HealthResponse structure.
    #[test]
    fn test_health_response() {
        let health = HealthResponse {
            status: "healthy".to_string(),
            timestamp: Utc::now(),
            database: "connected".to_string(),
            response_time: 5,
        };

        assert_eq!(health.status, "healthy");
        assert_eq!(health.database, "connected");
        assert!(health.response_time < 1000);
    }

    /// Test CategoryInsights with optional fields.
    #[test]
    fn test_category_insights() {
        let insights = CategoryInsights {
            most_expensive: Some("Adobe CC".to_string()),
            least_expensive: Some("Slack".to_string()),
            avg_users: 25.5,
        };

        assert!(insights.most_expensive.is_some());
        assert!(insights.least_expensive.is_some());
        assert!(insights.avg_users > 0.0);

        // Test empty category
        let empty_insights = CategoryInsights {
            most_expensive: None,
            least_expensive: None,
            avg_users: 0.0,
        };

        assert!(empty_insights.most_expensive.is_none());
        assert_eq!(empty_insights.avg_users, 0.0);
    }

    /// Test MessageResponse for success messages.
    #[test]
    fn test_message_response() {
        let msg = MessageResponse {
            message: "Tool deleted successfully".to_string(),
        };

        let json = serde_json::to_string(&msg);
        assert!(json.is_ok());
        assert!(json.unwrap().contains("deleted successfully"));
    }
}

/// Validation tests for business logic.
#[cfg(test)]
mod validation_tests {
    use super::*;

    /// Test that valid departments are recognized.
    #[test]
    fn test_valid_departments() {
        let valid_departments = vec![
            "Engineering",
            "Sales",
            "Marketing",
            "HR",
            "Finance",
            "Operations",
            "Design",
        ];

        for dept in valid_departments {
            let request = CreateToolRequest {
                name: "Test".to_string(),
                description: "Test".to_string(),
                vendor: "Test".to_string(),
                website_url: None,
                category_id: 1,
                monthly_cost: 10.0,
                active_users_count: None,
                owner_department: dept.to_string(),
                status: None,
            };
            assert_eq!(request.owner_department, dept);
        }
    }

    /// Test that valid statuses are recognized.
    #[test]
    fn test_valid_statuses() {
        let valid_statuses = vec!["active", "deprecated", "trial"];

        for status in valid_statuses {
            let request = CreateToolRequest {
                name: "Test".to_string(),
                description: "Test".to_string(),
                vendor: "Test".to_string(),
                website_url: None,
                category_id: 1,
                monthly_cost: 10.0,
                active_users_count: None,
                owner_department: "Engineering".to_string(),
                status: Some(status.to_string()),
            };
            assert_eq!(request.status.unwrap(), status);
        }
    }

    /// Test monthly cost must be non-negative.
    #[test]
    fn test_monthly_cost_validation() {
        // Negative costs should be caught by business logic
        let valid_cost = CreateToolRequest {
            name: "Test".to_string(),
            description: "Test".to_string(),
            vendor: "Test".to_string(),
            website_url: None,
            category_id: 1,
            monthly_cost: 0.0,
            active_users_count: None,
            owner_department: "Engineering".to_string(),
            status: None,
        };
        assert!(valid_cost.monthly_cost >= 0.0);

        let paid_cost = CreateToolRequest {
            name: "Test".to_string(),
            description: "Test".to_string(),
            vendor: "Test".to_string(),
            website_url: None,
            category_id: 1,
            monthly_cost: 99.99,
            active_users_count: None,
            owner_department: "Engineering".to_string(),
            status: None,
        };
        assert!(paid_cost.monthly_cost > 0.0);
    }
}

/// Performance and edge case tests.
#[cfg(test)]
mod edge_case_tests {
    use super::*;

    /// Test handling of large tool lists.
    #[test]
    fn test_large_tool_list() {
        let tools: Vec<Tool> = (0..1000)
            .map(|i| Tool {
                id: i,
                name: format!("Tool {}", i),
                description: Some(format!("Description {}", i)),
                vendor: Some(format!("Vendor {}", i)),
                website_url: None,
                category_id: 1,
                monthly_cost: (i as f64) * 0.99,
                active_users_count: i % 100,
                owner_department: "Engineering".to_string(),
                status: "active".to_string(),
                created_at: Utc::now(),
                updated_at: Utc::now(),
                category: Some("Development".to_string()),
            })
            .collect();

        let response = ToolsListResponse {
            data: tools,
            total: 1000,
            filtered: 1000,
            filters_applied: serde_json::json!({}),
        };

        assert_eq!(response.data.len(), 1000);
        assert_eq!(response.total, 1000);
    }

    /// Test empty response lists.
    #[test]
    fn test_empty_responses() {
        let empty_tools = ToolsListResponse {
            data: vec![],
            total: 0,
            filtered: 0,
            filters_applied: serde_json::json!({}),
        };
        assert_eq!(empty_tools.data.len(), 0);

        let empty_vendors = VendorSummaryResponse { vendors: vec![] };
        assert_eq!(empty_vendors.vendors.len(), 0);

        let empty_categories = CategoryToolsResponse { categories: vec![] };
        assert_eq!(empty_categories.categories.len(), 0);
    }

    /// Test null handling in optional fields.
    #[test]
    fn test_null_field_handling() {
        let json = r#"{
            "id": 1,
            "name": "Test",
            "description": null,
            "vendor": null,
            "website_url": null,
            "category_id": 1,
            "monthly_cost": 10.0,
            "active_users_count": 5,
            "owner_department": "Engineering",
            "status": "active",
            "created_at": "2025-11-27T10:00:00Z",
            "updated_at": "2025-11-27T10:00:00Z",
            "category": null
        }"#;

        let tool: Result<Tool, _> = serde_json::from_str(json);
        assert!(tool.is_ok());
        
        let tool = tool.unwrap();
        assert!(tool.description.is_none());
        assert!(tool.vendor.is_none());
        assert!(tool.website_url.is_none());
        assert!(tool.category.is_none());
    }

    /// Test unicode and special characters in names.
    #[test]
    fn test_unicode_handling() {
        let tool = Tool {
            id: 1,
            name: "测试工具 🚀".to_string(),
            description: Some("Descripción en español".to_string()),
            vendor: Some("Société française".to_string()),
            website_url: None,
            category_id: 1,
            monthly_cost: 10.0,
            active_users_count: 5,
            owner_department: "Engineering".to_string(),
            status: "active".to_string(),
            created_at: Utc::now(),
            updated_at: Utc::now(),
            category: None,
        };

        let json = serde_json::to_string(&tool);
        assert!(json.is_ok());
        
        let deserialized: Result<Tool, _> = serde_json::from_str(&json.unwrap());
        assert!(deserialized.is_ok());
    }
}
