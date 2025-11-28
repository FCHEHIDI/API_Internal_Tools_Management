package com.techcorp.internaltools.dto.analytics;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Data;

import java.math.BigDecimal;
import java.util.List;

@Data
@AllArgsConstructor
@Schema(description = "Expensive tools analysis response")
public class ExpensiveToolsResponse {
    
    @Schema(description = "List of expensive tools")
    private List<ExpensiveToolDto> data;

    @Schema(description = "Analysis summary")
    private Analysis analysis;

    @Data
    @AllArgsConstructor
    public static class Analysis {
        @Schema(description = "Total tools analyzed", example = "18")
        private Integer totalToolsAnalyzed;

        @Schema(description = "Average cost per user company-wide", example = "12.45")
        private BigDecimal avgCostPerUserCompany;

        @Schema(description = "Potential savings from inefficient tools", example = "345.50")
        private BigDecimal potentialSavingsIdentified;
    }
}
