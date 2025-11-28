package com.techcorp.internaltools.controller;

import com.techcorp.internaltools.dto.*;
import com.techcorp.internaltools.model.Department;
import com.techcorp.internaltools.model.ToolStatus;
import com.techcorp.internaltools.service.ToolService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.math.BigDecimal;

/**
 * ToolController - REST API endpoints for Tool CRUD operations (Part 1 requirements)
 * 
 * Base path: /api/tools
 * 
 * Endpoints:
 * - GET    /api/tools          - List all tools with optional filters
 * - GET    /api/tools/{id}     - Get single tool by ID
 * - POST   /api/tools          - Create new tool
 * - PUT    /api/tools/{id}     - Update existing tool (partial update)
 * - DELETE /api/tools/{id}     - Delete tool
 * 
 * All endpoints return JSON responses
 * Validation errors return 400 with field-level details
 * Missing resources return 404
 */
@RestController  // Combines @Controller + @ResponseBody (automatic JSON serialization)
@RequestMapping("/api/tools")  // Base path for all endpoints
@RequiredArgsConstructor  // Lombok: constructor injection for dependencies
@Tag(name = "Tools", description = "CRUD operations for managing tools")  // OpenAPI grouping
public class ToolController {

    private final ToolService toolService;

    @GetMapping
    @Operation(summary = "Get all tools with optional filters", 
               description = "Returns a filtered list of tools based on department, status, category, and cost range")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Successfully retrieved tools"),
        @ApiResponse(responseCode = "400", description = "Invalid filter parameters")
    })
    public ResponseEntity<ToolListResponse> getAllTools(
        @Parameter(description = "Filter by department") 
        @RequestParam(required = false) Department department,
        
        @Parameter(description = "Filter by status") 
        @RequestParam(required = false) ToolStatus status,
        
        @Parameter(description = "Filter by category ID") 
        @RequestParam(name = "category_id", required = false) Long categoryId,
        
        @Parameter(description = "Minimum monthly cost") 
        @RequestParam(name = "min_cost", required = false) BigDecimal minCost,
        
        @Parameter(description = "Maximum monthly cost") 
        @RequestParam(name = "max_cost", required = false) BigDecimal maxCost
    ) {
        ToolListResponse response = toolService.getAllTools(
            department, status, categoryId, minCost, maxCost
        );
        return ResponseEntity.ok(response);
    }

    @GetMapping("/{id}")
    @Operation(summary = "Get tool by ID", 
               description = "Returns detailed information about a specific tool")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Successfully retrieved tool"),
        @ApiResponse(responseCode = "404", description = "Tool not found")
    })
    public ResponseEntity<ToolResponse> getToolById(
        @Parameter(description = "Tool ID") 
        @PathVariable Long id
    ) {
        ToolResponse response = toolService.getToolById(id);
        return ResponseEntity.ok(response);
    }

    @PostMapping
    @Operation(summary = "Create a new tool", 
               description = "Creates a new tool with the provided information")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Tool successfully created"),
        @ApiResponse(responseCode = "400", description = "Invalid request data"),
        @ApiResponse(responseCode = "404", description = "Category not found")
    })
    public ResponseEntity<ToolResponse> createTool(
        @Valid @RequestBody CreateToolRequest request
    ) {
        ToolResponse response = toolService.createTool(request);
        return ResponseEntity.status(HttpStatus.CREATED).body(response);
    }

    @PutMapping("/{id}")
    @Operation(summary = "Update an existing tool", 
               description = "Updates tool information. Only provided fields will be updated.")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Tool successfully updated"),
        @ApiResponse(responseCode = "400", description = "Invalid request data"),
        @ApiResponse(responseCode = "404", description = "Tool or category not found")
    })
    public ResponseEntity<ToolResponse> updateTool(
        @Parameter(description = "Tool ID") 
        @PathVariable Long id,
        
        @Valid @RequestBody UpdateToolRequest request
    ) {
        ToolResponse response = toolService.updateTool(id, request);
        return ResponseEntity.ok(response);
    }

    @DeleteMapping("/{id}")
    @Operation(summary = "Delete a tool", 
               description = "Permanently deletes a tool from the system")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "204", description = "Tool successfully deleted"),
        @ApiResponse(responseCode = "404", description = "Tool not found")
    })
    public ResponseEntity<Void> deleteTool(
        @Parameter(description = "Tool ID") 
        @PathVariable Long id
    ) {
        toolService.deleteTool(id);
        return ResponseEntity.noContent().build();
    }
}
