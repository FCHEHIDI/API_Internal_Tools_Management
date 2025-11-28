package com.techcorp.internaltools.controller;

import com.techcorp.internaltools.dto.analytics.*;
import com.techcorp.internaltools.service.AnalyticsService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.math.BigDecimal;

/**
 * AnalyticsController - REST API endpoints for Analytics (Part 2 requirements)
 * 
 * Base path: /api/analytics
 * 
 * Endpoints (all GET requests):
 * 1. /department-costs     - Budget allocation by department
 * 2. /expensive-tools      - High-cost tools for negotiation prioritization
 * 3. /tools-by-category    - Technology stack analysis
 * 4. /low-usage-tools      - Underutilization identification
 * 5. /vendor-summary       - Vendor consolidation opportunities
 * 
 * Business Context:
 * These endpoints support CFO and IT Director decision-making for:
 * - Cost optimization (identify €200k+ savings opportunities)
 * - Vendor negotiation prioritization
 * - Underutilized tool cancellation
 * - Budget allocation analysis
 */
@RestController
@RequestMapping("/api/analytics")
@RequiredArgsConstructor
@Tag(name = "Analytics", description = "Analytics and reporting endpoints for cost optimization")
public class AnalyticsController {

    private final AnalyticsService analyticsService;

    @GetMapping("/department-costs")
    @Operation(summary = "Get department cost analysis", 
               description = "Returns cost breakdown by department with metrics and percentages")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Successfully retrieved department costs"),
        @ApiResponse(responseCode = "400", description = "Invalid parameters")
    })
    public ResponseEntity<DepartmentCostsResponse> getDepartmentCosts(
        @Parameter(description = "Sort by field (total_cost or department)") 
        @RequestParam(name = "sort_by", required = false, defaultValue = "department") String sortBy,
        
        @Parameter(description = "Sort order (asc or desc)") 
        @RequestParam(required = false, defaultValue = "desc") String order
    ) {
        DepartmentCostsResponse response = analyticsService.getDepartmentCosts(sortBy, order);
        return ResponseEntity.ok(response);
    }

    @GetMapping("/expensive-tools")
    @Operation(summary = "Get expensive tools analysis", 
               description = "Returns the most expensive tools with efficiency ratings and potential savings")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Successfully retrieved expensive tools"),
        @ApiResponse(responseCode = "400", description = "Invalid parameters")
    })
    public ResponseEntity<ExpensiveToolsResponse> getExpensiveTools(
        @Parameter(description = "Maximum number of tools to return (1-100)") 
        @RequestParam(required = false, defaultValue = "10") Integer limit,
        
        @Parameter(description = "Minimum cost filter") 
        @RequestParam(name = "min_cost", required = false) BigDecimal minCost
    ) {
        if (limit != null && (limit < 1 || limit > 100)) {
            throw new IllegalArgumentException("Limit must be between 1 and 100");
        }
        
        ExpensiveToolsResponse response = analyticsService.getExpensiveTools(limit, minCost);
        return ResponseEntity.ok(response);
    }

    @GetMapping("/tools-by-category")
    @Operation(summary = "Get tools by category analysis", 
               description = "Returns tool distribution and costs grouped by category")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Successfully retrieved category analysis")
    })
    public ResponseEntity<ToolsByCategoryResponse> getToolsByCategory() {
        ToolsByCategoryResponse response = analyticsService.getToolsByCategory();
        return ResponseEntity.ok(response);
    }

    @GetMapping("/low-usage-tools")
    @Operation(summary = "Get low usage tools analysis", 
               description = "Returns underutilized tools with warning levels and potential savings")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Successfully retrieved low usage tools"),
        @ApiResponse(responseCode = "400", description = "Invalid parameters")
    })
    public ResponseEntity<LowUsageToolsResponse> getLowUsageTools(
        @Parameter(description = "Maximum active users threshold (default: 5)") 
        @RequestParam(name = "max_users", required = false, defaultValue = "5") Integer maxUsers
    ) {
        if (maxUsers != null && maxUsers < 0) {
            throw new IllegalArgumentException("max_users must be non-negative");
        }
        
        LowUsageToolsResponse response = analyticsService.getLowUsageTools(maxUsers);
        return ResponseEntity.ok(response);
    }

    @GetMapping("/vendor-summary")
    @Operation(summary = "Get vendor summary analysis", 
               description = "Returns analytics grouped by vendor with efficiency ratings and consolidation opportunities")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Successfully retrieved vendor summary")
    })
    public ResponseEntity<VendorSummaryResponse> getVendorSummary() {
        VendorSummaryResponse response = analyticsService.getVendorSummary();
        return ResponseEntity.ok(response);
    }
}
