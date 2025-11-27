using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using InternalToolsApi.Data;
using InternalToolsApi.Models;

namespace InternalToolsApi.Controllers;

/// <summary>
/// Analytics Controller
/// </summary>
[ApiController]
[Route("api/[controller]")]
public class AnalyticsController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly ILogger<AnalyticsController> _logger;

    public AnalyticsController(AppDbContext context, ILogger<AnalyticsController> logger)
    {
        _context = context;
        _logger = logger;
    }

    /// <summary>
    /// Get department cost analytics
    /// </summary>
    [HttpGet("department-costs")]
    public async Task<ActionResult<List<DepartmentCost>>> GetDepartmentCosts()
    {
        try
        {
            var costs = await _context.Tools
                .Where(t => t.Status == "active")
                .GroupBy(t => t.OwnerDepartment)
                .Select(g => new DepartmentCost
                {
                    Department = g.Key,
                    TotalCost = g.Sum(t => t.MonthlyCost),
                    ToolCount = g.Count(),
                    AverageCost = g.Average(t => t.MonthlyCost)
                })
                .OrderByDescending(d => d.TotalCost)
                .ToListAsync();

            return Ok(costs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error fetching department costs");
            return StatusCode(500, new { error = "Failed to fetch department costs", message = ex.Message });
        }
    }

    /// <summary>
    /// Get expensive tools (monthly cost > $100)
    /// </summary>
    [HttpGet("expensive-tools")]
    public async Task<ActionResult<List<ToolResponse>>> GetExpensiveTools()
    {
        try
        {
            var tools = await _context.Tools
                .Include(t => t.Category)
                .Where(t => t.Status == "active" && t.MonthlyCost > 100)
                .OrderByDescending(t => t.MonthlyCost)
                .Select(t => new ToolResponse
                {
                    Id = t.Id,
                    Name = t.Name,
                    Description = t.Description,
                    Vendor = t.Vendor,
                    WebsiteUrl = t.WebsiteUrl,
                    CategoryId = t.CategoryId,
                    MonthlyCost = t.MonthlyCost,
                    ActiveUsersCount = t.ActiveUsersCount,
                    OwnerDepartment = t.OwnerDepartment,
                    Status = t.Status,
                    CreatedAt = t.CreatedAt,
                    UpdatedAt = t.UpdatedAt,
                    Category = t.Category != null ? t.Category.Name : null
                })
                .ToListAsync();

            return Ok(tools);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error fetching expensive tools");
            return StatusCode(500, new { error = "Failed to fetch expensive tools", message = ex.Message });
        }
    }

    /// <summary>
    /// Get low usage tools (active users < 10)
    /// </summary>
    [HttpGet("low-usage")]
    public async Task<ActionResult<List<ToolResponse>>> GetLowUsageTools()
    {
        try
        {
            var tools = await _context.Tools
                .Include(t => t.Category)
                .Where(t => t.Status == "active" && t.ActiveUsersCount < 10)
                .OrderBy(t => t.ActiveUsersCount)
                .Select(t => new ToolResponse
                {
                    Id = t.Id,
                    Name = t.Name,
                    Description = t.Description,
                    Vendor = t.Vendor,
                    WebsiteUrl = t.WebsiteUrl,
                    CategoryId = t.CategoryId,
                    MonthlyCost = t.MonthlyCost,
                    ActiveUsersCount = t.ActiveUsersCount,
                    OwnerDepartment = t.OwnerDepartment,
                    Status = t.Status,
                    CreatedAt = t.CreatedAt,
                    UpdatedAt = t.UpdatedAt,
                    Category = t.Category != null ? t.Category.Name : null
                })
                .ToListAsync();

            return Ok(tools);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error fetching low usage tools");
            return StatusCode(500, new { error = "Failed to fetch low usage tools", message = ex.Message });
        }
    }

    /// <summary>
    /// Get tools by category analytics
    /// </summary>
    [HttpGet("tools-by-category")]
    public async Task<ActionResult<List<CategoryAnalytics>>> GetToolsByCategory([FromQuery] int? category_id)
    {
        try
        {
            var query = _context.Tools
                .Where(t => t.Status == "active");

            if (category_id.HasValue)
                query = query.Where(t => t.CategoryId == category_id.Value);

            var analytics = await query
                .Include(t => t.Category)
                .GroupBy(t => new { t.CategoryId, t.Category!.Name })
                .Select(g => new CategoryAnalytics
                {
                    Category = g.Key.Name,
                    ToolCount = g.Count(),
                    TotalCost = g.Sum(t => t.MonthlyCost),
                    AverageUsers = g.Average(t => (double)t.ActiveUsersCount)
                })
                .OrderByDescending(c => c.TotalCost)
                .ToListAsync();

            return Ok(analytics);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error fetching tools by category");
            return StatusCode(500, new { error = "Failed to fetch tools by category", message = ex.Message });
        }
    }

    /// <summary>
    /// Get vendor summary analytics
    /// </summary>
    [HttpGet("vendor-summary")]
    public async Task<ActionResult<List<VendorSummary>>> GetVendorSummary()
    {
        try
        {
            var vendors = await _context.Tools
                .Where(t => t.Status == "active")
                .GroupBy(t => t.Vendor)
                .Select(g => new VendorSummary
                {
                    Vendor = g.Key,
                    ToolCount = g.Count(),
                    TotalSpend = g.Sum(t => t.MonthlyCost),
                    Tools = g.Select(t => t.Name).ToList()
                })
                .OrderByDescending(v => v.TotalSpend)
                .ToListAsync();

            return Ok(vendors);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error fetching vendor summary");
            return StatusCode(500, new { error = "Failed to fetch vendor summary", message = ex.Message });
        }
    }
}
