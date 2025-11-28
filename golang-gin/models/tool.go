package models

import "time"

// Tool represents a software tool in the system
type Tool struct {
	ID               int       `json:"id"`
	Name             string    `json:"name" binding:"required,min=2,max=100"`
	Description      *string   `json:"description" binding:"required"`
	Vendor           *string   `json:"vendor" binding:"required,max=100"`
	WebsiteURL       *string   `json:"website_url"`
	CategoryID       int       `json:"category_id" binding:"required"`
	MonthlyCost      float64   `json:"monthly_cost" binding:"required,min=0"`
	ActiveUsersCount int       `json:"active_users_count"`
	OwnerDepartment  string    `json:"owner_department" binding:"required,oneof=Engineering Sales Marketing HR Finance Operations Design"`
	Status           string    `json:"status" binding:"omitempty,oneof=active deprecated trial"`
	CreatedAt        time.Time `json:"created_at"`
	UpdatedAt        time.Time `json:"updated_at"`
	Category         *string   `json:"category,omitempty"`
}

// CreateToolRequest represents the request body for creating a tool
type CreateToolRequest struct {
	Name             string  `json:"name" binding:"required,min=2,max=100"`
	Description      string  `json:"description" binding:"required"`
	Vendor           string  `json:"vendor" binding:"required,max=100"`
	WebsiteURL       *string `json:"website_url" binding:"omitempty,url"`
	CategoryID       int     `json:"category_id" binding:"required"`
	MonthlyCost      float64 `json:"monthly_cost" binding:"required,min=0"`
	ActiveUsersCount *int    `json:"active_users_count" binding:"omitempty,min=0"`
	OwnerDepartment  string  `json:"owner_department" binding:"required,oneof=Engineering Sales Marketing HR Finance Operations Design"`
	Status           *string `json:"status" binding:"omitempty,oneof=active deprecated trial"`
}

// UpdateToolRequest represents the request body for updating a tool
type UpdateToolRequest struct {
	Name             *string  `json:"name" binding:"omitempty,min=2,max=100"`
	Description      *string  `json:"description"`
	Vendor           *string  `json:"vendor" binding:"omitempty,max=100"`
	WebsiteURL       *string  `json:"website_url" binding:"omitempty,url"`
	CategoryID       *int     `json:"category_id"`
	MonthlyCost      *float64 `json:"monthly_cost" binding:"omitempty,min=0"`
	ActiveUsersCount *int     `json:"active_users_count" binding:"omitempty,min=0"`
	OwnerDepartment  *string  `json:"owner_department" binding:"omitempty,oneof=Engineering Sales Marketing HR Finance Operations Design"`
	Status           *string  `json:"status" binding:"omitempty,oneof=active deprecated trial"`
}

// ToolsListResponse represents the response for listing tools
type ToolsListResponse struct {
	Data           []Tool                 `json:"data"`
	Total          int                    `json:"total"`
	Filtered       int                    `json:"filtered"`
	FiltersApplied map[string]interface{} `json:"filters_applied"`
}

// ErrorResponse represents an error response
type ErrorResponse struct {
	Error   string `json:"error"`
	Message string `json:"message,omitempty"`
}

// HealthResponse represents the health check response
type HealthResponse struct {
	Status       string `json:"status"`
	Timestamp    string `json:"timestamp"`
	Database     string `json:"database"`
	ResponseTime int64  `json:"responseTime"`
}
