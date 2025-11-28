package com.techcorp.internaltools.dto;

import com.techcorp.internaltools.model.Department;
import com.techcorp.internaltools.model.Tool;
import com.techcorp.internaltools.model.ToolStatus;
import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.math.BigDecimal;
import java.time.LocalDateTime;

@Data
@Builder
@NoArgsConstructor
@AllArgsConstructor
@Schema(description = "Tool response object")
public class ToolResponse {
    
    @Schema(description = "Tool ID", example = "1")
    private Long id;

    @Schema(description = "Tool name", example = "Slack")
    private String name;

    @Schema(description = "Tool description", example = "Team messaging platform")
    private String description;

    @Schema(description = "Vendor name", example = "Slack Technologies")
    private String vendor;

    @Schema(description = "Website URL", example = "https://slack.com")
    private String websiteUrl;

    @Schema(description = "Category name", example = "Communication")
    private String category;

    @Schema(description = "Monthly cost per user", example = "8.00")
    private BigDecimal monthlyCost;

    @Schema(description = "Total monthly cost (monthly_cost * active_users)", example = "200.00")
    private BigDecimal totalMonthlyCost;

    @Schema(description = "Owner department", example = "Engineering")
    private Department ownerDepartment;

    @Schema(description = "Tool status", example = "active")
    private ToolStatus status;

    @Schema(description = "Number of active users", example = "25")
    private Integer activeUsersCount;

    @Schema(description = "Creation timestamp", example = "2025-05-01T09:00:00")
    private LocalDateTime createdAt;

    @Schema(description = "Last update timestamp", example = "2025-05-01T09:00:00")
    private LocalDateTime updatedAt;

    public static ToolResponse fromEntity(Tool tool) {
        BigDecimal totalMonthlyCost = tool.getMonthlyCost()
            .multiply(BigDecimal.valueOf(tool.getActiveUsersCount()));
        
        return ToolResponse.builder()
            .id(tool.getId())
            .name(tool.getName())
            .description(tool.getDescription())
            .vendor(tool.getVendor())
            .websiteUrl(tool.getWebsiteUrl())
            .category(tool.getCategory().getName())
            .monthlyCost(tool.getMonthlyCost())
            .totalMonthlyCost(totalMonthlyCost)
            .ownerDepartment(tool.getOwnerDepartment())
            .status(tool.getStatus())
            .activeUsersCount(tool.getActiveUsersCount())
            .createdAt(tool.getCreatedAt())
            .updatedAt(tool.getUpdatedAt())
            .build();
    }
}
