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
@Schema(description = "Expensive tool analysis")
public class ExpensiveToolDto {
    
    @Schema(description = "Tool ID", example = "15")
    private Long id;

    @Schema(description = "Tool name", example = "Enterprise CRM")
    private String name;

    @Schema(description = "Monthly cost per user", example = "199.99")
    private BigDecimal monthlyCost;

    @Schema(description = "Number of active users", example = "12")
    private Integer activeUsersCount;

    @Schema(description = "Cost per user", example = "16.67")
    private BigDecimal costPerUser;

    @Schema(description = "Owner department", example = "Sales")
    private Department department;

    @Schema(description = "Vendor name", example = "BigCorp")
    private String vendor;

    @Schema(description = "Efficiency rating", example = "low")
    private String efficiencyRating;
}
