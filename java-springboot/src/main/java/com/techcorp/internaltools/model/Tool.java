package com.techcorp.internaltools.model;

import jakarta.persistence.*;
import lombok.Data;
import org.hibernate.annotations.JdbcTypeCode;
import org.hibernate.type.SqlTypes;

import java.math.BigDecimal;
import java.time.LocalDateTime;

/**
 * Tool Entity - Maps to 'tools' table in PostgreSQL database
 * 
 * Represents an internal SaaS tool used by the company.
 * Includes cost tracking, ownership information, and usage metrics.
 * 
 * Key Features:
 * - PostgreSQL ENUM support for department and status
 * - Automatic timestamp management (created_at, updated_at)
 * - Eager loading of Category relationship
 * - Cost calculations for analytics
 */
@Data  // Lombok: generates getters, setters, toString, equals, hashCode
@Entity  // JPA: marks this as a database entity
@Table(name = "tools")  // Maps to 'tools' table
public class Tool {
    // Primary key - auto-incremented by database
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    // Tool name - must be unique across all tools (business requirement)
    @Column(nullable = false, unique = true, length = 100)
    private String name;

    @Column(columnDefinition = "TEXT")
    private String description;

    @Column(nullable = false, length = 100)
    private String vendor;

    @Column(name = "website_url", length = 255)
    private String websiteUrl;

    @ManyToOne(fetch = FetchType.EAGER)
    @JoinColumn(name = "category_id", nullable = false)
    private Category category;

    @Column(name = "monthly_cost", nullable = false, precision = 10, scale = 2)
    private BigDecimal monthlyCost;

    // Owner department - uses PostgreSQL ENUM type (7 possible values)
    // @JdbcTypeCode ensures proper mapping to PostgreSQL custom ENUM type
    @Enumerated(EnumType.STRING)  // Store as string in database
    @Column(name = "owner_department", nullable = false, columnDefinition = "department_type")
    @JdbcTypeCode(SqlTypes.NAMED_ENUM)  // Hibernate 6.x: proper PostgreSQL ENUM support
    private Department ownerDepartment;

    // Tool status - uses PostgreSQL ENUM type (active, deprecated, trial)
    @Enumerated(EnumType.STRING)
    @Column(nullable = false, columnDefinition = "tool_status_type")
    @JdbcTypeCode(SqlTypes.NAMED_ENUM)  // Maps to PostgreSQL tool_status_type ENUM
    private ToolStatus status;

    @Column(name = "active_users_count", nullable = false)
    private Integer activeUsersCount = 0;

    @Column(name = "created_at", nullable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "updated_at", nullable = false)
    private LocalDateTime updatedAt;

    /**
     * JPA Lifecycle Hook - called automatically before entity is persisted
     * Sets default values for new tools:
     * - Initializes timestamps (created_at, updated_at)
     * - Sets default status to 'active' if not provided
     * - Initializes activeUsersCount to 0 if not provided
     */
    @PrePersist
    protected void onCreate() {
        createdAt = LocalDateTime.now();
        updatedAt = LocalDateTime.now();
        if (status == null) {
            status = ToolStatus.active;  // Default status for new tools
        }
        if (activeUsersCount == null) {
            activeUsersCount = 0;  // New tools start with 0 users
        }
    }

    /**
     * JPA Lifecycle Hook - called automatically before entity is updated
     * Updates the updated_at timestamp to current time
     */
    @PreUpdate
    protected void onUpdate() {
        updatedAt = LocalDateTime.now();  // Auto-update timestamp on any modification
    }
}
