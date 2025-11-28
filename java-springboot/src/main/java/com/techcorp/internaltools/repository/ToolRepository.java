package com.techcorp.internaltools.repository;

import com.techcorp.internaltools.model.Department;
import com.techcorp.internaltools.model.Tool;
import com.techcorp.internaltools.model.ToolStatus;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.math.BigDecimal;
import java.util.List;

/**
 * ToolRepository - Data access layer for Tool entity
 * 
 * Extends JpaRepository which provides:
 * - Basic CRUD: save(), findById(), findAll(), deleteById(), etc.
 * - Automatic transaction management
 * - Query derivation from method names
 * 
 * Custom queries:
 * - Filter queries for CRUD endpoints
 * - Aggregation queries for analytics endpoints
 * - Uses JPQL (Java Persistence Query Language)
 */
@Repository  // Spring: marks this as a data access component
public interface ToolRepository extends JpaRepository<Tool, Long>, JpaSpecificationExecutor<Tool> {
    
    // ========== CRUD OPERATIONS ==========
    // Simple filter methods using Spring Data JPA method naming convention
    
    List<Tool> findByStatus(ToolStatus status);
    List<Tool> findByOwnerDepartment(Department department);
    List<Tool> findByStatusAndOwnerDepartment(ToolStatus status, Department department);
    
    // ========== COMPLEX FILTERING ==========
    // Note: Dynamic filtering is now handled via JpaSpecificationExecutor
    // See ToolSpecifications class and service layer usage
    // This avoids PostgreSQL ENUM parameter binding issues
    
    // ========== ANALYTICS QUERIES ==========
    // Complex aggregation queries for business intelligence
    // Note: These return Object[] - need manual mapping in service layer
    
    /**
     * Department costs aggregation for Analytics Endpoint #1
     * 
     * Returns: [Department, totalCost, toolsCount, totalUsers]
     * Filters: Only active tools (business rule)
     * Order: By total cost descending (most expensive first)
     */
    @Query("SELECT t.ownerDepartment, SUM(t.monthlyCost * t.activeUsersCount) as totalCost, " +
           "COUNT(t) as toolsCount, SUM(t.activeUsersCount) as totalUsers " +
           "FROM Tool t WHERE t.status = 'active' " +
           "GROUP BY t.ownerDepartment " +
           "ORDER BY totalCost DESC")
    List<Object[]> findDepartmentCosts();
    
    @Query("SELECT t FROM Tool t WHERE t.status = 'active' " +
           "ORDER BY (t.monthlyCost * t.activeUsersCount) DESC")
    List<Tool> findExpensiveTools();
    
    @Query("SELECT c.name, COUNT(t), SUM(t.monthlyCost * t.activeUsersCount), " +
           "SUM(t.activeUsersCount) " +
           "FROM Tool t JOIN t.category c WHERE t.status = 'active' " +
           "GROUP BY c.name")
    List<Object[]> findToolsByCategory();
    
    @Query("SELECT t FROM Tool t WHERE t.status = 'active' AND " +
           "t.activeUsersCount <= :maxUsers " +
           "ORDER BY (t.monthlyCost * t.activeUsersCount) DESC")
    List<Tool> findLowUsageTools(@Param("maxUsers") Integer maxUsers);
    
    @Query("SELECT t.vendor, COUNT(t), SUM(t.monthlyCost * t.activeUsersCount), " +
           "SUM(t.activeUsersCount), GROUP_CONCAT(DISTINCT t.ownerDepartment) " +
           "FROM Tool t WHERE t.status = 'active' " +
           "GROUP BY t.vendor")
    List<Object[]> findVendorSummary();
    
    // Aggregate queries for analytics
    @Query("SELECT SUM(t.monthlyCost * t.activeUsersCount) FROM Tool t WHERE t.status = 'active'")
    BigDecimal getTotalCompanyCost();
    
    @Query("SELECT SUM(t.monthlyCost * t.activeUsersCount) / SUM(t.activeUsersCount) " +
           "FROM Tool t WHERE t.status = 'active' AND t.activeUsersCount > 0")
    BigDecimal getAverageCostPerUser();
}
