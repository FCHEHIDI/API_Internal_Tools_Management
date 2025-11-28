# Spring Boot CRUD Architecture - Request Flow Pipeline

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
│  LAYER 1: CONTROLLER (Web/API Layer - HTTP Entry Point)                     │
│  📁 controller/ToolController.java                                          │
├─────────────────────────────────────────────────────────────────────────────┤
│  @RestController                         // Marks as REST endpoint          │
│  @RequestMapping("/api/tools")           // Base URL path                   │
│  public class ToolController {                                              │
│                                                                             │
│    @PostMapping                          // HTTP POST mapping               │
│    public ResponseEntity<ToolResponse> createTool(                          │
│        @Valid @RequestBody CreateToolRequest request  // ← DTO Input        │
│    ) {                                                                      │
│        // Step 1: @Valid triggers validation on DTO                         │
│        // Step 2: Call service layer for business logic                     │
│        ToolResponse response = toolService.createTool(request);             │
│        // Step 3: Return HTTP 201 Created with response                     │
│        return ResponseEntity.status(HttpStatus.CREATED).body(response);     │
│    }                                                                        │
│  }                                                                          │
│                                                                             │
│  ROLE: HTTP request handling, routing, response formatting                  │
│  INPUT: HTTP request + CreateToolRequest DTO (validated)                    │
│  OUTPUT: HTTP response + ToolResponse DTO                                   │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │   @Valid annotation     │
                    │   triggers validation   │
                    └────────────┬────────────┘
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 2: DTO (Data Transfer Objects - API Contract)                       ,│
│  📁 dto/CreateToolRequest.java                                             |
├─────────────────────────────────────────────────────────────────────────────┤
│  @Data                                   // Lombok: getters/setters         │
│  public class CreateToolRequest {                                           │
│                                                                             │
│    @NotBlank(message = "Name required")  // Validation rule                 │
│    @Size(min = 2, max = 100)            // Length constraint                │
│    private String name;                                                     │
│                                                                             │
│    @NotNull(message = "Monthly cost required")                              │
│    @DecimalMin("0.0")                   // Must be positive                 │
│    @Digits(integer=10, fraction=2)      // Max 2 decimals                   │
│    private BigDecimal monthlyCost;                                          │
│                                                                             │
│    @NotNull                                                                 │
│    private Department ownerDepartment;  // ENUM validation                  │
│                                                                             │
│    private ToolStatus status;           // Optional field                   │
│  }                                                                          │
│                                                                             │
│  ROLE: API contract, input validation, data structure definition            │
│  INPUT: JSON from HTTP request body                                         │
│  OUTPUT: Validated Java object passed to service                            │
│                                                                             │
│  IF VALIDATION FAILS: Throws MethodArgumentNotValidException ────┐          │
└────────────────────────────────┬────────────────────────────────┘│          │
                                 │                                 │          │
                                 ▼                                 │          │
┌───────────────────────────────────────────────────────────────── ┼──────────┤
│  LAYER 3: SERVICE (Business Logic Layer)                         │          │
│  📁 service/ToolService.java                                     │          │
├──────────────────────────────────────────────────────────────────┼──────────┤
│  @Service                               // Spring component      │          │
│  public class ToolService {                                      │          │
│                                                                  │          │
│    @Transactional                       // Database transaction  │          │
│    public ToolResponse createTool(CreateToolRequest request) {   │          │
│                                                                  │          │
│      // STEP 1: Validate category exists (business rule)         │          │
│      Category category = categoryRepository                      │          │
│          .findById(request.getCategoryId())                      │          │
│          .orElseThrow(() -> new ResourceNotFoundException(...)); │ ─ ─ ─ ─ ─│─ ─ ─┐
│                                                                  │          │     │
│      // STEP 2: Map DTO to Entity                                │          │     │
│      Tool tool = new Tool();                                     │          │     │
│      tool.setName(request.getName());                            │          │     │
│      tool.setMonthlyCost(request.getMonthlyCost());              │          │     │
│      tool.setOwnerDepartment(request.getOwnerDepartment());      │          │     │
│      tool.setCategory(category);                                 │          │     │
│      tool.setStatus(request.getStatus() != null ?                │          │     │
│                     request.getStatus() : ToolStatus.active);    │          │     │
│      tool.setActiveUsersCount(0);  // Business logic             │          │     │
│                                                                  │          │     │
│      // STEP 3: Save to database via repository                  │          │     │
│      Tool savedTool = toolRepository.save(tool);                 │          │     │
│                                ↓                                 │          │     │
│      // STEP 4: Convert entity back to DTO                       │          │     │
│      return ToolResponse.fromEntity(savedTool);                  │          │     │
│    }                                                             │          │     │
│  }                                                               │          │     │
│                                                                  │          │     │
│  ROLE: Business logic, validation, orchestration, transactions   │          │     │
│  INPUT: CreateToolRequest DTO (validated)                        │          │     │
│  OUTPUT: ToolResponse DTO                                        │          │     │
│  CALLS: Repository layer for data access                         │          │     │
└────────────────────────────────┬─────────────────────────────────┘          │     │
                                 │                                            │     │
                                 ▼                                            │     │
┌─────────────────────────────────────────────────────────────────────────────┼─────┤
│  LAYER 4: REPOSITORY (Data Access Layer)                                    │     │
│  📁 repository/ToolRepository.java                                          │     │
├─────────────────────────────────────────────────────────────────────────────┼─────┤
│  @Repository                                                                │     │
│  public interface ToolRepository extends JpaRepository<Tool, Long> {        │     │
│                                                                             │     │
│    // JpaRepository provides built-in methods:                              │     │
│    // - save(Tool tool)           → INSERT or UPDATE                        │     │
│    // - findById(Long id)         → SELECT by ID                            │     │
│    // - findAll()                 → SELECT all                              │     │
│    // - deleteById(Long id)       → DELETE                                  │     │
│    // - existsById(Long id)       → CHECK EXISTS                            │     │
│                                                                             │     │
│    // Custom query methods:                                                 │     │
│    List<Tool> findByStatus(ToolStatus status);                              │     │
│    List<Tool> findByOwnerDepartment(Department department);                 │     │
│                                                                             │     │
│    @Query("SELECT t FROM Tool t WHERE ...")  // JPQL custom query           │     │
│    List<Tool> findWithFilters(...);                                         │     │
│  }                                                                          │     │
│                                                                             │     │
│  ROLE: Database queries, CRUD operations abstraction                        │     │
│  INPUT: Entity objects or query parameters                                  │     │
│  OUTPUT: Entity objects from database                                       │     │
│  USES: JPA/Hibernate for SQL generation and execution                       │     │
└────────────────────────────────┬────────────────────────────────────────────┘     │
                                 │                                                  │
                                 ▼                                                  │
┌─────────────────────────────────────────────────────────────────────────────┐     │
│  LAYER 5: MODEL/ENTITY (Database Table Mapping)                             │     │
│  📁 model/Tool.java                                                         │    │
├─────────────────────────────────────────────────────────────────────────────┤    │
│  @Entity                                // JPA entity annotation            │    │
│  @Table(name = "tools")                 // Maps to 'tools' table            │    │
│  public class Tool {                                                        │    │
│                                                                             │    │
│    @Id                                  // Primary key                      │    │
│    @GeneratedValue(strategy = IDENTITY) // Auto-increment                   │    │
│    private Long id;                                                         │    │
│                                                                             │    │
│    @Column(nullable = false, unique = true)                                 │    │
│    private String name;                                                     │    │
│                                                                             │    │
│    @Column(name = "monthly_cost", precision = 10, scale = 2)                │    │
│    private BigDecimal monthlyCost;                                          │    │
│                                                                             │    │
│    @Enumerated(EnumType.STRING)         // Store as string                  │    │
│    @JdbcTypeCode(SqlTypes.NAMED_ENUM)   // PostgreSQL ENUM support          │    │
│    private Department ownerDepartment;                                      │    │
│                                                                             │    │
│    @ManyToOne(fetch = FetchType.EAGER)  // Relationship                     │    │
│    @JoinColumn(name = "category_id")                                        │    │
│    private Category category;                                               │    │
│                                                                             │    │
│    @PrePersist                          // Lifecycle hook                   │    │
│    protected void onCreate() {                                              │    │
│      createdAt = LocalDateTime.now();   // Auto-set timestamp               │    │
│      if (status == null) status = ToolStatus.active; // Default             │    │
│    }                                                                        │    │
│  }                                                                          │    │
│                                                                             │    │
│  ROLE: Database schema mapping, data structure, constraints                 │    │
│  INPUT: Data from repository save operations                                │    │
│  OUTPUT: Persisted data in PostgreSQL database                              │    │
│  GENERATES: SQL INSERT/UPDATE/SELECT statements via Hibernate               │    │
└────────────────────────────────┬────────────────────────────────────────────┘    │
                                 │                                                 │
                                 ▼                                                 │
┌─────────────────────────────────────────────────────────────────────────────┐    │
│                         DATABASE (PostgreSQL)                               │    │
│  📊 Table: tools                                                            │    │
├─────────────────────────────────────────────────────────────────────────────┤    │
│  SQL Generated by Hibernate:                                                │    │
│                                                                             │    │
│  INSERT INTO tools (                                                        │    │
│    name, description, vendor, monthly_cost,                                 │    │
│    owner_department, status, category_id,                                   │    │
│    active_users_count, created_at, updated_at                               │    │
│  ) VALUES (                                                                 │    │
│    'Slack', 'Team messaging', 'Slack Tech', 8.00,                           │    │
│    'Engineering'::department_type, 'active'::tool_status_type, 1,           │    │
│    0, NOW(), NOW()                                                          │    │
│  ) RETURNING id;                                                            │    │
│                                                                             │    │
│  Result: id = 21 (auto-generated)                                           │    │
└────────────────────────────────┬────────────────────────────────────────────┘    │
                                 │                                                 │
                ┌────────────────┴─────────────────┐                               │
                │  Transaction committed            │                              │
                │  Tool saved successfully          │                              │
                └────────────────┬─────────────────┘                               │
                                 │                                                 │
              ┌──────────────────┴──────────────────┐                              │
              │  RESPONSE FLOW (Going back up)      │                              │
              └──────────────────┬──────────────────┘                              │
                                 │                                                 │
                                 ▼                                                 │
┌─────────────────────────────────────────────────────────────────────────────┐    │
│  LAYER 6: DTO OUTPUT (Response Object)                                      │    │
│  📁 dto/ToolResponse.java                                                   │    │
├─────────────────────────────────────────────────────────────────────────────┤    │
│  public class ToolResponse {                                                │    │
│    private Long id;                     // From saved entity                │    │
│    private String name;                                                     │    │
│    private String category;             // From Category.name               │    │
│    private BigDecimal monthlyCost;                                          │    │
│    private BigDecimal totalMonthlyCost; // Calculated field                 │    │
│    private Department ownerDepartment;                                      │    │
│    private LocalDateTime createdAt;                                         │    │
│                                                                             │    │
│    public static ToolResponse fromEntity(Tool tool) {                       │    │
│      return ToolResponse.builder()                                          │    │
│        .id(tool.getId())                // Map entity fields to DTO         │    │
│        .name(tool.getName())                                                │    │
│        .category(tool.getCategory().getName()) // Flatten relationship      │    │
│        .monthlyCost(tool.getMonthlyCost())                                  │    │
│        .totalMonthlyCost(                                                   │    │
│          tool.getMonthlyCost()                                              │    │
│            .multiply(valueOf(tool.getActiveUsersCount()))                   │    │
│        )                                                                    │    │
│        .build();                                                            │    │
│    }                                                                        │    │
│  }                                                                          │    │
│                                                                             │    │
│  ROLE: API response contract, data transformation for clients               │    │
│  INPUT: Tool entity from database                                           │    │
│  OUTPUT: Clean JSON response (hides internal structure)                     │    │
└────────────────────────────────┬────────────────────────────────────────────┘    │
                                 │                                                 │
                                 ▼                                                 │
┌─────────────────────────────────────────────────────────────────────────────┐    │
│                      HTTP RESPONSE TO CLIENT                                │    │
│  Status: 201 Created                                                        │    │
│  Content-Type: application/json                                             │    │
│  Body:                                                                      │    │
│  {                                                                          │    │
│    "id": 21,                                                                │    │
│    "name": "Slack",                                                         │    │
│    "description": "Team messaging platform",                                │    │
│    "vendor": "Slack Technologies",                                          │    │
│    "category": "Communication",                                             │    │
│    "monthlyCost": 8.00,                                                     │    │
│    "totalMonthlyCost": 0.00,                                                │    │
│    "ownerDepartment": "Engineering",                                        │    │
│    "status": "active",                                                      │    │
│    "activeUsersCount": 0,                                                   │    │
│    "createdAt": "2025-11-28T15:30:00",                                      │    │
│    "updatedAt": "2025-11-28T15:30:00"                                       │    │
│  }                                                                          │    │
└─────────────────────────────────────────────────────────────────────────────┘    │
                                                                                   │
┌────────────────────────────────────────────────────────────────────────────────┐ │
│  ERROR PATH (EXCEPTION HANDLING)                                               │ │
│  📁 exception/GlobalExceptionHandler.java                      ◄─────────────────┘
├────────────────────────────────────────────────────────────────────────────────┤
│  @RestControllerAdvice                  // Global exception interceptor        │
│  public class GlobalExceptionHandler {                                         │
│                                                                                │
│    // Validation errors from @Valid                                            │
│    @ExceptionHandler(MethodArgumentNotValidException.class)                    │
│    public ResponseEntity<ErrorResponse> handleValidation(exception) {          │
│      Map<String, String> errors = new HashMap<>();                             │
│      exception.getBindingResult().getFieldErrors()                             │
│        .forEach(error -> errors.put(                                           │
│          error.getField(),        // "name"                                    │
│          error.getDefaultMessage() // "Name is required"                       │
│        ));                                                                     │
│                                                                                │
│      return ResponseEntity.status(400).body(                                   │
│        new ErrorResponse("Validation failed", errors)                          │
│      );                                                                        │
│    }                                                                           │
│                                                                                │
│    // Resource not found (from service layer)                                  │
│    @ExceptionHandler(ResourceNotFoundException.class)                          │
│    public ResponseEntity<ErrorResponse> handleNotFound(exception) {            │
│      return ResponseEntity.status(404).body(                                   │
│        new ErrorResponse("Resource not found", exception.getMessage())         │
│      );                                                                        │
│    }                                                                           │
│                                                                                │
│    // Generic errors                                                           │
│    @ExceptionHandler(Exception.class)                                          │
│    public ResponseEntity<ErrorResponse> handleGeneric(exception) {             │
│      return ResponseEntity.status(500).body(                                   │
│        new ErrorResponse("Internal server error", exception.getMessage())      │
│      );                                                                        │
│    }                                                                           │
│  }                                                                             │
│                                                                                │
│  ROLE: Centralized error handling, consistent error responses                  │
│  CATCHES: All exceptions from any layer                                        │
│  OUTPUT: Standardized ErrorResponse DTO with HTTP status codes                 │
└────────────────────────────────────────────────────────────────────────────────┘
```

## 🎯 Key Concepts Summary

### **1. Separation of Concerns**
Each layer has a single responsibility:
- **Controller**: HTTP routing only
- **DTO**: API contract & validation
- **Service**: Business logic & orchestration
- **Repository**: Database queries
- **Entity**: Database structure
- **Exception Handler**: Error responses

### **2. Data Flow Transformation**
```
JSON Request → CreateToolRequest DTO → Tool Entity → Database
Database → Tool Entity → ToolResponse DTO → JSON Response
```

### **3. Why This Structure?**
- **Testability**: Each layer can be tested independently
- **Maintainability**: Changes to API don't affect database structure
- **Security**: DTOs prevent over-posting attacks
- **Flexibility**: Can change database without changing API
- **Reusability**: Services can be called from multiple controllers

### **4. Comparison to Other Layers**

| Layer | C# .NET | Java Spring Boot | TypeScript NestJS |
|-------|---------|------------------|-------------------|
| Controller | `[ApiController]` | `@RestController` | `@Controller()` |
| DTO | Data Annotations | `@Valid` annotations | class-validator |
| Service | Service classes | `@Service` class | `@Injectable()` class |
| Repository | EF DbContext | `JpaRepository` | TypeORM Repository |
| Model | Entity classes | `@Entity` class | `@Entity()` class |

### **5. Transaction Flow**
```
@Transactional annotation ensures:
├─ All database operations succeed together
├─ Automatic rollback on exceptions
└─ Connection pool management
```

### **6. The Magic of Attributes**
```csharp
[ApiController]      → API controller behavior
[Route("api/[controller]")] → Route template
[HttpPost]           → HTTP POST mapping
[Required]           → Required field validation
[Range(0, 1000)]     → Value range validation
[Table("tools")]     → Database table mapping
[Column("name")]     → Column name mapping
```

## 📝 Complete CRUD Operation Examples

### CREATE (POST)
```
Client Request → Controller (@PostMapping)
              → Validate DTO (@Valid)
              → Service.createTool()
              → Repository.save()
              → Database INSERT
              → Return ToolResponse (201 Created)
```

### READ (GET)
```
Client Request → Controller (@GetMapping)
              → Service.getToolById(id)
              → Repository.findById()
              → Database SELECT
              → Return ToolResponse (200 OK)
```

### UPDATE (PUT)
```
Client Request → Controller (@PutMapping)
              → Validate DTO (@Valid)
              → Service.updateTool(id, dto)
              → Repository.findById() + save()
              → Database SELECT + UPDATE
              → Return ToolResponse (200 OK)
```

### DELETE
```
Client Request → Controller (@DeleteMapping)
              → Service.deleteTool(id)
              → Repository.deleteById()
              → Database DELETE
              → Return 204 No Content
```

### LIST with FILTERS
```
Client Request → Controller (@GetMapping with @RequestParam)
              → Service.getAllTools(filters)
              → Repository.findWithFilters() [@Query JPQL]
              → Database SELECT with WHERE
              → Return ToolListResponse (200 OK)
```

---

**This architecture ensures:**
✅ Clean separation of concerns  
✅ Easy testing at each layer  
✅ Type safety with DTOs  
✅ Automatic SQL generation  
✅ Consistent error handling  
✅ Transaction management  
✅ Validation before business logic

