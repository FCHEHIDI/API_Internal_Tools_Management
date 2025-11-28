package com.techcorp.internaltools.dto.analytics;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Data;

import java.util.List;

@Data
@AllArgsConstructor
@Schema(description = "Vendor summary analysis response")
public class VendorSummaryResponse {
    
    @Schema(description = "List of vendors with analytics")
    private List<VendorDto> data;

    @Schema(description = "Vendor insights")
    private VendorInsights vendorInsights;

    @Data
    @AllArgsConstructor
    public static class VendorInsights {
        @Schema(description = "Most expensive vendor", example = "BigCorp")
        private String mostExpensiveVendor;

        @Schema(description = "Most efficient vendor", example = "Google")
        private String mostEfficientVendor;

        @Schema(description = "Number of single-tool vendors", example = "8")
        private Integer singleToolVendors;
    }
}
