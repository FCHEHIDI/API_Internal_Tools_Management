package com.techcorp.internaltools.repository;

import com.techcorp.internaltools.model.Department;
import com.techcorp.internaltools.model.Tool;
import com.techcorp.internaltools.model.ToolStatus;
import jakarta.persistence.criteria.Predicate;
import org.springframework.data.jpa.domain.Specification;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.List;

/**
 * ToolSpecifications - Dynamic query builder using JPA Criteria API
 * 
 * This solves the PostgreSQL ENUM parameter binding issue by:
 * - Building predicates dynamically (only add filters when non-null)
 * - Using proper JPA type handling (no raw SQL, no manual casting)
 * - Type-safe queries with compile-time checking
 * 
 * The Specification pattern is the recommended approach for dynamic queries
 * in Spring Data JPA, especially with complex types like ENUMs.
 */
public class ToolSpecifications {
    
    /**
     * Build dynamic filter specification
     * 
     * Only adds predicates for non-null parameters
     * This avoids the "could not determine data type" error entirely
     * because we never pass null ENUM values to PostgreSQL
     */
    public static Specification<Tool> withFilters(
        Department department,
        ToolStatus status,
        Long categoryId,
        BigDecimal minCost,
        BigDecimal maxCost
    ) {
        return (root, query, criteriaBuilder) -> {
            List<Predicate> predicates = new ArrayList<>();
            
            // Only add department filter if provided
            if (department != null) {
                predicates.add(criteriaBuilder.equal(root.get("ownerDepartment"), department));
            }
            
            // Only add status filter if provided
            if (status != null) {
                predicates.add(criteriaBuilder.equal(root.get("status"), status));
            }
            
            // Only add category filter if provided
            if (categoryId != null) {
                predicates.add(criteriaBuilder.equal(root.get("category").get("id"), categoryId));
            }
            
            // Only add min cost filter if provided
            if (minCost != null) {
                predicates.add(criteriaBuilder.greaterThanOrEqualTo(root.get("monthlyCost"), minCost));
            }
            
            // Only add max cost filter if provided
            if (maxCost != null) {
                predicates.add(criteriaBuilder.lessThanOrEqualTo(root.get("monthlyCost"), maxCost));
            }
            
            // Combine all predicates with AND
            return criteriaBuilder.and(predicates.toArray(new Predicate[0]));
        };
    }
}
