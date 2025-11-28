# C# + .NET CRUD Architecture - Request Flow Pipeline

## 📊 Complete Request Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           CLIENT REQUEST                                    │
│                  POST /api/tools (Create New Tool)                          │
│                  Content-Type: application/json                             │
│                  Body: {"name":"Slack", "vendor":"Slack",...}               │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 1: CONTROLLER (ASP.NET Core API Controller)                          │
│  📁 Controllers/ToolController.cs                                           │
├─────────────────────────────────────────────────────────────────────────────┤
│  [ApiController]                                                            │
│  [Route("api/[controller]")]                                                │
│  public class ToolController : ControllerBase                               │
│  {                                                                          │
│      private readonly IToolService _toolService;                            │
│                                                                             │
│      public ToolController(IToolService toolService)                        │
│      {                                                                      │
│          _toolService = toolService;  // Dependency injection               │
│      }                                                                      │
│                                                                             │
│      [HttpPost]                       // POST /api/tool                     │
│      [ProducesResponseType(201)]      // Return 201 Created                 │
│      public async Task<ActionResult<ToolResponseDto>> CreateTool(           │
│          [FromBody] CreateToolRequest request  // Auto-validation via attrs│
│      )                                                                      │
│      {                                                                      │
│          // Step 1: ModelState validates request automatically              │
│          if (!ModelState.IsValid)                                           │
│          {                                                                  │
│              return BadRequest(ModelState);                                 │
│          }                                                                  │
│                                                                             │
│          // Step 2: Call service layer for business logic                   │
│          var tool = await _toolService.CreateToolAsync(request);            │
│                                                                             │
│          // Step 3: Return 201 Created with location header                 │
│          return CreatedAtAction(                                            │
│              nameof(GetTool),                                               │
│              new { id = tool.Id },                                          │
│              tool                                                           │
│          );                                                                 │
│      }                                                                      │
│  }                                                                          │
│                                                                             │
│  ROLE: HTTP request handling, routing, model binding                        │
│  INPUT: HTTP request + CreateToolRequest (validated by ModelState)          │
│  OUTPUT: HTTP 201 + ToolResponseDto as JSON                                 │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │   Model validation      │
                    │   + Data annotations    │
                    └────────────┬────────────┘
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 2: DTOs (Data Transfer Objects with Data Annotations)                │
│  📁 Models/Dtos/CreateToolRequest.cs                                        │
├─────────────────────────────────────────────────────────────────────────────┤
│  using System.ComponentModel.DataAnnotations;                               │
│                                                                             │
│  // C# Enums (mapped to PostgreSQL ENUMs)                                   │
│  public enum Department                                                     │
│  {                                                                          │
│      Engineering,                                                           │
│      Sales,                                                                 │
│      Marketing,                                                             │
│      IT,                                                                    │
│      HR,                                                                    │
│      Finance,                                                               │
│      Operations                                                             │
│  }                                                                          │
│                                                                             │
│  public enum ToolStatus                                                     │
│  {                                                                          │
│      Active,                                                                │
│      Deprecated,                                                            │
│      Trial                                                                  │
│  }                                                                          │
│                                                                             │
│  // Request DTO (input validation)                                          │
│  public class CreateToolRequest                                             │
│  {                                                                          │
│      [Required]                                                             │
│      [StringLength(100, MinimumLength = 2)]                                 │
│      public string Name { get; set; }                                       │
│                                                                             │
│      [StringLength(500)]                                                    │
│      public string? Description { get; set; }                               │
│                                                                             │
│      [Required]                                                             │
│      [StringLength(100, MinimumLength = 1)]                                 │
│      public string Vendor { get; set; }                                     │
│                                                                             │
│      [Url]                                                                  │
│      public string? WebsiteUrl { get; set; }                                │
│                                                                             │
│      [Required]                                                             │
│      [Range(0, double.MaxValue)]                                            │
│      public decimal MonthlyCost { get; set; }                               │
│                                                                             │
│      [Required]                                                             │
│      [Range(1, int.MaxValue)]                                               │
│      public int CategoryId { get; set; }                                    │
│                                                                             │
│      [Required]                                                             │
│      public Department OwnerDepartment { get; set; }                        │
│                                                                             │
│      public ToolStatus Status { get; set; } = ToolStatus.Active;            │
│                                                                             │
│      [Range(0, int.MaxValue)]                                               │
│      public int ActiveUsersCount { get; set; } = 0;                         │
│  }                                                                          │
│                                                                             │
│  // Response DTO                                                            │
│  public class ToolResponseDto                                               │
│  {                                                                          │
│      public int Id { get; set; }                                            │
│      public string Name { get; set; }                                       │
│      public string? Description { get; set; }                               │
│      public string Vendor { get; set; }                                     │
│      public string? WebsiteUrl { get; set; }                                │
│      public string CategoryName { get; set; }                               │
│      public decimal MonthlyCost { get; set; }                               │
│      public decimal TotalMonthlyCost { get; set; }                          │
│      public Department OwnerDepartment { get; set; }                        │
│      public ToolStatus Status { get; set; }                                 │
│      public int ActiveUsersCount { get; set; }                              │
│      public DateTime CreatedAt { get; set; }                                │
│      public DateTime UpdatedAt { get; set; }                                │
│  }                                                                          │
│                                                                             │
│  ROLE: Data validation, type safety, serialization                          │
│  INPUT: JSON from HTTP request                                              │
│  OUTPUT: Validated C# object (or ModelState errors)                         │
│                                                                             │
│  IF VALIDATION FAILS: Returns 400 Bad Request with ModelState ───────────┐  │
└────────────────────────────────┬─────────────────────────────────────────┘│  │
                                 │                                           │  │
                                 ▼                                           │  │
┌──────────────────────────────────────────────────────────────────────────┼──┤
│  LAYER 3: SERVICE (Business Logic Layer)                                │  │
│  📁 Services/ToolService.cs                                             │  │
├──────────────────────────────────────────────────────────────────────────┼──┤
│  public interface IToolService                                           │  │
│  {                                                                       │  │
│      Task<ToolResponseDto> CreateToolAsync(CreateToolRequest request);   │  │
│  }                                                                       │  │
│                                                                          │  │
│  public class ToolService : IToolService                                 │  │
│  {                                                                       │  │
│      private readonly ApplicationDbContext _context;                     │  │
│      private readonly IMapper _mapper;  // AutoMapper for DTO mapping    │  │
│                                                                          │  │
│      public ToolService(ApplicationDbContext context, IMapper mapper)   │  │
│      {                                                                   │  │
│          _context = context;                                             │  │
│          _mapper = mapper;                                               │  │
│      }                                                                   │  │
│                                                                          │  │
│      public async Task<ToolResponseDto> CreateToolAsync(                │  │
│          CreateToolRequest request                                      │  │
│      )                                                                   │  │
│      {                                                                   │  │
│          // STEP 1: Verify category exists (business rule)               │  │
│          var categoryExists = await _context.Categories                 │  │
│              .AnyAsync(c => c.Id == request.CategoryId);                │  │
│                                                                          │  │
│          if (!categoryExists)                                            │  │
│          {                                                               │  │
│              throw new NotFoundException(                                │ ─┘
│                  $"Category {request.CategoryId} not found"             │
│              );                                                          │
│          }                                                               │
│                                                                          │
│          // STEP 2: Map DTO to Entity                                    │
│          var tool = _mapper.Map<Tool>(request);                          │
│                                                                          │
│          // STEP 3: Add to context and save                              │
│          _context.Tools.Add(tool);                                       │
│          await _context.SaveChangesAsync();                              │
│                                                                          │
│          // STEP 4: Load relationships                                   │
│          await _context.Entry(tool)                                      │
│              .Reference(t => t.Category)                                 │
│              .LoadAsync();                                               │
│                                                                          │
│          // STEP 5: Map to response DTO                                  │
│          return _mapper.Map<ToolResponseDto>(tool);                      │
│      }                                                                   │
│  }                                                                       │
│                                                                          │
│  ROLE: Business logic, validation, transaction management               │
│  INPUT: Validated request DTO + DbContext                               │
│  OUTPUT: ToolResponseDto or throw exception                             │
└────────────────────────────────┬─────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 4: ENTITY FRAMEWORK CORE (ORM)                                      │
│  📁 Models/Entities/Tool.cs                                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│  using Microsoft.EntityFrameworkCore;                                       │
│  using System.ComponentModel.DataAnnotations;                               │
│  using System.ComponentModel.DataAnnotations.Schema;                        │
│                                                                             │
│  [Table("tools")]                                                           │
│  public class Tool                                                          │
│  {                                                                          │
│      [Key]                                                                  │
│      [DatabaseGenerated(DatabaseGeneratedOption.Identity)]                  │
│      public int Id { get; set; }                                            │
│                                                                             │
│      [Required]                                                             │
│      [MaxLength(100)]                                                       │
│      [Column("name")]                                                       │
│      public string Name { get; set; }                                       │
│                                                                             │
│      [MaxLength(500)]                                                       │
│      [Column("description")]                                                │
│      public string? Description { get; set; }                               │
│                                                                             │
│      [Required]                                                             │
│      [MaxLength(100)]                                                       │
│      [Column("vendor")]                                                     │
│      public string Vendor { get; set; }                                     │
│                                                                             │
│      [MaxLength(255)]                                                       │
│      [Column("website_url")]                                                │
│      public string? WebsiteUrl { get; set; }                                │
│                                                                             │
│      [Required]                                                             │
│      [Column("monthly_cost", TypeName = "decimal(10,2)")]                   │
│      public decimal MonthlyCost { get; set; }                               │
│                                                                             │
│      [Column("active_users_count")]                                         │
│      public int ActiveUsersCount { get; set; } = 0;                         │
│                                                                             │
│      // Foreign key relationship                                            │
│      [Required]                                                             │
│      [Column("category_id")]                                                │
│      public int CategoryId { get; set; }                                    │
│                                                                             │
│      [ForeignKey(nameof(CategoryId))]                                       │
│      public Category Category { get; set; }                                 │
│                                                                             │
│      // PostgreSQL ENUM columns (stored as strings)                         │
│      [Required]                                                             │
│      [Column("owner_department")]                                           │
│      public Department OwnerDepartment { get; set; }                        │
│                                                                             │
│      [Column("status")]                                                     │
│      public ToolStatus Status { get; set; } = ToolStatus.Active;            │
│                                                                             │
│      // Timestamps (auto-managed by EF Core)                                │
│      [Column("created_at")]                                                 │
│      public DateTime CreatedAt { get; set; }                                │
│                                                                             │
│      [Column("updated_at")]                                                 │
│      public DateTime UpdatedAt { get; set; }                                │
│  }                                                                          │
│                                                                             │
│  // DbContext configuration                                                 │
│  public class ApplicationDbContext : DbContext                              │
│  {                                                                          │
│      public DbSet<Tool> Tools { get; set; }                                 │
│      public DbSet<Category> Categories { get; set; }                        │
│                                                                             │
│      protected override void OnModelCreating(ModelBuilder modelBuilder)     │
│      {                                                                      │
│          // Configure PostgreSQL ENUMs                                      │
│          modelBuilder.HasPostgresEnum<Department>();                        │
│          modelBuilder.HasPostgresEnum<ToolStatus>();                        │
│                                                                             │
│          // Configure entity                                                │
│          modelBuilder.Entity<Tool>(entity =>                                │
│          {                                                                  │
│              entity.HasIndex(e => e.Name).IsUnique();                       │
│              entity.Property(e => e.CreatedAt).HasDefaultValueSql("NOW()");│
│              entity.Property(e => e.UpdatedAt).HasDefaultValueSql("NOW()");│
│          });                                                                │
│      }                                                                      │
│  }                                                                          │
│                                                                             │
│  ROLE: ORM mapping, database operations                                     │
│  INPUT: C# entities                                                         │
│  OUTPUT: SQL INSERT/UPDATE/SELECT via Entity Framework Core                 │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         DATABASE (PostgreSQL 15)                            │
│  📊 Table: tools                                                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  SQL Generated by Entity Framework Core:                                    │
│                                                                             │
│  INSERT INTO tools (                                                        │
│    name, description, vendor, website_url, monthly_cost,                    │
│    category_id, owner_department, status,                                   │
│    active_users_count, created_at, updated_at                               │
│  ) VALUES (                                                                 │
│    @p0, @p1, @p2, @p3, @p4, @p5, @p6::department_type,                      │
│    @p7::tool_status_type, @p8, NOW(), NOW()                                 │
│  ) RETURNING id, created_at, updated_at;                                    │
│                                                                             │
│  Parameters:                                                                │
│    @p0='Slack', @p1='Team messaging', @p2='Slack Technologies',             │
│    @p3='https://slack.com', @p4=8.00, @p5=1,                                │
│    @p6='Engineering', @p7='active', @p8=0                                   │
│                                                                             │
│  Result: Tool(id=21, created_at='2025-11-28 16:30:00', ...)                 │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
              ┌──────────────────┴──────────────────┐
              │  RESPONSE FLOW (Going back up)      │
              └──────────────────┬──────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                      HTTP RESPONSE TO CLIENT                                │
│  Status: 201 Created                                                        │
│  Location: /api/tool/21                                                     │
│  Content-Type: application/json                                             │
│  Body:                                                                      │
│  {                                                                          │
│    "id": 21,                                                                │
│    "name": "Slack",                                                         │
│    "description": "Team messaging platform",                                │
│    "vendor": "Slack Technologies",                                          │
│    "websiteUrl": "https://slack.com",                                       │
│    "categoryName": "Communication",                                         │
│    "monthlyCost": 8.00,                                                     │
│    "totalMonthlyCost": 0.00,                                                │
│    "ownerDepartment": "Engineering",                                        │
│    "status": "Active",                                                      │
│    "activeUsersCount": 0,                                                   │
│    "createdAt": "2025-11-28T16:30:00Z",                                     │
│    "updatedAt": "2025-11-28T16:30:00Z"                                      │
│  }                                                                          │
└─────────────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────────┐
│  ERROR HANDLING (Exception Middleware)                                     │
│  📁 Middleware/ExceptionHandlingMiddleware.cs                              │
├────────────────────────────────────────────────────────────────────────────┤
│  public class ExceptionHandlingMiddleware                                  │
│  {                                                                         │
│      private readonly RequestDelegate _next;                               │
│      private readonly ILogger<ExceptionHandlingMiddleware> _logger;        │
│                                                                            │
│      public async Task InvokeAsync(HttpContext context)                    │
│      {                                                                     │
│          try                                                               │
│          {                                                                 │
│              await _next(context);                                         │
│          }                                                                 │
│          catch (Exception ex)                                              │
│          {                                                                 │
│              await HandleExceptionAsync(context, ex);                      │
│          }                                                                 │
│      }                                                                     │
│                                                                            │
│      private async Task HandleExceptionAsync(                              │
│          HttpContext context,                                              │
│          Exception exception                                               │
│      )                                                                     │
│      {                                                                     │
│          _logger.LogError(exception, "An error occurred");                 │
│                                                                            │
│          var (statusCode, message) = exception switch                      │
│          {                                                                 │
│              NotFoundException => (404, exception.Message),                │
│              ValidationException => (400, exception.Message),              │
│              DbUpdateException => (409, "Database conflict"),              │
│              _ => (500, "Internal server error")                           │
│          };                                                                │
│                                                                            │
│          context.Response.StatusCode = statusCode;                         │
│          context.Response.ContentType = "application/json";                │
│                                                                            │
│          var response = new                                                │
│          {                                                                 │
│              StatusCode = statusCode,                                      │
│              Error = message,                                              │
│              Timestamp = DateTime.UtcNow                                   │
│          };                                                                │
│                                                                            │
│          await context.Response.WriteAsJsonAsync(response);                │
│      }                                                                     │
│  }                                                                         │
│                                                                            │
│  // Register in Program.cs                                                 │
│  app.UseMiddleware<ExceptionHandlingMiddleware>();                         │
│                                                                            │
│  ROLE: Centralized exception handling, standardized error responses        │
│  CATCHES: NotFoundException, ValidationException, DbUpdateException, etc.  │
│  OUTPUT: Consistent JSON error format                                      │
└────────────────────────────────────────────────────────────────────────────┘
```

## 🎯 Key C#/.NET Concepts

### **1. Attributes (Like Decorators)**
```csharp
// Attributes add metadata for validation, routing, ORM
[ApiController]                   // Enables automatic model validation
[Route("api/[controller]")]       // Routing template
public class ToolController : ControllerBase
{
    [HttpPost]                    // HTTP method
    [ProducesResponseType(201)]   // OpenAPI documentation
    public async Task<ActionResult<Tool>> Create(
        [FromBody] CreateToolRequest request  // Model binding
    ) { }
}

[Table("tools")]                  // ORM table mapping
public class Tool
{
    [Key]                         // Primary key
    [Required]                    // NOT NULL constraint
    [MaxLength(100)]              // VARCHAR(100)
    public string Name { get; set; }
}
```

### **2. Async/Await (Task-Based Asynchronous Pattern)**
```csharp
// C# async/await with Task<T>
public async Task<Tool> CreateToolAsync(CreateToolRequest request)
{
    var category = await _context.Categories.FindAsync(request.CategoryId);
    //                   ^^^^^ Non-blocking wait
    
    var tool = await _context.SaveChangesAsync();
    //               ^^^^^ Non-blocking save
    
    return tool;
}

// Task = Promise in JavaScript
// async/await syntax is identical to TypeScript/JavaScript!
```

### **3. LINQ (Language Integrated Query)**
```csharp
// Query syntax (looks like SQL)
var activeTools = from tool in _context.Tools
                  where tool.Status == ToolStatus.Active
                  orderby tool.CreatedAt descending
                  select tool;

// Method syntax (functional style)
var activeTools = _context.Tools
    .Where(t => t.Status == ToolStatus.Active)
    .OrderByDescending(t => t.CreatedAt)
    .ToListAsync();

// Both compile to SQL!
```

### **4. Dependency Injection (Built-in)**
```csharp
// Register services in Program.cs
builder.Services.AddScoped<IToolService, ToolService>();
builder.Services.AddDbContext<ApplicationDbContext>();

// Inject in constructor
public class ToolController : ControllerBase
{
    private readonly IToolService _toolService;
    
    public ToolController(IToolService toolService)
    {
        _toolService = toolService;  // Auto-injected!
    }
}
```

### **5. Properties with Get/Set**
```csharp
// C# properties (not fields!)
public class Tool
{
    // Auto-property (backing field auto-generated)
    public string Name { get; set; }
    
    // Computed property
    public decimal TotalCost => MonthlyCost * ActiveUsersCount;
    
    // Property with validation
    private string _name;
    public string Name
    {
        get => _name;
        set => _name = value?.Trim() ?? throw new ArgumentNullException();
    }
}
```

## 📝 Complete CRUD Operations Flow

### **CREATE (POST /api/tools)**
```
Client → ASP.NET Controller ([HttpPost])
      → Model validation (Data Annotations)
      → Service layer (business logic)
      → Entity Framework Core (INSERT)
      → PostgreSQL database
      → Return ToolResponseDto (201 Created)
```

### **READ (GET /api/tools/{id})**
```
Client → ASP.NET Controller ([HttpGet("{id}")])
      → Service layer
      → EF Core FirstOrDefaultAsync (SELECT WHERE id = ?)
      → PostgreSQL database
      → Return ToolResponseDto (200 OK)
```

### **UPDATE (PUT /api/tools/{id})**
```
Client → ASP.NET Controller ([HttpPut("{id}")])
      → Model validation
      → Service layer (fetch + update)
      → EF Core Update + SaveChangesAsync
      → PostgreSQL database
      → Return updated ToolResponseDto (200 OK)
```

### **DELETE (DELETE /api/tools/{id})**
```
Client → ASP.NET Controller ([HttpDelete("{id}")])
      → Service layer
      → EF Core Remove + SaveChangesAsync
      → PostgreSQL database
      → Return 204 No Content
```

### **LIST with FILTERS (GET /api/tools?department=Engineering)**
```
Client → ASP.NET Controller (with [FromQuery])
      → Service layer builds LINQ query
      → EF Core Where() clauses
      → PostgreSQL WHERE
      → Return List<ToolResponseDto> (200 OK)
```

## 🔥 C#/.NET Advantages

✅ **Type Safety** - Strong typing with compile-time checks  
✅ **LINQ** - SQL-like queries integrated in language  
✅ **Entity Framework Core** - Powerful ORM with migrations  
✅ **Async/Await** - Task-based async programming (mature!)  
✅ **Dependency Injection** - Built-in IoC container  
✅ **Performance** - JIT compilation + native code generation  

## 🆚 C# vs Other Stacks

| Feature | C# .NET | Java Spring Boot | TypeScript NestJS |
|---------|---------|------------------|-------------------|
| **Type Safety** | ⭐⭐⭐⭐⭐ Compile-time | ⭐⭐⭐⭐⭐ Compile-time | ⭐⭐⭐⭐⭐ Compile-time |
| **Performance** | ⭐⭐⭐⭐⭐ Very fast (JIT) | ⭐⭐⭐⭐⭐ Very fast | ⭐⭐⭐⭐ Fast (V8) |
| **Learning Curve** | ⭐⭐⭐⭐ Moderate | ⭐⭐⭐⭐ Steep | ⭐⭐⭐ Moderate |
| **LINQ** | ✅ Built-in language feature | ❌ Stream API only | ❌ Array methods |
| **Async Model** | Task-based (async/await) | Virtual Threads | Event loop |
| **ORM** | Entity Framework Core | Hibernate/JPA | TypeORM |
| **Properties** | `{ get; set; }` | Getters/setters | TypeScript props |
| **Nullability** | `string?` (nullable reference types) | `Optional<T>` | `string \| null` |
| **Platform** | Cross-platform (.NET 6+) | Cross-platform (JVM) | Node.js |

## 💡 Why C# + .NET?

1. **Modern Language** - C# evolves rapidly (records, pattern matching, LINQ)
2. **LINQ Power** - Query anything (DB, collections, XML) with same syntax
3. **Entity Framework Core** - Best-in-class ORM experience
4. **Visual Studio** - Excellent IDE with IntelliSense
5. **Cross-Platform** - .NET 6+ runs on Linux, macOS, Windows
6. **Performance** - Comparable to Java, faster than Node.js/Python

## 🏗️ ASP.NET Core Architecture

**Middleware Pipeline:**
```
Request → Authentication → Authorization → Routing → 
       → Controller → Service → Repository → Database
```

**Dependency Injection Lifetimes:**
- **Transient** - New instance every time
- **Scoped** - One instance per HTTP request
- **Singleton** - One instance for app lifetime

## 🆕 Modern C# Features

### **Records (C# 9+)**
```csharp
// Immutable data structures
public record ToolDto(
    int Id,
    string Name,
    decimal MonthlyCost
);

// Auto-generates: constructor, ToString, Equals, GetHashCode
```

### **Pattern Matching (C# 8+)**
```csharp
public string GetStatusMessage(Tool tool) => tool.Status switch
{
    ToolStatus.Active => "Tool is active",
    ToolStatus.Deprecated => "Tool is deprecated",
    ToolStatus.Trial => "Tool is in trial",
    _ => "Unknown status"
};
```

### **Nullable Reference Types (C# 8+)**
```csharp
// Opt-in nullability checking at compile time
string name = null;   // ⚠️ Warning: Assigning null to non-nullable
string? description = null;  // ✅ OK, explicitly nullable
```

## ⚠️ C#/.NET Trade-offs

- **Windows Legacy** - Historically Windows-only (but .NET Core changed this)
- **Verbosity** - More boilerplate than Python/Go
- **JIT Warmup** - Slow cold start (but getting better)
- **Ecosystem Lock-in** - Microsoft stack (but improving)
- **But** → Trade for enterprise features and performance! 🚀

---

**This C# .NET architecture ensures:**
✅ Type-safe code with compile-time checks  
✅ LINQ for powerful, readable queries  
✅ Entity Framework Core for ORM excellence  
✅ Async/await with Task-based pattern  
✅ Built-in dependency injection  
✅ PostgreSQL ENUM support via Npgsql
