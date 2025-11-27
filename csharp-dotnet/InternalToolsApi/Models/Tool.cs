using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace InternalToolsApi.Models;

/// <summary>
/// Represents a SaaS tool in the organization
/// </summary>
[Table("tools")]
public class Tool
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Required]
    [MaxLength(100)]
    [Column("name")]
    public string Name { get; set; } = string.Empty;

    [Required]
    [Column("description")]
    public string Description { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    [Column("vendor")]
    public string Vendor { get; set; } = string.Empty;

    [MaxLength(255)]
    [Column("website_url")]
    public string? WebsiteUrl { get; set; }

    [Required]
    [Column("category_id")]
    public int CategoryId { get; set; }

    [Required]
    [Column("monthly_cost", TypeName = "decimal(10,2)")]
    public decimal MonthlyCost { get; set; }

    [Required]
    [Column("active_users_count")]
    public int ActiveUsersCount { get; set; }

    [Required]
    [MaxLength(50)]
    [Column("owner_department")]
    public string OwnerDepartment { get; set; } = string.Empty;

    [Required]
    [MaxLength(20)]
    [Column("status")]
    public string Status { get; set; } = "active";

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    [Column("updated_at")]
    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    // Navigation property
    [ForeignKey("CategoryId")]
    public Category? Category { get; set; }
}

/// <summary>
/// Tool category
/// </summary>
[Table("categories")]
public class Category
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Required]
    [MaxLength(50)]
    [Column("name")]
    public string Name { get; set; } = string.Empty;

    [Column("description")]
    public string? Description { get; set; }

    // Navigation property
    public ICollection<Tool> Tools { get; set; } = new List<Tool>();
}
