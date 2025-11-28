package main

import (
	"log"
	"os"

	"github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/config"
	"github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/handlers"
	"github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/middleware"
	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"
	
	swaggerFiles "github.com/swaggo/files"
	ginSwagger "github.com/swaggo/gin-swagger"
	_ "github.com/FCHEHIDI/API_Internal_Tools_Management/golang-gin/docs"
)

// @title Internal Tools Management API
// @version 1.0
// @description API for managing internal software tools, subscriptions, and analytics
// @termsOfService http://swagger.io/terms/

// @contact.name API Support
// @contact.email support@techcorp.com

// @license.name MIT
// @license.url https://opensource.org/licenses/MIT

// @host localhost:8000
// @BasePath /api

func main() {
	// Load environment variables
	if err := godotenv.Load(); err != nil {
		log.Println("Warning: .env file not found, using system environment variables")
	}

	// Initialize database
	db, err := config.InitDB()
	if err != nil {
		log.Fatal("Failed to connect to database:", err)
	}
	defer db.Close()

	// Set Gin mode
	if os.Getenv("GIN_MODE") == "release" {
		gin.SetMode(gin.ReleaseMode)
	}

	// Create router
	router := gin.Default()

	// CORS middleware
	router.Use(cors.New(cors.Config{
		AllowOrigins:     []string{"*"},
		AllowMethods:     []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
		AllowHeaders:     []string{"Origin", "Content-Type", "Accept", "Authorization"},
		ExposeHeaders:    []string{"Content-Length"},
		AllowCredentials: true,
	}))

	// Request logging middleware
	router.Use(middleware.Logger())

	// API routes
	api := router.Group("/api")
	{
		// Health check
		api.GET("/health", handlers.HealthCheck(db))

		// Tools endpoints
		tools := api.Group("/tools")
		{
			tools.GET("", handlers.GetTools(db))
			tools.GET("/:id", handlers.GetTool(db))
			tools.POST("", handlers.CreateTool(db))
			tools.PUT("/:id", handlers.UpdateTool(db))
			tools.DELETE("/:id", handlers.DeleteTool(db))
		}

		// Analytics endpoints
		analytics := api.Group("/analytics")
		{
			analytics.GET("/department-costs", handlers.GetDepartmentCosts(db))
			analytics.GET("/expensive-tools", handlers.GetExpensiveTools(db))
			analytics.GET("/tools-by-category", handlers.GetToolsByCategory(db))
			analytics.GET("/low-usage-tools", handlers.GetLowUsageTools(db))
			analytics.GET("/vendor-summary", handlers.GetVendorSummary(db))
		}
	}

	// Swagger documentation
	router.GET("/docs/*any", ginSwagger.WrapHandler(swaggerFiles.Handler))

	// Start server
	port := os.Getenv("PORT")
	if port == "" {
		port = "8000"
	}

	log.Printf("🚀 Server starting on port %s", port)
	log.Printf("📚 Swagger docs available at http://localhost:%s/docs/index.html", port)
	
	if err := router.Run(":" + port); err != nil {
		log.Fatal("Failed to start server:", err)
	}
}
