package com.techcorp.internaltools.dto;

import io.swagger.v3.oas.annotations.media.Schema;
import lombok.AllArgsConstructor;
import lombok.Data;

import java.util.List;
import java.util.Map;

@Data
@AllArgsConstructor
@Schema(description = "Paginated list of tools with filters applied")
public class ToolListResponse {
    
    @Schema(description = "List of tools")
    private List<ToolResponse> data;

    @Schema(description = "Total number of tools in database", example = "20")
    private Long total;

    @Schema(description = "Number of tools after filters", example = "15")
    private Long filtered;

    @Schema(description = "Applied filters")
    private Map<String, String> filtersApplied;
}
