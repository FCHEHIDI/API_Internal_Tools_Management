package com.techcorp.internaltools.dto.analytics;

import com.techcorp.internaltools.model.Department;
import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;

import java.math.BigDecimal;

@Data
@Builder
@AllArgsConstructor
@Schema(description = "Low usage tool analysis")
public class LowUsageToolDto {
    
    @Schema(description = "Tool ID", example = "23")
    private Long id;

    @Schema(description = "Tool name", example = "Specialized Analytics")
    private String name;

    @Schema(description = "Monthly cost per user", example = "89.99")
    private BigDecimal monthlyCost;

    @Schema(description = "Number of active users", example = "2")
    private Integer activeUsersCount;

    @Schema(description = "Total cost per user", example = "45.00")
    private BigDecimal costPerUser;

    @Schema(description = "Owner department", example = "Marketing")
    private Department department;

    @Schema(description = "Vendor name", example = "SmallVendor")
    private String vendor;

    @Schema(description = "Warning level", example = "high")
    private String warningLevel;

    @Schema(description = "Recommended action", example = "Consider canceling or downgrading")
    private String potentialAction;
}
