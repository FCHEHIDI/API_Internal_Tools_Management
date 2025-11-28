package com.techcorp.internaltools.service;

import com.techcorp.internaltools.dto.*;
import com.techcorp.internaltools.exception.ResourceNotFoundException;
import com.techcorp.internaltools.model.*;
import com.techcorp.internaltools.repository.CategoryRepository;
import com.techcorp.internaltools.repository.ToolRepository;
import com.techcorp.internaltools.repository.ToolSpecifications;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

/**
 * ToolService - Business logic layer for Tool CRUD operations
 * 
 * Responsibilities:
 * - Implements business rules for tool management
 * - Handles validation and data transformation
 * - Manages transactions for database operations
 * - Converts between Entity and DTO objects
 * 
 * All public methods are transactional to ensure data consistency.
 */
@Service  // Spring: marks this as a service component
@RequiredArgsConstructor  // Lombok: generates constructor with final fields (dependency injection)
public class ToolService {

    private final ToolRepository toolRepository;
    private final CategoryRepository categoryRepository;

    /**
     * Get all tools with optional filters
     * 
     * Supports filtering by:
     * - department: Filter by owner department (Engineering, Sales, etc.)
     * - status: Filter by tool status (active, deprecated, trial)
     * - categoryId: Filter by category ID
     * - minCost/maxCost: Filter by monthly cost range
     * 
     * @return ToolListResponse containing:
     *         - data: List of matching tools
     *         - total: Total number of tools in database
     *         - filtered: Number of tools after applying filters
     *         - filtersApplied: Map of filters that were applied
     */
    @Transactional(readOnly = true)  // Read-only transaction for better performance
    public ToolListResponse getAllTools(
        Department department,
        ToolStatus status,
        Long categoryId,
        BigDecimal minCost,
        BigDecimal maxCost
    ) {
        // Query database with filters using JPA Specification pattern
        // This approach avoids PostgreSQL ENUM parameter binding issues
        // by only adding predicates for non-null values
        List<Tool> tools = toolRepository.findAll(
            ToolSpecifications.withFilters(department, status, categoryId, minCost, maxCost)
        );
        
        List<ToolResponse> toolResponses = tools.stream()
            .map(ToolResponse::fromEntity)
            .collect(Collectors.toList());
        
        long total = toolRepository.count();
        long filtered = tools.size();
        
        Map<String, String> appliedFilters = new HashMap<>();
        if (department != null) appliedFilters.put("department", department.name());
        if (status != null) appliedFilters.put("status", status.name());
        if (categoryId != null) appliedFilters.put("category_id", categoryId.toString());
        if (minCost != null) appliedFilters.put("min_cost", minCost.toString());
        if (maxCost != null) appliedFilters.put("max_cost", maxCost.toString());
        
        return new ToolListResponse(toolResponses, total, filtered, appliedFilters);
    }

    /**
     * Get tool by ID
     */
    @Transactional(readOnly = true)
    public ToolResponse getToolById(Long id) {
        Tool tool = toolRepository.findById(id)
            .orElseThrow(() -> new ResourceNotFoundException(
                "Tool with ID " + id + " does not exist"
            ));
        return ToolResponse.fromEntity(tool);
    }

    /**
     * Create new tool
     * 
     * Validates:
     * - Category exists (throws 404 if not found)
     * - Name is unique (database constraint)
     * - All required fields are provided (validated by @Valid in controller)
     * 
     * Business Rules:
     * - Status defaults to 'active' if not provided
     * - activeUsersCount initialized to 0
     * - Timestamps set automatically via @PrePersist
     * 
     * @param request CreateToolRequest with validated data
     * @return ToolResponse with created tool (including generated ID)
     * @throws ResourceNotFoundException if category doesn't exist
     */
    @Transactional  // Ensures atomic operation (rollback on error)
    public ToolResponse createTool(CreateToolRequest request) {
        // STEP 1: Validate category exists (business rule requirement)
        Category category = categoryRepository.findById(request.getCategoryId())
            .orElseThrow(() -> new ResourceNotFoundException(
                "Category with ID " + request.getCategoryId() + " does not exist"
            ));

        // STEP 2: Map request DTO to entity
        Tool tool = new Tool();
        tool.setName(request.getName());
        tool.setDescription(request.getDescription());
        tool.setVendor(request.getVendor());
        tool.setWebsiteUrl(request.getWebsiteUrl());
        tool.setCategory(category);  // Set validated category
        tool.setMonthlyCost(request.getMonthlyCost());
        tool.setOwnerDepartment(request.getOwnerDepartment());  // PostgreSQL ENUM
        tool.setStatus(request.getStatus() != null ? request.getStatus() : ToolStatus.active);
        tool.setActiveUsersCount(0);  // New tools start with 0 users

        // STEP 3: Save to database (triggers @PrePersist for timestamps)
        Tool savedTool = toolRepository.save(tool);
        
        // STEP 4: Convert entity to response DTO
        return ToolResponse.fromEntity(savedTool);
    }

    /**
     * Update existing tool
     * 
     * Partial update strategy:
     * - Only fields provided in request are updated
     * - Null fields are ignored (preserve existing values)
     * - updated_at timestamp automatically updated via @PreUpdate
     * 
     * Validates:
     * - Tool exists (throws 404 if not found)
     * - Category exists if categoryId provided
     * - Field values are valid (validated by @Valid in controller)
     * 
     * @param id Tool ID to update
     * @param request UpdateToolRequest with fields to update (partial)
     * @return ToolResponse with updated tool
     * @throws ResourceNotFoundException if tool or category not found
     */
    @Transactional  // Transaction ensures all-or-nothing update
    public ToolResponse updateTool(Long id, UpdateToolRequest request) {
        // STEP 1: Fetch existing tool (or throw 404)
        Tool tool = toolRepository.findById(id)
            .orElseThrow(() -> new ResourceNotFoundException(
                "Tool with ID " + id + " does not exist"
            ));

        // STEP 2: Update only provided fields (partial update pattern)
        if (request.getName() != null) {
            tool.setName(request.getName());
        }
        if (request.getDescription() != null) {
            tool.setDescription(request.getDescription());
        }
        if (request.getVendor() != null) {
            tool.setVendor(request.getVendor());
        }
        if (request.getWebsiteUrl() != null) {
            tool.setWebsiteUrl(request.getWebsiteUrl());
        }
        if (request.getCategoryId() != null) {
            Category category = categoryRepository.findById(request.getCategoryId())
                .orElseThrow(() -> new ResourceNotFoundException(
                    "Category with ID " + request.getCategoryId() + " does not exist"
                ));
            tool.setCategory(category);
        }
        if (request.getMonthlyCost() != null) {
            tool.setMonthlyCost(request.getMonthlyCost());
        }
        if (request.getOwnerDepartment() != null) {
            tool.setOwnerDepartment(request.getOwnerDepartment());
        }
        if (request.getStatus() != null) {
            tool.setStatus(request.getStatus());
        }
        if (request.getActiveUsersCount() != null) {
            tool.setActiveUsersCount(request.getActiveUsersCount());
        }

        Tool updatedTool = toolRepository.save(tool);
        return ToolResponse.fromEntity(updatedTool);
    }

    /**
     * Delete tool
     */
    @Transactional
    public void deleteTool(Long id) {
        if (!toolRepository.existsById(id)) {
            throw new ResourceNotFoundException("Tool with ID " + id + " does not exist");
        }
        toolRepository.deleteById(id);
    }
}
