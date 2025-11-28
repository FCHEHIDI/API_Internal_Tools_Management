package com.techcorp.internaltools.dto.analytics;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Data;

import java.math.BigDecimal;
import java.util.List;

@Data
@AllArgsConstructor
@Schema(description = "Low usage tools analysis response")
public class LowUsageToolsResponse {
    
    @Schema(description = "List of underutilized tools")
    private List<LowUsageToolDto> data;

    @Schema(description = "Savings analysis")
    private SavingsAnalysis savingsAnalysis;

    @Data
    @AllArgsConstructor
    public static class SavingsAnalysis {
        @Schema(description = "Total underutilized tools", example = "5")
        private Integer totalUnderutilizedTools;

        @Schema(description = "Potential monthly savings", example = "287.50")
        private BigDecimal potentialMonthlySavings;

        @Schema(description = "Potential annual savings", example = "3450.00")
        private BigDecimal potentialAnnualSavings;
    }
}
