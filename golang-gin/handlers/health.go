package handlers

import (
	"database/sql"
	"net/http"
	"time"

	"github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/models"
	"github.com/gin-gonic/gin"
)

// HealthCheck godoc
// @Summary Health check endpoint
// @Description Check if the API and database are operational
// @Tags health
// @Produce json
// @Success 200 {object} models.HealthResponse
// @Failure 500 {object} models.ErrorResponse
// @Router /health [get]
func HealthCheck(db *sql.DB) gin.HandlerFunc {
	return func(c *gin.Context) {
		start := time.Now()
		
		// Test database connection
		err := db.Ping()
		dbStatus := "connected"
		if err != nil {
			dbStatus = "disconnected"
		}

		responseTime := time.Since(start).Milliseconds()

		response := models.HealthResponse{
			Status:       "healthy",
			Timestamp:    time.Now().Format(time.RFC3339),
			Database:     dbStatus,
			ResponseTime: responseTime,
		}

		if err != nil {
			response.Status = "unhealthy"
			c.JSON(http.StatusInternalServerError, response)
			return
		}

		c.JSON(http.StatusOK, response)
	}
}
