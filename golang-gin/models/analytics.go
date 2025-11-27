package models

// DepartmentCost represents cost analysis by department
type DepartmentCost struct {
	Department         string  `json:"department"`
	TotalCost          float64 `json:"total_cost"`
	ToolsCount         int     `json:"tools_count"`
	TotalUsers         int     `json:"total_users"`
	AverageCostPerTool float64 `json:"average_cost_per_tool"`
	CostPercentage     float64 `json:"cost_percentage"`
}

// DepartmentCostsResponse represents the full response for department costs
type DepartmentCostsResponse struct {
	Data    []DepartmentCost          `json:"data"`
	Summary DepartmentCostsSummary    `json:"summary"`
}

// DepartmentCostsSummary represents summary data for department costs
type DepartmentCostsSummary struct {
	TotalCompanyCost        float64 `json:"total_company_cost"`
	DepartmentsCount        int     `json:"departments_count"`
	MostExpensiveDepartment string  `json:"most_expensive_department"`
}

// ExpensiveTool represents an expensive tool with efficiency analysis
type ExpensiveTool struct {
	ID               int     `json:"id"`
	Name             string  `json:"name"`
	MonthlyCost      float64 `json:"monthly_cost"`
	ActiveUsersCount int     `json:"active_users_count"`
	CostPerUser      float64 `json:"cost_per_user"`
	Department       string  `json:"department"`
	Vendor           string  `json:"vendor"`
	EfficiencyRating string  `json:"efficiency_rating"`
}

// ExpensiveToolsResponse represents the response for expensive tools
type ExpensiveToolsResponse struct {
	Data     []ExpensiveTool        `json:"data"`
	Analysis ExpensiveToolsAnalysis `json:"analysis"`
}

// ExpensiveToolsAnalysis represents analysis data for expensive tools
type ExpensiveToolsAnalysis struct {
	TotalToolsAnalyzed        int     `json:"total_tools_analyzed"`
	AvgCostPerUserCompany     float64 `json:"avg_cost_per_user_company"`
	PotentialSavingsIdentified float64 `json:"potential_savings_identified"`
}

// CategoryTools represents tools grouped by category
type CategoryTools struct {
	CategoryName       string  `json:"category_name"`
	ToolsCount         int     `json:"tools_count"`
	TotalCost          float64 `json:"total_cost"`
	TotalUsers         int     `json:"total_users"`
	PercentageOfBudget float64 `json:"percentage_of_budget"`
	AverageCostPerUser float64 `json:"average_cost_per_user"`
}

// ToolsByCategoryResponse represents the response for tools by category
type ToolsByCategoryResponse struct {
	Data     []CategoryTools       `json:"data"`
	Insights CategoryToolsInsights `json:"insights"`
}

// CategoryToolsInsights represents insights for tools by category
type CategoryToolsInsights struct {
	MostExpensiveCategory string `json:"most_expensive_category"`
	MostEfficientCategory string `json:"most_efficient_category"`
}

// LowUsageTool represents a tool with low usage
type LowUsageTool struct {
	ID               int     `json:"id"`
	Name             string  `json:"name"`
	MonthlyCost      float64 `json:"monthly_cost"`
	ActiveUsersCount int     `json:"active_users_count"`
	CostPerUser      float64 `json:"cost_per_user"`
	Department       string  `json:"department"`
	Vendor           string  `json:"vendor"`
	WarningLevel     string  `json:"warning_level"`
	PotentialAction  string  `json:"potential_action"`
}

// LowUsageToolsResponse represents the response for low usage tools
type LowUsageToolsResponse struct {
	Data            []LowUsageTool      `json:"data"`
	SavingsAnalysis SavingsAnalysis     `json:"savings_analysis"`
}

// SavingsAnalysis represents savings analysis for low usage tools
type SavingsAnalysis struct {
	TotalUnderutilizedTools  int     `json:"total_underutilized_tools"`
	PotentialMonthlySavings  float64 `json:"potential_monthly_savings"`
	PotentialAnnualSavings   float64 `json:"potential_annual_savings"`
}

// VendorSummary represents a summary of tools by vendor
type VendorSummary struct {
	Vendor             string  `json:"vendor"`
	ToolsCount         int     `json:"tools_count"`
	TotalMonthlyCost   float64 `json:"total_monthly_cost"`
	TotalUsers         int     `json:"total_users"`
	Departments        string  `json:"departments"`
	AverageCostPerUser float64 `json:"average_cost_per_user"`
	VendorEfficiency   string  `json:"vendor_efficiency"`
}

// VendorSummaryResponse represents the response for vendor summary
type VendorSummaryResponse struct {
	Data           []VendorSummary   `json:"data"`
	VendorInsights VendorInsights    `json:"vendor_insights"`
}

// VendorInsights represents insights for vendor summary
type VendorInsights struct {
	MostExpensiveVendor string `json:"most_expensive_vendor"`
	MostEfficientVendor string `json:"most_efficient_vendor"`
	SingleToolVendors   int    `json:"single_tool_vendors"`
}
