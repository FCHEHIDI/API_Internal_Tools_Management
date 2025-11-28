package com.techcorp.internaltools.dto.analytics;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;

import java.math.BigDecimal;

@Data
@Builder
@AllArgsConstructor
@Schema(description = "Category analysis")
public class CategoryDto {
    
    @Schema(description = "Category name", example = "Development")
    private String categoryName;

    @Schema(description = "Number of tools in category", example = "8")
    private Long toolsCount;

    @Schema(description = "Total cost for category", example = "650.00")
    private BigDecimal totalCost;

    @Schema(description = "Total users across all tools", example = "67")
    private Long totalUsers;

    @Schema(description = "Percentage of budget", example = "26.5")
    private BigDecimal percentageOfBudget;

    @Schema(description = "Average cost per user", example = "9.70")
    private BigDecimal averageCostPerUser;
}
