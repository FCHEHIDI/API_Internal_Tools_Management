# NestJS + TypeORM CRUD Architecture - Request Flow Pipeline

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
│  📁 tools/tools.controller.ts                                               │
├─────────────────────────────────────────────────────────────────────────────┤
│  @Controller('api/tools')                // Marks as REST endpoint          │
│  export class ToolsController {                                             │
│                                                                             │
│    constructor(private readonly toolsService: ToolsService) {}              │
│                                                                             │
│    @Post()                               // HTTP POST mapping               │
│    @HttpCode(HttpStatus.CREATED)                                            │
│    async create(                                                            │
│        @Body(ValidationPipe) createToolDto: CreateToolDto  // ← DTO Input   │
│    ): Promise<ToolResponse> {                                               │
│        // Step 1: ValidationPipe triggers class-validator on DTO            │
│        // Step 2: Call service layer for business logic                     │
│        const tool = await this.toolsService.create(createToolDto);          │
│        // Step 3: Return HTTP 201 Created with response                     │
│        return tool;                                                         │
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
│  LAYER 2: DTO (Data Transfer Objects - API Contract)                       │
│  📁 tools/dto/create-tool.dto.ts                                           │
├─────────────────────────────────────────────────────────────────────────────┤
│  export class CreateToolDto {                                               │
│                                                                             │
│    @IsString()                           // Type validation                 │
│    @IsNotEmpty({ message: 'Name required' })                                │
│    @Length(2, 100)                       // Length constraint               │
│    name: string;                                                            │
│                                                                             │
│    @IsNumber()                                                              │
│    @IsNotEmpty({ message: 'Monthly cost required' })                        │
│    @Min(0.0)                             // Must be positive               │
│    monthlyCost: number;                                                     │
│                                                                             │
│    @IsEnum(Department)                   // ENUM validation                 │
│    @IsNotEmpty()                                                            │
│    ownerDepartment: Department;                                             │
│                                                                             │
│    @IsEnum(ToolStatus)                                                      │
│    @IsOptional()                         // Optional field                  │
│    status?: ToolStatus;                                                     │
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
│  📁 tools/tools.service.ts                                       │          │
├──────────────────────────────────────────────────────────────────┼──────────┤
│  @Injectable()                          // NestJS service        │          │
│  export class ToolsService {                                     │          │
│                                                                  │          │
│    async create(createToolDto: CreateToolDto): Promise<Tool> {   │          │
│                                                                  │          │
│      // STEP 1: Validate category exists (business rule)         │          │
│      const category = await this.categoryRepository              │          │
│          .findOne({ where: { id: createToolDto.categoryId }});   │          │
│      if (!category) {                                            │ ─ ─ ─ ─ ─│─ ─ ─┐
│        throw new NotFoundException('Category not found');        │          │     │
│      }                                                           │          │     │
│                                                                  │          │     │
│      // STEP 2: Map DTO to Entity                                │          │     │
│      const tool = this.toolRepository.create({                   │          │     │
│        name: createToolDto.name,                                 │          │     │
│        monthlyCost: createToolDto.monthlyCost,                   │          │     │
│        ownerDepartment: createToolDto.ownerDepartment,           │          │     │
│        category: category,                                       │          │     │
│        status: createToolDto.status || ToolStatus.ACTIVE,        │          │     │
│        activeUsersCount: 0  // Business logic                    │          │     │
│      });                                                         │          │     │
│                                                                  │          │     │
│      // STEP 3: Save to database via repository                  │          │     │
│      const savedTool = await this.toolRepository.save(tool);     │          │     │
│                                ↓                                 │          │     │
│      // STEP 4: Return entity (auto-converted to response)       │          │     │
│      return savedTool;                                           │          │     │
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
│  📁 tools/entities/tool.entity.ts (TypeORM Repository Pattern)              │     │
├─────────────────────────────────────────────────────────────────────────────┼─────┤
│  // TypeORM Repository accessed via DataSource                              │     │
│  // Injected in service: @InjectRepository(Tool)                            │     │
│  private toolRepository: Repository<Tool>                                    │     │
│                                                                             │     │
│    // TypeORM Repository provides built-in methods:                         │     │
│    // - save(tool)                → INSERT or UPDATE                        │     │
│    // - findOne({ where: {...}})  → SELECT by condition                     │     │
│    // - find()                    → SELECT all                              │     │
│    // - delete(id)                → DELETE                                  │     │
│    // - count()                   → COUNT records                           │     │
│                                                                             │     │
│    // Custom query methods via QueryBuilder:                                │     │
│    await this.toolRepository                                                │     │
│      .createQueryBuilder('tool')                                            │     │
│      .where('tool.status = :status', { status })                            │     │
│      .getMany();                                                            │     │
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
│  📁 tools/entities/tool.entity.ts                                           │    │
├─────────────────────────────────────────────────────────────────────────────┤    │
│  @Entity('tools')                       // TypeORM entity                   │    │
│  export class Tool {                                                        │    │
│                                                                             │    │
│    @PrimaryGeneratedColumn()            // Primary key + auto-increment     │    │
│    id: number;                                                              │    │
│                                                                             │    │
│    @Column({ nullable: false, unique: true })                               │    │
│    name: string;                                                            │    │
│                                                                             │    │
│    @Column({ name: 'monthly_cost', type: 'decimal', precision: 10, scale: 2 })   │
│    monthlyCost: number;                                                     │    │
│                                                                             │    │
│    @Column({                                                                │    │
│      type: 'enum',                                                          │    │
│      enum: Department,                  // PostgreSQL ENUM support          │    │
│      enumName: 'department_type'                                            │    │
│    })                                                                       │    │
│    ownerDepartment: Department;                                             │    │
│                                                                             │    │
│    @ManyToOne(() => Category, { eager: true })  // Relationship             │    │
│    @JoinColumn({ name: 'category_id' })                                     │    │
│    category: Category;                                                      │    │
│                                                                             │    │
│    @CreateDateColumn()                  // Auto-set on insert               │    │
│    createdAt: Date;                                                         │    │
│                                                                             │    │
│    @BeforeInsert()                      // Lifecycle hook                   │    │
│    setDefaults() {                                                          │    │
│      if (!this.status) this.status = ToolStatus.ACTIVE;                     │    │
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

| Layer | TypeScript NestJS | Java Spring Boot | Python FastAPI |
|-------|-------------------|------------------|----------------|
| Controller | `@Controller()` | `@RestController` | `@app.post()` |
| DTO | class-validator decorators | `@Valid` annotations | Pydantic models |
| Service | `@Injectable()` class | `@Service` class | Service functions |
| Repository | TypeORM Repository | `JpaRepository` | SQLAlchemy ORM |
| Entity | `@Entity()` class | `@Entity` class | SQLAlchemy models |

### **5. Transaction Flow**
```
@Transactional annotation ensures:
├─ All database operations succeed together
├─ Automatic rollback on exceptions
└─ Connection pool management
```

### **6. The Magic of Decorators**
```typescript
@Controller()    → Makes class handle HTTP requests
@Post()          → Maps to HTTP POST method
@Body()          → Extracts request body
@Injectable()    → Marks as NestJS service (dependency injection)
@Entity()        → Maps to database table
@Column()        → Maps to table column
@BeforeInsert()  → Runs before INSERT
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

