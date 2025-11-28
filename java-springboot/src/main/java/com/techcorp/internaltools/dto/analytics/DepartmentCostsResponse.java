package com.techcorp.internaltools.dto.analytics;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Data;

import java.math.BigDecimal;
import java.util.List;

@Data
@AllArgsConstructor
@Schema(description = "Department costs analysis response")
public class DepartmentCostsResponse {
    
    @Schema(description = "List of department costs")
    private List<DepartmentCostDto> data;

    @Schema(description = "Summary statistics")
    private Summary summary;

    @Data
    @AllArgsConstructor
    public static class Summary {
        @Schema(description = "Total company cost", example = "2450.80")
        private BigDecimal totalCompanyCost;

        @Schema(description = "Number of departments", example = "6")
        private Integer departmentsCount;

        @Schema(description = "Most expensive department", example = "Engineering")
        private String mostExpensiveDepartment;
    }
}
