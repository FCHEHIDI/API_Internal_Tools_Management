package com.techcorp.internaltools.dto.analytics;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Data;

import java.util.List;

@Data
@AllArgsConstructor
@Schema(description = "Tools by category analysis response")
public class ToolsByCategoryResponse {
    
    @Schema(description = "List of categories with analytics")
    private List<CategoryDto> data;

    @Schema(description = "Insights")
    private Insights insights;

    @Data
    @AllArgsConstructor
    public static class Insights {
        @Schema(description = "Most expensive category", example = "Development")
        private String mostExpensiveCategory;

        @Schema(description = "Most efficient category", example = "Communication")
        private String mostEfficientCategory;
    }
}
