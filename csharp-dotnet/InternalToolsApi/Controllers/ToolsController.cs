using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using InternalToolsApi.Data;
using InternalToolsApi.Models;

namespace InternalToolsApi.Controllers;

/// <summary>
/// Tools Management Controller
/// </summary>
[ApiController]
[Route("api/[controller]")]
public class ToolsController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly ILogger<ToolsController> _logger;

    public ToolsController(AppDbContext context, ILogger<ToolsController> logger)
    {
        _context = context;
        _logger = logger;
    }

    /// <summary>
    /// Health check endpoint
    /// </summary>
    [HttpGet("health")]
    public IActionResult HealthCheck()
    {
        return Ok(new { status = "healthy", timestamp = DateTime.UtcNow });
    }

    /// <summary>
    /// Get all tools with pagination and filters
    /// </summary>
    [HttpGet]
    public async Task<ActionResult<ToolsListResponse>> GetTools(
        [FromQuery] int? limit,
        [FromQuery] int? skip,
        [FromQuery] string? status,
        [FromQuery] int? category_id,
        [FromQuery] string? vendor,
        [FromQuery] string? search)
    {
        try
        {
            var pageLimit = limit ?? 50;
            var pageSkip = skip ?? 0;

            var query = _context.Tools.AsQueryable();

            // Apply filters
            if (!string.IsNullOrEmpty(status))
                query = query.Where(t => t.Status == status);

            if (category_id.HasValue)
                query = query.Where(t => t.CategoryId == category_id.Value);

            if (!string.IsNullOrEmpty(vendor))
                query = query.Where(t => t.Vendor.Contains(vendor));

            if (!string.IsNullOrEmpty(search))
                query = query.Where(t => t.Name.Contains(search));

            var total = await query.CountAsync();

            var tools = await query
                .Include(t => t.Category)
                .OrderBy(t => t.Id)
                .Skip(pageSkip)
                .Take(pageLimit)
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

            return Ok(new ToolsListResponse
            {
                Tools = tools,
                Total = total,
                Page = pageSkip / pageLimit + 1,
                Limit = pageLimit
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error fetching tools");
            return StatusCode(500, new { error = "Failed to fetch tools", message = ex.Message });
        }
    }

    /// <summary>
    /// Get a single tool by ID
    /// </summary>
    [HttpGet("{id}")]
    public async Task<ActionResult<ToolResponse>> GetTool(int id)
    {
        try
        {
            var tool = await _context.Tools
                .Include(t => t.Category)
                .FirstOrDefaultAsync(t => t.Id == id);

            if (tool == null)
                return NotFound(new { error = "Tool not found", message = $"Tool with ID {id} does not exist" });

            return Ok(new ToolResponse
            {
                Id = tool.Id,
                Name = tool.Name,
                Description = tool.Description,
                Vendor = tool.Vendor,
                WebsiteUrl = tool.WebsiteUrl,
                CategoryId = tool.CategoryId,
                MonthlyCost = tool.MonthlyCost,
                ActiveUsersCount = tool.ActiveUsersCount,
                OwnerDepartment = tool.OwnerDepartment,
                Status = tool.Status,
                CreatedAt = tool.CreatedAt,
                UpdatedAt = tool.UpdatedAt,
                Category = tool.Category?.Name
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error fetching tool {ToolId}", id);
            return StatusCode(500, new { error = "Failed to fetch tool", message = ex.Message });
        }
    }

    /// <summary>
    /// Create a new tool
    /// </summary>
    [HttpPost]
    public async Task<ActionResult<ToolResponse>> CreateTool([FromBody] CreateToolRequest request)
    {
        try
        {
            var tool = new Tool
            {
                Name = request.Name,
                Description = request.Description,
                Vendor = request.Vendor,
                WebsiteUrl = request.WebsiteUrl,
                CategoryId = request.CategoryId,
                MonthlyCost = request.MonthlyCost,
                ActiveUsersCount = request.ActiveUsersCount ?? 0,
                OwnerDepartment = request.OwnerDepartment ?? "Engineering",
                Status = request.Status ?? "active",
                CreatedAt = DateTime.UtcNow,
                UpdatedAt = DateTime.UtcNow
            };

            _context.Tools.Add(tool);
            await _context.SaveChangesAsync();

            // Reload with category
            await _context.Entry(tool).Reference(t => t.Category).LoadAsync();

            var response = new ToolResponse
            {
                Id = tool.Id,
                Name = tool.Name,
                Description = tool.Description,
                Vendor = tool.Vendor,
                WebsiteUrl = tool.WebsiteUrl,
                CategoryId = tool.CategoryId,
                MonthlyCost = tool.MonthlyCost,
                ActiveUsersCount = tool.ActiveUsersCount,
                OwnerDepartment = tool.OwnerDepartment,
                Status = tool.Status,
                CreatedAt = tool.CreatedAt,
                UpdatedAt = tool.UpdatedAt,
                Category = tool.Category?.Name
            };

            return CreatedAtAction(nameof(GetTool), new { id = tool.Id }, response);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error creating tool");
            return StatusCode(500, new { error = "Failed to create tool", message = ex.Message });
        }
    }

    /// <summary>
    /// Update an existing tool
    /// </summary>
    [HttpPut("{id}")]
    public async Task<ActionResult<ToolResponse>> UpdateTool(int id, [FromBody] UpdateToolRequest request)
    {
        try
        {
            var tool = await _context.Tools.FindAsync(id);
            if (tool == null)
                return NotFound(new { error = "Tool not found", message = $"Tool with ID {id} does not exist" });

            // Update fields if provided
            if (request.Name != null) tool.Name = request.Name;
            if (request.Description != null) tool.Description = request.Description;
            if (request.Vendor != null) tool.Vendor = request.Vendor;
            if (request.WebsiteUrl != null) tool.WebsiteUrl = request.WebsiteUrl;
            if (request.CategoryId.HasValue) tool.CategoryId = request.CategoryId.Value;
            if (request.MonthlyCost.HasValue) tool.MonthlyCost = request.MonthlyCost.Value;
            if (request.ActiveUsersCount.HasValue) tool.ActiveUsersCount = request.ActiveUsersCount.Value;
            if (request.OwnerDepartment != null) tool.OwnerDepartment = request.OwnerDepartment;
            if (request.Status != null) tool.Status = request.Status;

            tool.UpdatedAt = DateTime.UtcNow;

            await _context.SaveChangesAsync();

            // Reload with category
            await _context.Entry(tool).Reference(t => t.Category).LoadAsync();

            return Ok(new ToolResponse
            {
                Id = tool.Id,
                Name = tool.Name,
                Description = tool.Description,
                Vendor = tool.Vendor,
                WebsiteUrl = tool.WebsiteUrl,
                CategoryId = tool.CategoryId,
                MonthlyCost = tool.MonthlyCost,
                ActiveUsersCount = tool.ActiveUsersCount,
                OwnerDepartment = tool.OwnerDepartment,
                Status = tool.Status,
                CreatedAt = tool.CreatedAt,
                UpdatedAt = tool.UpdatedAt,
                Category = tool.Category?.Name
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating tool {ToolId}", id);
            return StatusCode(500, new { error = "Failed to update tool", message = ex.Message });
        }
    }

    /// <summary>
    /// Delete a tool
    /// </summary>
    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteTool(int id)
    {
        try
        {
            var tool = await _context.Tools.FindAsync(id);
            if (tool == null)
                return NotFound(new { error = "Tool not found", message = $"Tool with ID {id} does not exist" });

            _context.Tools.Remove(tool);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Tool deleted successfully" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error deleting tool {ToolId}", id);
            return StatusCode(500, new { error = "Failed to delete tool", message = ex.Message });
        }
    }
}
