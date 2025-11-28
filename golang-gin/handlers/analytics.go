package handlers

import (
	"database/sql"
	"math"
	"net/http"
	"strconv"

	"github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/models"
	"github.com/gin-gonic/gin"
)

// GetDepartmentCosts godoc
// @Summary Get department costs analysis
// @Description Get cost breakdown by department with aggregations
// @Tags analytics
// @Produce json
// @Param year query int false "Year filter"
// @Param month query int false "Month filter"
// @Success 200 {object} models.DepartmentCostsResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /analytics/department-costs [get]
func GetDepartmentCosts(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		query := `
			SELECT 
				owner_department as department,
				SUM(monthly_cost) as total_cost,
				COUNT(*) as tools_count,
				SUM(active_users_count) as total_users,
				AVG(monthly_cost) as average_cost_per_tool
			FROM tools
			WHERE status = 'active'
			GROUP BY owner_department
			ORDER BY total_cost DESC`

		rows, err := db.Query(query)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to fetch department costs",
				Message: err.Error(),
			})
			return
		}
		defer rows.Close()

		data := []models.DepartmentCost{}
		var totalCompanyCost float64
		var maxCost float64
		var mostExpensiveDepartment string

		for rows.Next() {
			var dept models.DepartmentCost
			err := rows.Scan(&dept.Department, &dept.TotalCost, &dept.ToolsCount,
				&dept.TotalUsers, &dept.AverageCostPerTool)
			if err != nil {
				c.JSON(http.StatusInternalServerError, models.ErrorResponse{
					Error:   "Failed to scan department cost",
					Message: err.Error(),
				})
				return
			}

			dept.AverageCostPerTool = roundToTwo(dept.AverageCostPerTool)
			totalCompanyCost += dept.TotalCost

			if dept.TotalCost > maxCost {
				maxCost = dept.TotalCost
				mostExpensiveDepartment = dept.Department
			}

			data = append(data, dept)
		}

		// Calculate percentages
		for i := range data {
			if totalCompanyCost > 0 {
				data[i].CostPercentage = roundToOne((data[i].TotalCost / totalCompanyCost) * 100)
			}
		}

		response := models.DepartmentCostsResponse{
			Data: data,
			Summary: models.DepartmentCostsSummary{
				TotalCompanyCost:        roundToTwo(totalCompanyCost),
				DepartmentsCount:        len(data),
				MostExpensiveDepartment: mostExpensiveDepartment,
			},
		}

		c.JSON(http.StatusOK, response)
	}
}

// GetExpensiveTools godoc
// @Summary Get most expensive tools
// @Description Get list of most expensive tools with efficiency ratings
// @Tags analytics
// @Produce json
// @Param limit query int false "Number of tools to return" default(10)
// @Success 200 {object} models.ExpensiveToolsResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /analytics/expensive-tools [get]
func GetExpensiveTools(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		limit, _ := strconv.Atoi(c.DefaultQuery("limit", "10"))
		if limit > 50 {
			limit = 50
		}

		// First, get company average cost per user
		var avgCostPerUser sql.NullFloat64
		avgQuery := `
			SELECT 
				CASE 
					WHEN SUM(active_users_count) > 0 
					THEN SUM(monthly_cost) / SUM(active_users_count)
					ELSE 0 
				END as avg_cost_per_user
			FROM tools
			WHERE status = 'active' AND active_users_count > 0`

		db.QueryRow(avgQuery).Scan(&avgCostPerUser)
		companyAvg := 0.0
		if avgCostPerUser.Valid {
			companyAvg = avgCostPerUser.Float64
		}

		// Get expensive tools
		query := `
			SELECT 
				id, name, monthly_cost, active_users_count,
				owner_department, vendor
			FROM tools
			WHERE status = 'active'
			ORDER BY monthly_cost DESC
			LIMIT $1`

		rows, err := db.Query(query, limit)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to fetch expensive tools",
				Message: err.Error(),
			})
			return
		}
		defer rows.Close()

		data := []models.ExpensiveTool{}
		var totalTools int
		var potentialSavings float64

		for rows.Next() {
			var tool models.ExpensiveTool
			err := rows.Scan(&tool.ID, &tool.Name, &tool.MonthlyCost,
				&tool.ActiveUsersCount, &tool.Department, &tool.Vendor)
			if err != nil {
				c.JSON(http.StatusInternalServerError, models.ErrorResponse{
					Error:   "Failed to scan expensive tool",
					Message: err.Error(),
				})
				return
			}

			// Calculate cost per user
			if tool.ActiveUsersCount > 0 {
				tool.CostPerUser = roundToTwo(tool.MonthlyCost / float64(tool.ActiveUsersCount))
			} else {
				tool.CostPerUser = tool.MonthlyCost
			}

			// Assign efficiency rating
			if tool.ActiveUsersCount == 0 || (companyAvg > 0 && tool.CostPerUser > companyAvg*1.2) {
				tool.EfficiencyRating = "low"
				potentialSavings += tool.MonthlyCost
			} else if companyAvg > 0 && tool.CostPerUser >= companyAvg*0.8 {
				tool.EfficiencyRating = "average"
			} else if companyAvg > 0 && tool.CostPerUser >= companyAvg*0.5 {
				tool.EfficiencyRating = "good"
			} else {
				tool.EfficiencyRating = "excellent"
			}

			data = append(data, tool)
			totalTools++
		}

		response := models.ExpensiveToolsResponse{
			Data: data,
			Analysis: models.ExpensiveToolsAnalysis{
				TotalToolsAnalyzed:        totalTools,
				AvgCostPerUserCompany:     roundToTwo(companyAvg),
				PotentialSavingsIdentified: roundToTwo(potentialSavings),
			},
		}

		c.JSON(http.StatusOK, response)
	}
}

// GetToolsByCategory godoc
// @Summary Get tools grouped by category
// @Description Get aggregated tool data by category with insights
// @Tags analytics
// @Produce json
// @Success 200 {object} models.ToolsByCategoryResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /analytics/tools-by-category [get]
func GetToolsByCategory(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		query := `
			SELECT 
				COALESCE(c.name, 'Uncategorized') as category_name,
				COUNT(t.id) as tools_count,
				SUM(t.monthly_cost) as total_cost,
				SUM(t.active_users_count) as total_users
			FROM tools t
			LEFT JOIN categories c ON t.category_id = c.id
			WHERE t.status = 'active'
			GROUP BY c.name
			ORDER BY total_cost DESC`

		rows, err := db.Query(query)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to fetch tools by category",
				Message: err.Error(),
			})
			return
		}
		defer rows.Close()

		data := []models.CategoryTools{}
		var totalCompanyCost float64
		var maxCost float64
		var mostExpensiveCategory string
		var minAvgCostPerUser float64 = math.MaxFloat64
		var mostEfficientCategory string

		for rows.Next() {
			var cat models.CategoryTools
			err := rows.Scan(&cat.CategoryName, &cat.ToolsCount, &cat.TotalCost, &cat.TotalUsers)
			if err != nil {
				c.JSON(http.StatusInternalServerError, models.ErrorResponse{
					Error:   "Failed to scan category tools",
					Message: err.Error(),
				})
				return
			}

			// Calculate average cost per user
			if cat.TotalUsers > 0 {
				cat.AverageCostPerUser = roundToTwo(cat.TotalCost / float64(cat.TotalUsers))
				if cat.AverageCostPerUser < minAvgCostPerUser {
					minAvgCostPerUser = cat.AverageCostPerUser
					mostEfficientCategory = cat.CategoryName
				}
			}

			totalCompanyCost += cat.TotalCost

			if cat.TotalCost > maxCost {
				maxCost = cat.TotalCost
				mostExpensiveCategory = cat.CategoryName
			}

			data = append(data, cat)
		}

		// Calculate percentages
		for i := range data {
			if totalCompanyCost > 0 {
				data[i].PercentageOfBudget = roundToOne((data[i].TotalCost / totalCompanyCost) * 100)
			}
		}

		response := models.ToolsByCategoryResponse{
			Data: data,
			Insights: models.CategoryToolsInsights{
				MostExpensiveCategory: mostExpensiveCategory,
				MostEfficientCategory: mostEfficientCategory,
			},
		}

		c.JSON(http.StatusOK, response)
	}
}

// Helper functions
func roundToTwo(val float64) float64 {
	return math.Round(val*100) / 100
}

func roundToOne(val float64) float64 {
	return math.Round(val*10) / 10
}
