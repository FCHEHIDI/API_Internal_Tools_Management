package com.techcorp.internaltools.dto.analytics;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;

import java.math.BigDecimal;

@Data
@Builder
@AllArgsConstructor
@Schema(description = "Vendor analysis")
public class VendorDto {
    
    @Schema(description = "Vendor name", example = "Google")
    private String vendor;

    @Schema(description = "Number of tools from vendor", example = "4")
    private Long toolsCount;

    @Schema(description = "Total monthly cost", example = "234.50")
    private BigDecimal totalMonthlyCost;

    @Schema(description = "Total users across vendor tools", example = "67")
    private Long totalUsers;

    @Schema(description = "Departments using vendor tools", example = "Engineering,Sales,Marketing")
    private String departments;

    @Schema(description = "Average cost per user", example = "3.50")
    private BigDecimal averageCostPerUser;

    @Schema(description = "Vendor efficiency rating", example = "excellent")
    private String vendorEfficiency;
}
