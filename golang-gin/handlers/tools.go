package handlers

import (
	"database/sql"
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/models"
	"github.com/gin-gonic/gin"
)

// GetTools godoc
// @Summary Get all tools
// @Description Get list of tools with optional filters
// @Tags tools
// @Produce json
// @Param status query string false "Filter by status (active, deprecated, trial)"
// @Param category_id query int false "Filter by category ID"
// @Param vendor query string false "Filter by vendor"
// @Param search query string false "Search in tool name"
// @Param limit query int false "Number of results per page" default(50)
// @Param skip query int false "Number of results to skip" default(0)
// @Success 200 {object} models.ToolsListResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /tools [get]
func GetTools(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		// Parse query parameters
		status := c.Query("status")
		categoryID := c.Query("category_id")
		vendor := c.Query("vendor")
		search := c.Query("search")
		limit, _ := strconv.Atoi(c.DefaultQuery("limit", "50"))
		skip, _ := strconv.Atoi(c.DefaultQuery("skip", "0"))

		// Build query
		query := `
			SELECT t.id, t.name, t.description, t.vendor, t.website_url, 
			       t.category_id, t.monthly_cost, t.active_users_count, 
			       t.owner_department, t.status, t.created_at, t.updated_at,
			       c.name as category
			FROM tools t
			LEFT JOIN categories c ON t.category_id = c.id
			WHERE 1=1`
		
		countQuery := "SELECT COUNT(*) FROM tools t WHERE 1=1"
		args := []interface{}{}
		argCount := 0
		filtersApplied := make(map[string]interface{})

		// Add filters
		if status != "" {
			argCount++
			query += fmt.Sprintf(" AND t.status = $%d", argCount)
			countQuery += fmt.Sprintf(" AND t.status = $%d", argCount)
			args = append(args, status)
			filtersApplied["status"] = status
		}

		if categoryID != "" {
			argCount++
			query += fmt.Sprintf(" AND t.category_id = $%d", argCount)
			countQuery += fmt.Sprintf(" AND t.category_id = $%d", argCount)
			catID, _ := strconv.Atoi(categoryID)
			args = append(args, catID)
			filtersApplied["category_id"] = catID
		}

		if vendor != "" {
			argCount++
			query += fmt.Sprintf(" AND t.vendor ILIKE $%d", argCount)
			countQuery += fmt.Sprintf(" AND t.vendor ILIKE $%d", argCount)
			args = append(args, "%"+vendor+"%")
			filtersApplied["vendor"] = vendor
		}

		if search != "" {
			argCount++
			query += fmt.Sprintf(" AND t.name ILIKE $%d", argCount)
			countQuery += fmt.Sprintf(" AND t.name ILIKE $%d", argCount)
			args = append(args, "%"+search+"%")
			filtersApplied["search"] = search
		}

		// Get total count
		var total int
		err := db.QueryRow(countQuery, args...).Scan(&total)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to count tools",
				Message: err.Error(),
			})
			return
		}

		// Add pagination
		query += fmt.Sprintf(" ORDER BY t.id LIMIT $%d OFFSET $%d", argCount+1, argCount+2)
		args = append(args, limit, skip)

		// Execute query
		rows, err := db.Query(query, args...)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to fetch tools",
				Message: err.Error(),
			})
			return
		}
		defer rows.Close()

	tools := []models.Tool{}
	for rows.Next() {
		var tool models.Tool
		var description, vendor sql.NullString
		err := rows.Scan(
			&tool.ID, &tool.Name, &description, &vendor,
			&tool.WebsiteURL, &tool.CategoryID, &tool.MonthlyCost,
			&tool.ActiveUsersCount, &tool.OwnerDepartment, &tool.Status,
			&tool.CreatedAt, &tool.UpdatedAt, &tool.Category,
		)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to scan tool",
				Message: err.Error(),
			})
			return
		}
		if description.Valid {
			tool.Description = &description.String
		}
		if vendor.Valid {
			tool.Vendor = &vendor.String
		}
		tools = append(tools, tool)
	}

	response := models.ToolsListResponse{
		Data:           tools,
		Total:          total,
		Filtered:       len(tools),
		FiltersApplied: filtersApplied,
	}

	c.JSON(http.StatusOK, response)
	}
}

// GetTool godoc
// @Summary Get tool by ID
// @Description Get a single tool by its ID
// @Tags tools
// @Produce json
// @Param id path int true "Tool ID"
// @Success 200 {object} models.Tool
// @Failure 404 {object} models.ErrorResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /tools/{id} [get]
func GetTool(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Param("id")

		query := `
			SELECT t.id, t.name, t.description, t.vendor, t.website_url, 
			       t.category_id, t.monthly_cost, t.active_users_count, 
			       t.owner_department, t.status, t.created_at, t.updated_at,
			       c.name as category
			FROM tools t
			LEFT JOIN categories c ON t.category_id = c.id
			WHERE t.id = $1`

	var tool models.Tool
	var description, vendor sql.NullString
	err := db.QueryRow(query, id).Scan(
		&tool.ID, &tool.Name, &description, &vendor,
		&tool.WebsiteURL, &tool.CategoryID, &tool.MonthlyCost,
		&tool.ActiveUsersCount, &tool.OwnerDepartment, &tool.Status,
		&tool.CreatedAt, &tool.UpdatedAt, &tool.Category,
	)
	if description.Valid {
		tool.Description = &description.String
	}
	if vendor.Valid {
		tool.Vendor = &vendor.String
	}
	
	if err == sql.ErrNoRows {
		c.JSON(http.StatusNotFound, models.ErrorResponse{
			Error:   "Tool not found",
			Message: fmt.Sprintf("Tool with ID %s does not exist", id),
		})
		return
	}

		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to fetch tool",
				Message: err.Error(),
			})
			return
		}

		c.JSON(http.StatusOK, tool)
	}
}

// CreateTool godoc
// @Summary Create a new tool
// @Description Create a new tool with the provided information
// @Tags tools
// @Accept json
// @Produce json
// @Param tool body models.CreateToolRequest true "Tool data"
// @Success 201 {object} models.Tool
// @Failure 400 {object} models.ErrorResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /tools [post]
func CreateTool(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		var req models.CreateToolRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, models.ErrorResponse{
				Error:   "Invalid request body",
				Message: err.Error(),
			})
			return
		}

		// Set defaults
		activeUsersCount := 0
		if req.ActiveUsersCount != nil {
			activeUsersCount = *req.ActiveUsersCount
		}

		status := "active"
		if req.Status != nil {
			status = *req.Status
		}

		query := `
			INSERT INTO tools (name, description, vendor, website_url, category_id, 
			                   monthly_cost, active_users_count, owner_department, status)
			VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)
			RETURNING id, created_at, updated_at`

		var tool models.Tool
		err := db.QueryRow(query, req.Name, req.Description, req.Vendor, req.WebsiteURL,
			req.CategoryID, req.MonthlyCost, activeUsersCount, req.OwnerDepartment, status).
			Scan(&tool.ID, &tool.CreatedAt, &tool.UpdatedAt)

		if err != nil {
			if strings.Contains(err.Error(), "duplicate key") {
				c.JSON(http.StatusBadRequest, models.ErrorResponse{
					Error:   "Tool already exists",
					Message: "A tool with this name already exists",
				})
				return
			}
			if strings.Contains(err.Error(), "foreign key") {
				c.JSON(http.StatusBadRequest, models.ErrorResponse{
					Error:   "Invalid category",
					Message: "The specified category does not exist",
				})
				return
			}
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to create tool",
				Message: err.Error(),
			})
			return
		}

	// Populate the response
	tool.Name = req.Name
	tool.Description = &req.Description
	tool.Vendor = &req.Vendor
	tool.WebsiteURL = req.WebsiteURL
	tool.CategoryID = req.CategoryID
	tool.MonthlyCost = req.MonthlyCost
	tool.ActiveUsersCount = activeUsersCount
	tool.OwnerDepartment = req.OwnerDepartment
	tool.Status = status

	// Get category name
	var categoryName string
	db.QueryRow("SELECT name FROM categories WHERE id = $1", req.CategoryID).Scan(&categoryName)
	tool.Category = &categoryName

	c.JSON(http.StatusCreated, tool)
	}
}

// UpdateTool godoc
// @Summary Update a tool
// @Description Update an existing tool with partial data
// @Tags tools
// @Accept json
// @Produce json
// @Param id path int true "Tool ID"
// @Param tool body models.UpdateToolRequest true "Tool data to update"
// @Success 200 {object} models.Tool
// @Failure 400 {object} models.ErrorResponse
// @Failure 404 {object} models.ErrorResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /tools/{id} [put]
func UpdateTool(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Param("id")
		
		var req models.UpdateToolRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, models.ErrorResponse{
				Error:   "Invalid request body",
				Message: err.Error(),
			})
			return
		}

		// Check if tool exists
		var exists bool
		err := db.QueryRow("SELECT EXISTS(SELECT 1 FROM tools WHERE id = $1)", id).Scan(&exists)
		if err != nil || !exists {
			c.JSON(http.StatusNotFound, models.ErrorResponse{
				Error:   "Tool not found",
				Message: fmt.Sprintf("Tool with ID %s does not exist", id),
			})
			return
		}

		// Build dynamic update query
		updates := []string{}
		args := []interface{}{}
		argCount := 0

		if req.Name != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("name = $%d", argCount))
			args = append(args, *req.Name)
		}
		if req.Description != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("description = $%d", argCount))
			args = append(args, *req.Description)
		}
		if req.Vendor != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("vendor = $%d", argCount))
			args = append(args, *req.Vendor)
		}
		if req.WebsiteURL != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("website_url = $%d", argCount))
			args = append(args, *req.WebsiteURL)
		}
		if req.CategoryID != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("category_id = $%d", argCount))
			args = append(args, *req.CategoryID)
		}
		if req.MonthlyCost != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("monthly_cost = $%d", argCount))
			args = append(args, *req.MonthlyCost)
		}
		if req.ActiveUsersCount != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("active_users_count = $%d", argCount))
			args = append(args, *req.ActiveUsersCount)
		}
		if req.OwnerDepartment != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("owner_department = $%d", argCount))
			args = append(args, *req.OwnerDepartment)
		}
		if req.Status != nil {
			argCount++
			updates = append(updates, fmt.Sprintf("status = $%d", argCount))
			args = append(args, *req.Status)
		}

		if len(updates) == 0 {
			c.JSON(http.StatusBadRequest, models.ErrorResponse{
				Error:   "No fields to update",
				Message: "At least one field must be provided",
			})
			return
		}

		// Add updated_at
		argCount++
		updates = append(updates, fmt.Sprintf("updated_at = $%d", argCount))
		args = append(args, "NOW()")

		// Add ID for WHERE clause
		argCount++
		args = append(args, id)

		query := fmt.Sprintf("UPDATE tools SET %s WHERE id = $%d", strings.Join(updates, ", "), argCount)
		
		_, err = db.Exec(query, args...)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to update tool",
				Message: err.Error(),
			})
			return
		}

		// Fetch and return updated tool
		GetTool(db)(c)
	}
}

// DeleteTool godoc
// @Summary Delete a tool
// @Description Delete a tool by its ID
// @Tags tools
// @Param id path int true "Tool ID"
// @Success 204 "No Content"
// @Failure 404 {object} models.ErrorResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /tools/{id} [delete]
func DeleteTool(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Param("id")

		result, err := db.Exec("DELETE FROM tools WHERE id = $1", id)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ErrorResponse{
				Error:   "Failed to delete tool",
				Message: err.Error(),
			})
			return
		}

		rowsAffected, _ := result.RowsAffected()
		if rowsAffected == 0 {
			c.JSON(http.StatusNotFound, models.ErrorResponse{
				Error:   "Tool not found",
				Message: fmt.Sprintf("Tool with ID %s does not exist", id),
			})
			return
		}

		c.Status(http.StatusNoContent)
	}
}
