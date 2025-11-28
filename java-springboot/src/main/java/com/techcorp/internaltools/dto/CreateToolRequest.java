package com.techcorp.internaltools.dto;

import com.techcorp.internaltools.model.Department;
import com.techcorp.internaltools.model.ToolStatus;
import io.swagger.v3.oas.annotations.media.Schema;
import jakarta.validation.constraints.*;
import lombok.Data;

import java.math.BigDecimal;

@Data
@Schema(description = "Request to create a new tool")
public class CreateToolRequest {
    
    @NotBlank(message = "Name is required and must be 2-100 characters")
    @Size(min = 2, max = 100, message = "Name must be between 2 and 100 characters")
    @Schema(description = "Tool name (unique)", example = "Slack")
    private String name;

    @Schema(description = "Tool description", example = "Team messaging platform")
    private String description;

    @NotBlank(message = "Vendor is required")
    @Size(max = 100, message = "Vendor name must not exceed 100 characters")
    @Schema(description = "Vendor/provider name", example = "Slack Technologies")
    private String vendor;

    @Pattern(regexp = "^https?://.*", message = "Must be a valid URL format")
    @Schema(description = "Tool website URL", example = "https://slack.com")
    private String websiteUrl;

    @NotNull(message = "Category ID is required")
    @Schema(description = "Category ID (must exist)", example = "1")
    private Long categoryId;

    @NotNull(message = "Monthly cost is required")
    @DecimalMin(value = "0.0", message = "Must be a positive number")
    @Digits(integer = 10, fraction = 2, message = "Must have maximum 2 decimal places")
    @Schema(description = "Monthly cost per user", example = "8.00")
    private BigDecimal monthlyCost;

    @NotNull(message = "Owner department is required")
    @Schema(description = "Department that owns this tool", example = "Engineering")
    private Department ownerDepartment;

    @Schema(description = "Tool status", example = "active")
    private ToolStatus status;
}
