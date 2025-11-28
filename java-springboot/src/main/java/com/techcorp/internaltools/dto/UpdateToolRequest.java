package com.techcorp.internaltools.dto;

import com.techcorp.internaltools.model.Department;
import com.techcorp.internaltools.model.ToolStatus;
import io.swagger.v3.oas.annotations.media.Schema;
import jakarta.validation.constraints.*;
import lombok.Data;

import java.math.BigDecimal;

@Data
@Schema(description = "Request to update an existing tool")
public class UpdateToolRequest {
    
    @Size(min = 2, max = 100, message = "Name must be between 2 and 100 characters")
    @Schema(description = "Tool name", example = "Slack")
    private String name;

    @Schema(description = "Tool description", example = "Updated team messaging platform")
    private String description;

    @Size(max = 100, message = "Vendor name must not exceed 100 characters")
    @Schema(description = "Vendor/provider name", example = "Slack Technologies")
    private String vendor;

    @Pattern(regexp = "^https?://.*", message = "Must be a valid URL format")
    @Schema(description = "Tool website URL", example = "https://slack.com")
    private String websiteUrl;

    @Schema(description = "Category ID", example = "1")
    private Long categoryId;

    @DecimalMin(value = "0.0", message = "Must be a positive number")
    @Digits(integer = 10, fraction = 2, message = "Must have maximum 2 decimal places")
    @Schema(description = "Monthly cost per user", example = "9.50")
    private BigDecimal monthlyCost;

    @Schema(description = "Owner department", example = "Engineering")
    private Department ownerDepartment;

    @Schema(description = "Tool status", example = "active")
    private ToolStatus status;

    @Min(value = 0, message = "Active users count must be non-negative")
    @Schema(description = "Number of active users", example = "25")
    private Integer activeUsersCount;
}
