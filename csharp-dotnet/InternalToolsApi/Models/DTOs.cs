using System.ComponentModel.DataAnnotations;

namespace InternalToolsApi.Models;

/// <summary>
/// Response model for tool with category name
/// </summary>
public class ToolResponse
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public string Vendor { get; set; } = string.Empty;
    public string? WebsiteUrl { get; set; }
    public int CategoryId { get; set; }
    public decimal MonthlyCost { get; set; }
    public int ActiveUsersCount { get; set; }
    public string OwnerDepartment { get; set; } = string.Empty;
    public string Status { get; set; } = string.Empty;
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }
    public string? Category { get; set; }
}

/// <summary>
/// Request model for creating a new tool
/// </summary>
public class CreateToolRequest
{
    [Required]
    [MaxLength(100)]
    public string Name { get; set; } = string.Empty;

    [Required]
    public string Description { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    public string Vendor { get; set; } = string.Empty;

    [MaxLength(255)]
    public string? WebsiteUrl { get; set; }

    [Required]
    public int CategoryId { get; set; }

    [Required]
    [Range(0, double.MaxValue)]
    public decimal MonthlyCost { get; set; }

    public int? ActiveUsersCount { get; set; }

    [MaxLength(50)]
    public string? OwnerDepartment { get; set; }

    [MaxLength(20)]
    public string? Status { get; set; }
}

/// <summary>
/// Request model for updating a tool
/// </summary>
public class UpdateToolRequest
{
    [MaxLength(100)]
    public string? Name { get; set; }

    public string? Description { get; set; }

    [MaxLength(100)]
    public string? Vendor { get; set; }

    [MaxLength(255)]
    public string? WebsiteUrl { get; set; }

    public int? CategoryId { get; set; }

    [Range(0, double.MaxValue)]
    public decimal? MonthlyCost { get; set; }

    public int? ActiveUsersCount { get; set; }

    [MaxLength(50)]
    public string? OwnerDepartment { get; set; }

    [MaxLength(20)]
    public string? Status { get; set; }
}

/// <summary>
/// Paginated list response
/// </summary>
public class ToolsListResponse
{
    public List<ToolResponse> Tools { get; set; } = new();
    public int Total { get; set; }
    public int Page { get; set; }
    public int Limit { get; set; }
}

/// <summary>
/// Department cost analytics
/// </summary>
public class DepartmentCost
{
    public string Department { get; set; } = string.Empty;
    public decimal TotalCost { get; set; }
    public int ToolCount { get; set; }
    public decimal AverageCost { get; set; }
}

/// <summary>
/// Tools by category analytics
/// </summary>
public class CategoryAnalytics
{
    public string Category { get; set; } = string.Empty;
    public int ToolCount { get; set; }
    public decimal TotalCost { get; set; }
    public double AverageUsers { get; set; }
}

/// <summary>
/// Vendor summary analytics
/// </summary>
public class VendorSummary
{
    public string Vendor { get; set; } = string.Empty;
    public int ToolCount { get; set; }
    public decimal TotalSpend { get; set; }
    public List<string> Tools { get; set; } = new();
}
