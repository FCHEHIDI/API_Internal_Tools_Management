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
@Schema(description = "Department cost analysis")
public class DepartmentCostDto {
    
    @Schema(description = "Department name", example = "Engineering")
    private Department department;

    @Schema(description = "Total monthly cost for department", example = "890.50")
    private BigDecimal totalCost;

    @Schema(description = "Number of tools used by department", example = "12")
    private Long toolsCount;

    @Schema(description = "Total active users in department", example = "45")
    private Long totalUsers;

    @Schema(description = "Average cost per tool", example = "74.21")
    private BigDecimal averageCostPerTool;

    @Schema(description = "Percentage of total company budget", example = "36.2")
    private BigDecimal costPercentage;
}
