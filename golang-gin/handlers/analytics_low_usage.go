package handlers

import (
	"database/sql"
	"net/http"
	"sort"
	"strconv"
	"strings"

	"github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/models"
	"github.com/gin-gonic/gin"
)

// GetLowUsageTools godoc
// @Summary Get tools with low usage
// @Description Get tools with low active user counts and savings analysis
// @Tags analytics
// @Produce json
// @Param year query int false "Year filter"
// @Param month query int false "Month filter"
// @Param threshold query int false "Max active users threshold" default(5)
// @Success 200 {object} models.LowUsageToolsResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /analytics/low-usage-tools [get]
func GetLowUsageTools(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		threshold, _ := strconv.Atoi(c.DefaultQuery("threshold", "5"))

		query := `
			SELECT 
				id, name, monthly_cost, active_users_count,
				owner_department, vendor
			FROM tools
			WHERE status = 'active' AND active_users_count <= $1
			ORDER BY active_users_count ASC, monthly_cost DESC`

		rows, err := db.Query(query, threshold)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to fetch low usage tools",
				Message: err.Error(),
			})
			return
		}
		defer rows.Close()

		data := []models.LowUsageTool{}
		var potentialMonthlySavings float64

		for rows.Next() {
			var tool models.LowUsageTool
			err := rows.Scan(&tool.ID, &tool.Name, &tool.MonthlyCost,
				&tool.ActiveUsersCount, &tool.Department, &tool.Vendor)
			if err != nil {
				c.JSON(http.StatusInternalServerError, models.ErrorResponse{
					Error:   "Failed to scan low usage tool",
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

			// Assign warning level and action
			if tool.ActiveUsersCount == 0 || tool.CostPerUser > 50 {
				tool.WarningLevel = "high"
				tool.PotentialAction = "Consider canceling or downgrading"
				potentialMonthlySavings += tool.MonthlyCost
			} else if tool.CostPerUser >= 20 {
				tool.WarningLevel = "medium"
				tool.PotentialAction = "Review usage and consider optimization"
				potentialMonthlySavings += tool.MonthlyCost
			} else {
				tool.WarningLevel = "low"
				tool.PotentialAction = "Monitor usage trends"
			}

			data = append(data, tool)
		}

		response := models.LowUsageToolsResponse{
			Data: data,
			SavingsAnalysis: models.SavingsAnalysis{
				TotalUnderutilizedTools: len(data),
				PotentialMonthlySavings: roundToTwo(potentialMonthlySavings),
				PotentialAnnualSavings:  roundToTwo(potentialMonthlySavings * 12),
			},
		}

		c.JSON(http.StatusOK, response)
	}
}

// GetVendorSummary godoc
// @Summary Get vendor summary
// @Description Get aggregated data by vendor with efficiency insights
// @Tags analytics
// @Produce json
// @Success 200 {object} models.VendorSummaryResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /analytics/vendor-summary [get]
func GetVendorSummary(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		query := `
			SELECT 
				vendor,
				COUNT(*) as tools_count,
				SUM(monthly_cost) as total_monthly_cost,
				SUM(active_users_count) as total_users,
				STRING_AGG(DISTINCT owner_department, ',' ORDER BY owner_department) as departments
			FROM tools
			WHERE status = 'active'
			GROUP BY vendor
			ORDER BY total_monthly_cost DESC`

		rows, err := db.Query(query)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to fetch vendor summary",
				Message: err.Error(),
			})
			return
		}
		defer rows.Close()

		data := []models.VendorSummary{}
		var maxCost float64
		var mostExpensiveVendor string
		var minAvgCostPerUser float64 = 999999.0
		var mostEfficientVendor string
		var singleToolVendors int

		for rows.Next() {
			var vendor models.VendorSummary
			var depts string
			err := rows.Scan(&vendor.Vendor, &vendor.ToolsCount, &vendor.TotalMonthlyCost,
				&vendor.TotalUsers, &depts)
			if err != nil {
				c.JSON(http.StatusInternalServerError, models.ErrorResponse{
					Error:   "Failed to scan vendor summary",
					Message: err.Error(),
				})
				return
			}

			// Sort departments alphabetically
			deptsArray := strings.Split(depts, ",")
			sort.Strings(deptsArray)
			vendor.Departments = strings.Join(deptsArray, ",")

			// Calculate average cost per user
			if vendor.TotalUsers > 0 {
				vendor.AverageCostPerUser = roundToTwo(vendor.TotalMonthlyCost / float64(vendor.TotalUsers))
				if vendor.AverageCostPerUser < minAvgCostPerUser {
					minAvgCostPerUser = vendor.AverageCostPerUser
					mostEfficientVendor = vendor.Vendor
				}
			}

			// Assign vendor efficiency
			if vendor.TotalUsers == 0 || vendor.AverageCostPerUser > 25 {
				vendor.VendorEfficiency = "poor"
			} else if vendor.AverageCostPerUser > 15 {
				vendor.VendorEfficiency = "average"
			} else if vendor.AverageCostPerUser > 5 {
				vendor.VendorEfficiency = "good"
			} else {
				vendor.VendorEfficiency = "excellent"
			}

			// Track most expensive vendor
			if vendor.TotalMonthlyCost > maxCost {
				maxCost = vendor.TotalMonthlyCost
				mostExpensiveVendor = vendor.Vendor
			}

			// Count single tool vendors
			if vendor.ToolsCount == 1 {
				singleToolVendors++
			}

			data = append(data, vendor)
		}

		response := models.VendorSummaryResponse{
			Data: data,
			VendorInsights: models.VendorInsights{
				MostExpensiveVendor: mostExpensiveVendor,
				MostEfficientVendor: mostEfficientVendor,
				SingleToolVendors:   singleToolVendors,
			},
		}

		c.JSON(http.StatusOK, response)
	}
}
