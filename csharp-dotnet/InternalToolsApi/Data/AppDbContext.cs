using Microsoft.EntityFrameworkCore;
using InternalToolsApi.Models;

namespace InternalToolsApi.Data;

/// <summary>
/// Database context for Internal Tools Management API
/// </summary>
public class AppDbContext : DbContext
{
    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options)
    {
    }

    public DbSet<Tool> Tools { get; set; }
    public DbSet<Category> Categories { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        // Configure Tool entity
        modelBuilder.Entity<Tool>(entity =>
        {
            entity.HasKey(e => e.Id);
            entity.Property(e => e.Id).ValueGeneratedOnAdd();
            
            entity.Property(e => e.Name).IsRequired().HasMaxLength(100);
            entity.Property(e => e.Description).IsRequired();
            entity.Property(e => e.Vendor).IsRequired().HasMaxLength(100);
            entity.Property(e => e.WebsiteUrl).HasMaxLength(255);
            entity.Property(e => e.MonthlyCost).HasColumnType("decimal(10,2)").IsRequired();
            entity.Property(e => e.ActiveUsersCount).IsRequired();
            entity.Property(e => e.OwnerDepartment).IsRequired().HasMaxLength(50);
            entity.Property(e => e.Status).IsRequired().HasMaxLength(20).HasDefaultValue("active");
            entity.Property(e => e.CreatedAt).HasDefaultValueSql("CURRENT_TIMESTAMP");
            entity.Property(e => e.UpdatedAt).HasDefaultValueSql("CURRENT_TIMESTAMP");

            // Configure relationship
            entity.HasOne(e => e.Category)
                  .WithMany(c => c.Tools)
                  .HasForeignKey(e => e.CategoryId)
                  .OnDelete(DeleteBehavior.Restrict);
        });

        // Configure Category entity
        modelBuilder.Entity<Category>(entity =>
        {
            entity.HasKey(e => e.Id);
            entity.Property(e => e.Id).ValueGeneratedOnAdd();
            entity.Property(e => e.Name).IsRequired().HasMaxLength(50);
        });
    }
}
