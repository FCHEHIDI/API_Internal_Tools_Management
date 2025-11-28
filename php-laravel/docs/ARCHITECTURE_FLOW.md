# PHP + Laravel CRUD Architecture - Request Flow Pipeline

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
│  LAYER 1: CONTROLLER (Laravel API Resource Controller)                      │
│  📁 app/Http/Controllers/ToolController.php                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│  <?php                                                                      │
│                                                                             │
│  namespace App\Http\Controllers;                                            │
│                                                                             │
│  use App\Http\Requests\CreateToolRequest;                                   │
│  use App\Http\Resources\ToolResource;                                       │
│  use App\Services\ToolService;                                              │
│  use Illuminate\Http\JsonResponse;                                          │
│                                                                             │
│  class ToolController extends Controller                                    │
│  {                                                                          │
│      protected ToolService $toolService;                                    │
│                                                                             │
│      public function __construct(ToolService $toolService)                  │
│      {                                                                      │
│          $this->toolService = $toolService;  // Dependency injection        │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Create a new tool                                                   │
│       *                                                                     │
│       * @param CreateToolRequest $request  // Auto-validated!               │
│       * @return JsonResponse                                                │
│       */                                                                    │
│      public function store(CreateToolRequest $request): JsonResponse        │
│      {                                                                      │
│          // Step 1: Request already validated by FormRequest                │
│          // Laravel automatically runs $request->validate()                 │
│                                                                             │
│          // Step 2: Call service layer                                      │
│          $tool = $this->toolService->createTool($request->validated());     │
│                                                                             │
│          // Step 3: Transform to API Resource and return 201                │
│          return (new ToolResource($tool))                                   │
│              ->response()                                                   │
│              ->setStatusCode(201)                                           │
│              ->header('Location', route('tools.show', $tool->id));          │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Get all tools with optional filters                                 │
│       */                                                                    │
│      public function index(Request $request): JsonResponse                  │
│      {                                                                      │
│          $tools = $this->toolService->getTools($request->all());            │
│          return ToolResource::collection($tools)->response();               │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Get a single tool by ID                                             │
│       */                                                                    │
│      public function show(int $id): JsonResponse                            │
│      {                                                                      │
│          $tool = $this->toolService->getToolById($id);                      │
│          return (new ToolResource($tool))->response();                      │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Update a tool                                                       │
│       */                                                                    │
│      public function update(                                                │
│          UpdateToolRequest $request,                                        │
│          int $id                                                            │
│      ): JsonResponse                                                        │
│      {                                                                      │
│          $tool = $this->toolService->updateTool($id, $request->validated());│
│          return (new ToolResource($tool))->response();                      │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Delete a tool                                                       │
│       */                                                                    │
│      public function destroy(int $id): JsonResponse                         │
│      {                                                                      │
│          $this->toolService->deleteTool($id);                               │
│          return response()->json(null, 204);                                │
│      }                                                                      │
│  }                                                                          │
│                                                                             │
│  ROLE: HTTP handling, routing, response formatting                          │
│  INPUT: HTTP request + CreateToolRequest (auto-validated)                   │
│  OUTPUT: HTTP 201 + ToolResource (JSON)                                     │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │   FormRequest validates │
                    │   + Rules engine        │
                    └────────────┬────────────┘
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 2: FORM REQUEST (Validation Layer)                                   │
│  📁 app/Http/Requests/CreateToolRequest.php                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│  <?php                                                                      │
│                                                                             │
│  namespace App\Http\Requests;                                               │
│                                                                             │
│  use Illuminate\Foundation\Http\FormRequest;                                │
│  use Illuminate\Validation\Rule;                                            │
│                                                                             │
│  class CreateToolRequest extends FormRequest                                │
│  {                                                                          │
│      /**                                                                    │
│       * Determine if the user is authorized to make this request            │
│       */                                                                    │
│      public function authorize(): bool                                      │
│      {                                                                      │
│          return true;  // Can add authorization logic here                  │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Get the validation rules that apply to the request                  │
│       */                                                                    │
│      public function rules(): array                                         │
│      {                                                                      │
│          return [                                                           │
│              'name' => [                                                    │
│                  'required',                                                │
│                  'string',                                                  │
│                  'min:2',                                                   │
│                  'max:100',                                                 │
│                  'unique:tools,name'  // Database unique check              │
│              ],                                                             │
│              'description' => 'nullable|string|max:500',                    │
│              'vendor' => 'required|string|min:1|max:100',                   │
│              'website_url' => 'nullable|url|max:255',                       │
│              'monthly_cost' => 'required|numeric|min:0',                    │
│              'category_id' => [                                             │
│                  'required',                                                │
│                  'integer',                                                 │
│                  Rule::exists('categories', 'id')  // Foreign key check     │
│              ],                                                             │
│              'owner_department' => [                                        │
│                  'required',                                                │
│                  Rule::in([                                                 │
│                      'Engineering', 'Sales', 'Marketing',                   │
│                      'IT', 'HR', 'Finance', 'Operations'                    │
│                  ])                                                         │
│              ],                                                             │
│              'status' => [                                                  │
│                  'nullable',                                                │
│                  Rule::in(['active', 'deprecated', 'trial'])                │
│              ],                                                             │
│              'active_users_count' => 'nullable|integer|min:0'               │
│          ];                                                                 │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Get custom error messages for validator errors                      │
│       */                                                                    │
│      public function messages(): array                                      │
│      {                                                                      │
│          return [                                                           │
│              'name.required' => 'The tool name is required',                │
│              'name.unique' => 'A tool with this name already exists',       │
│              'category_id.exists' => 'The selected category does not exist',│
│              'website_url.url' => 'Please provide a valid URL'              │
│          ];                                                                 │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Prepare the data for validation (transform input)                   │
│       */                                                                    │
│      protected function prepareForValidation(): void                        │
│      {                                                                      │
│          $this->merge([                                                     │
│              'status' => $this->status ?? 'active',                         │
│              'active_users_count' => $this->active_users_count ?? 0         │
│          ]);                                                                │
│      }                                                                      │
│  }                                                                          │
│                                                                             │
│  ROLE: Input validation, data transformation, authorization                 │
│  INPUT: Raw HTTP request data                                               │
│  OUTPUT: Validated data array or ValidationException                        │
│                                                                             │
│  IF VALIDATION FAILS: Returns 422 Unprocessable Entity ─────────────────┐   │
└────────────────────────────────┬─────────────────────────────────────────┘│  │
                                 │                                           │  │
                                 ▼                                           │  │
┌──────────────────────────────────────────────────────────────────────────┼──┤
│  LAYER 3: SERVICE LAYER (Business Logic)                                │  │
│  📁 app/Services/ToolService.php                                        │  │
├──────────────────────────────────────────────────────────────────────────┼──┤
│  <?php                                                                  │  │
│                                                                          │  │
│  namespace App\Services;                                                │  │
│                                                                          │  │
│  use App\Models\Tool;                                                   │  │
│  use App\Models\Category;                                               │  │
│  use Illuminate\Database\Eloquent\Collection;                           │  │
│  use Illuminate\Support\Facades\DB;                                     │  │
│                                                                          │  │
│  class ToolService                                                      │  │
│  {                                                                      │  │
│      /**                                                                │  │
│       * Create a new tool                                               │  │
│       *                                                                 │  │
│       * @param array $data Validated data from request                  │  │
│       * @return Tool                                                    │  │
│       * @throws \Exception                                              │  │
│       */                                                                │  │
│      public function createTool(array $data): Tool                      │  │
│      {                                                                  │  │
│          // Use database transaction for atomic operations              │  │
│          return DB::transaction(function () use ($data) {               │  │
│                                                                          │  │
│              // STEP 1: Verify category exists (business rule)          │  │
│              $category = Category::find($data['category_id']);          │  │
│                                                                          │  │
│              if (!$category) {                                           │  │
│                  throw new \Exception(                                   │ ─┘
│                      "Category {$data['category_id']} not found",       │
│                      404                                                │
│                  );                                                     │
│              }                                                          │
│                                                                          │
│              // STEP 2: Create tool using Eloquent ORM                   │
│              $tool = Tool::create([                                      │
│                  'name' => $data['name'],                                │
│                  'description' => $data['description'] ?? null,          │
│                  'vendor' => $data['vendor'],                            │
│                  'website_url' => $data['website_url'] ?? null,          │
│                  'monthly_cost' => $data['monthly_cost'],                │
│                  'category_id' => $data['category_id'],                  │
│                  'owner_department' => $data['owner_department'],        │
│                  'status' => $data['status'] ?? 'active',                │
│                  'active_users_count' => $data['active_users_count'] ?? 0│
│              ]);                                                         │
│                                                                          │
│              // STEP 3: Eager load relationships                         │
│              $tool->load('category');                                    │
│                                                                          │
│              return $tool;                                               │
│          });                                                             │
│      }                                                                   │
│                                                                          │
│      /**                                                                │
│       * Get all tools with optional filters                             │
│       */                                                                │
│      public function getTools(array $filters = []): Collection          │
│      {                                                                  │
│          $query = Tool::with('category');  // Eager load relationship    │
│                                                                          │
│          // Apply filters using Eloquent query builder                   │
│          if (isset($filters['department'])) {                            │
│              $query->where('owner_department', $filters['department']);  │
│          }                                                               │
│                                                                          │
│          if (isset($filters['status'])) {                                │
│              $query->where('status', $filters['status']);                │
│          }                                                               │
│                                                                          │
│          if (isset($filters['category_id'])) {                           │
│              $query->where('category_id', $filters['category_id']);      │
│          }                                                               │
│                                                                          │
│          return $query->orderBy('created_at', 'desc')->get();            │
│      }                                                                   │
│                                                                          │
│      /**                                                                │
│       * Get tool by ID                                                  │
│       */                                                                │
│      public function getToolById(int $id): Tool                          │
│      {                                                                  │
│          $tool = Tool::with('category')->find($id);                      │
│                                                                          │
│          if (!$tool) {                                                   │
│              throw new \Exception("Tool not found", 404);                │
│          }                                                               │
│                                                                          │
│          return $tool;                                                   │
│      }                                                                   │
│  }                                                                       │
│                                                                          │
│  ROLE: Business logic, transactions, complex operations                 │
│  INPUT: Validated data + database context                               │
│  OUTPUT: Eloquent models or throw exceptions                            │
└────────────────────────────────┬─────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  LAYER 4: ELOQUENT ORM (Models & Relationships)                             │
│  📁 app/Models/Tool.php                                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│  <?php                                                                      │
│                                                                             │
│  namespace App\Models;                                                      │
│                                                                             │
│  use Illuminate\Database\Eloquent\Factories\HasFactory;                     │
│  use Illuminate\Database\Eloquent\Model;                                    │
│  use Illuminate\Database\Eloquent\Relations\BelongsTo;                      │
│                                                                             │
│  class Tool extends Model                                                   │
│  {                                                                          │
│      use HasFactory;                                                        │
│                                                                             │
│      // Table name (Laravel auto-detects 'tools' from class name)           │
│      protected $table = 'tools';                                            │
│                                                                             │
│      // Mass-assignable attributes (protection against mass-assignment)     │
│      protected $fillable = [                                                │
│          'name',                                                            │
│          'description',                                                     │
│          'vendor',                                                          │
│          'website_url',                                                     │
│          'monthly_cost',                                                    │
│          'category_id',                                                     │
│          'owner_department',                                                │
│          'status',                                                          │
│          'active_users_count'                                               │
│      ];                                                                     │
│                                                                             │
│      // Attributes hidden from JSON serialization                           │
│      protected $hidden = [];                                                │
│                                                                             │
│      // Cast attributes to specific types                                   │
│      protected $casts = [                                                   │
│          'monthly_cost' => 'decimal:2',                                     │
│          'active_users_count' => 'integer',                                 │
│          'created_at' => 'datetime',                                        │
│          'updated_at' => 'datetime'                                         │
│      ];                                                                     │
│                                                                             │
│      // Default attribute values                                            │
│      protected $attributes = [                                              │
│          'status' => 'active',                                              │
│          'active_users_count' => 0                                          │
│      ];                                                                     │
│                                                                             │
│      /**                                                                    │
│       * Relationship: Tool belongs to Category                              │
│       */                                                                    │
│      public function category(): BelongsTo                                  │
│      {                                                                      │
│          return $this->belongsTo(Category::class);                          │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Accessor: Computed property for total monthly cost                  │
│       */                                                                    │
│      public function getTotalMonthlyCostAttribute(): float                  │
│      {                                                                      │
│          return $this->monthly_cost * $this->active_users_count;            │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Mutator: Transform name to title case before saving                 │
│       */                                                                    │
│      public function setNameAttribute(string $value): void                  │
│      {                                                                      │
│          $this->attributes['name'] = ucwords(strtolower($value));           │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Scope: Query only active tools                                      │
│       */                                                                    │
│      public function scopeActive($query)                                    │
│      {                                                                      │
│          return $query->where('status', 'active');                          │
│      }                                                                      │
│                                                                             │
│      /**                                                                    │
│       * Scope: Query by department                                          │
│       */                                                                    │
│      public function scopeByDepartment($query, string $department)          │
│      {                                                                      │
│          return $query->where('owner_department', $department);             │
│      }                                                                      │
│  }                                                                          │
│                                                                             │
│  ROLE: ORM mapping, relationships, accessors/mutators                       │
│  INPUT: PHP arrays                                                          │
│  OUTPUT: SQL queries via Eloquent Query Builder                             │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                  DATABASE MIGRATION (Schema Definition)                     │
│  📁 database/migrations/xxxx_create_tools_table.php                         │
├─────────────────────────────────────────────────────────────────────────────┤
│  <?php                                                                      │
│                                                                             │
│  use Illuminate\Database\Migrations\Migration;                              │
│  use Illuminate\Database\Schema\Blueprint;                                  │
│  use Illuminate\Support\Facades\Schema;                                     │
│  use Illuminate\Support\Facades\DB;                                         │
│                                                                             │
│  return new class extends Migration                                         │
│  {                                                                          │
│      public function up(): void                                             │
│      {                                                                      │
│          // Create PostgreSQL ENUMs first                                   │
│          DB::statement("                                                    │
│              CREATE TYPE department_type AS ENUM (                          │
│                  'Engineering', 'Sales', 'Marketing',                       │
│                  'IT', 'HR', 'Finance', 'Operations'                        │
│              )                                                              │
│          ");                                                                │
│                                                                             │
│          DB::statement("                                                    │
│              CREATE TYPE tool_status_type AS ENUM (                         │
│                  'active', 'deprecated', 'trial'                            │
│              )                                                              │
│          ");                                                                │
│                                                                             │
│          // Create table with Laravel Schema Builder                        │
│          Schema::create('tools', function (Blueprint $table) {              │
│              $table->id();  // BIGSERIAL primary key                        │
│              $table->string('name', 100)->unique();                         │
│              $table->text('description')->nullable();                       │
│              $table->string('vendor', 100);                                 │
│              $table->string('website_url', 255)->nullable();                │
│              $table->decimal('monthly_cost', 10, 2)->default(0);            │
│              $table->integer('active_users_count')->default(0);             │
│                                                                             │
│              // Foreign key                                                 │
│              $table->foreignId('category_id')                               │
│                  ->constrained('categories')                                │
│                  ->onDelete('cascade');                                     │
│                                                                             │
│              // PostgreSQL ENUM columns (using raw SQL)                     │
│              $table->addColumn('department_type', 'owner_department');      │
│              $table->addColumn('tool_status_type', 'status')                │
│                  ->default('active');                                       │
│                                                                             │
│              $table->timestamps();  // created_at, updated_at               │
│          });                                                                │
│      }                                                                      │
│                                                                             │
│      public function down(): void                                           │
│      {                                                                      │
│          Schema::dropIfExists('tools');                                     │
│          DB::statement("DROP TYPE IF EXISTS department_type");              │
│          DB::statement("DROP TYPE IF EXISTS tool_status_type");             │
│      }                                                                      │
│  };                                                                         │
│                                                                             │
│  ROLE: Database schema version control                                      │
│  RUN: php artisan migrate                                                   │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         DATABASE (PostgreSQL 15)                            │
│  📊 Table: tools                                                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  SQL Generated by Eloquent ORM:                                             │
│                                                                             │
│  INSERT INTO tools (                                                        │
│    name, description, vendor, website_url, monthly_cost,                    │
│    category_id, owner_department, status,                                   │
│    active_users_count, created_at, updated_at                               │
│  ) VALUES (                                                                 │
│    ?, ?, ?, ?, ?, ?, ?::department_type, ?::tool_status_type,               │
│    ?, NOW(), NOW()                                                          │
│  ) RETURNING *;                                                             │
│                                                                             │
│  Bindings:                                                                  │
│    ['Slack', 'Team messaging', 'Slack Technologies',                        │
│     'https://slack.com', 8.00, 1, 'Engineering', 'active', 0]               │
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
│  LAYER 5: API RESOURCE (Response Transformation)                            │
│  📁 app/Http/Resources/ToolResource.php                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│  <?php                                                                      │
│                                                                             │
│  namespace App\Http\Resources;                                              │
│                                                                             │
│  use Illuminate\Http\Request;                                               │
│  use Illuminate\Http\Resources\Json\JsonResource;                           │
│                                                                             │
│  class ToolResource extends JsonResource                                    │
│  {                                                                          │
│      /**                                                                    │
│       * Transform the resource into an array                                │
│       *                                                                     │
│       * @param Request $request                                             │
│       * @return array                                                       │
│       */                                                                    │
│      public function toArray(Request $request): array                       │
│      {                                                                      │
│          return [                                                           │
│              'id' => $this->id,                                             │
│              'name' => $this->name,                                         │
│              'description' => $this->description,                           │
│              'vendor' => $this->vendor,                                     │
│              'website_url' => $this->website_url,                           │
│              'category' => [                                                │
│                  'id' => $this->category->id,                               │
│                  'name' => $this->category->name                            │
│              ],                                                             │
│              'monthly_cost' => (float) $this->monthly_cost,                 │
│              'total_monthly_cost' => $this->total_monthly_cost,             │
│              'owner_department' => $this->owner_department,                 │
│              'status' => $this->status,                                     │
│              'active_users_count' => $this->active_users_count,             │
│              'created_at' => $this->created_at->toIso8601String(),          │
│              'updated_at' => $this->updated_at->toIso8601String(),          │
│                                                                             │
│              // Conditional fields                                          │
│              'links' => [                                                   │
│                  'self' => route('tools.show', $this->id),                  │
│                  'category' => route('categories.show', $this->category_id) │
│              ]                                                              │
│          ];                                                                 │
│      }                                                                      │
│  }                                                                          │
│                                                                             │
│  ROLE: Response transformation, data formatting                             │
│  INPUT: Eloquent model                                                      │
│  OUTPUT: JSON array structure                                               │
└────────────────────────────────┬────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                      HTTP RESPONSE TO CLIENT                                │
│  Status: 201 Created                                                        │
│  Location: /api/tools/21                                                    │
│  Content-Type: application/json                                             │
│  Body:                                                                      │
│  {                                                                          │
│    "data": {                                                                │
│      "id": 21,                                                              │
│      "name": "Slack",                                                       │
│      "description": "Team messaging platform",                              │
│      "vendor": "Slack Technologies",                                        │
│      "website_url": "https://slack.com",                                    │
│      "category": {                                                          │
│        "id": 1,                                                             │
│        "name": "Communication"                                              │
│      },                                                                     │
│      "monthly_cost": 8.00,                                                  │
│      "total_monthly_cost": 0.00,                                            │
│      "owner_department": "Engineering",                                     │
│      "status": "active",                                                    │
│      "active_users_count": 0,                                               │
│      "created_at": "2025-11-28T16:30:00Z",                                  │
│      "updated_at": "2025-11-28T16:30:00Z",                                  │
│      "links": {                                                             │
│        "self": "/api/tools/21",                                             │
│        "category": "/api/categories/1"                                      │
│      }                                                                      │
│    }                                                                        │
│  }                                                                          │
└─────────────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────────┐
│  ERROR HANDLING (Exception Handler)                                        │
│  📁 app/Exceptions/Handler.php                                             │
├────────────────────────────────────────────────────────────────────────────┤
│  <?php                                                                     │
│                                                                            │
│  namespace App\Exceptions;                                                 │
│                                                                            │
│  use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;         │
│  use Illuminate\Validation\ValidationException;                            │
│  use Illuminate\Database\Eloquent\ModelNotFoundException;                  │
│  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;         │
│                                                                            │
│  class Handler extends ExceptionHandler                                    │
│  {                                                                         │
│      public function register(): void                                      │
│      {                                                                     │
│          // Handle validation errors                                       │
│          $this->renderable(function (ValidationException $e, $request) {   │
│              if ($request->expectsJson()) {                                │
│                  return response()->json([                                 │
│                      'message' => 'Validation failed',                     │
│                      'errors' => $e->errors()                              │
│                  ], 422);                                                  │
│              }                                                             │
│          });                                                               │
│                                                                            │
│          // Handle model not found                                         │
│          $this->renderable(function (ModelNotFoundException $e, $request) {│
│              if ($request->expectsJson()) {                                │
│                  return response()->json([                                 │
│                      'message' => 'Resource not found'                     │
│                  ], 404);                                                  │
│              }                                                             │
│          });                                                               │
│      }                                                                     │
│  }                                                                         │
│                                                                            │
│  ROLE: Centralized exception handling                                      │
│  CATCHES: ValidationException, ModelNotFoundException, etc.                │
│  OUTPUT: Consistent JSON error responses                                   │
└────────────────────────────────────────────────────────────────────────────┘
```

## 🎯 Key PHP Laravel Concepts

### **1. Eloquent ORM (Active Record Pattern)**
```php
// Eloquent = Active Record (model = database row)
$tool = new Tool();
$tool->name = 'Slack';
$tool->save();  // Automatically INSERT

// Or mass assignment
$tool = Tool::create([
    'name' => 'Slack',
    'vendor' => 'Slack Technologies'
]);

// Query builder (fluent interface)
$tools = Tool::where('status', 'active')
    ->where('monthly_cost', '>', 100)
    ->orderBy('created_at', 'desc')
    ->get();

// Relationships (eager loading)
$tool = Tool::with('category')->find(1);
echo $tool->category->name;  // No N+1 queries!
```

### **2. FormRequest Validation (Automatic)**
```php
// Laravel automatically validates BEFORE controller method runs
public function store(CreateToolRequest $request)
{
    // If we reach here, validation passed!
    $data = $request->validated();  // Only validated data
}

// FormRequest rules
public function rules(): array
{
    return [
        'name' => 'required|string|min:2|max:100|unique:tools',
        'email' => 'required|email',
        'price' => 'required|numeric|min:0'
    ];
}
```

### **3. API Resources (Response Transformation)**
```php
// Transform Eloquent models to JSON
class ToolResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => new CategoryResource($this->category),
            'links' => [
                'self' => route('tools.show', $this->id)
            ]
        ];
    }
}

// Usage in controller
return new ToolResource($tool);  // Single resource
return ToolResource::collection($tools);  // Collection
```

### **4. Database Migrations (Schema as Code)**
```php
// Version control for database schema
Schema::create('tools', function (Blueprint $table) {
    $table->id();  // BIGSERIAL
    $table->string('name', 100)->unique();
    $table->decimal('monthly_cost', 10, 2);
    $table->foreignId('category_id')->constrained();
    $table->timestamps();  // created_at, updated_at
});

// Run migrations
php artisan migrate

// Rollback
php artisan migrate:rollback
```

### **5. Service Container (Dependency Injection)**
```php
// Bind in AppServiceProvider
$this->app->bind(ToolService::class, function ($app) {
    return new ToolService($app->make(ToolRepository::class));
});

// Auto-inject anywhere
public function __construct(ToolService $toolService)
{
    $this->toolService = $toolService;  // Auto-resolved!
}
```

### **6. Accessors & Mutators (Computed Properties)**
```php
class Tool extends Model
{
    // Accessor (getter) - computed property
    public function getTotalCostAttribute(): float
    {
        return $this->monthly_cost * $this->active_users_count;
    }
    
    // Usage: $tool->total_cost (auto-computed!)
    
    // Mutator (setter) - transform before saving
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = ucwords($value);
    }
}
```

### **7. Query Scopes (Reusable Queries)**
```php
class Tool extends Model
{
    // Local scope
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeByDepartment($query, $dept)
    {
        return $query->where('owner_department', $dept);
    }
}

// Usage (chainable!)
$tools = Tool::active()->byDepartment('Engineering')->get();
```

## 📝 Complete CRUD Operations Flow

### **CREATE (POST /api/tools)**
```
Client → Laravel Route (routes/api.php)
      → Controller (store method)
      → FormRequest (auto-validation)
      → Service layer (business logic)
      → Eloquent ORM (Model::create())
      → PostgreSQL (INSERT)
      → API Resource (transform)
      → Return JSON (201 Created)
```

### **READ (GET /api/tools/{id})**
```
Client → Route → Controller (show method)
      → Service layer
      → Eloquent (Tool::with('category')->find($id))
      → PostgreSQL (SELECT)
      → API Resource
      → Return JSON (200 OK)
```

### **UPDATE (PUT /api/tools/{id})**
```
Client → Route → Controller (update method)
      → FormRequest validation
      → Service layer
      → Eloquent (Tool::find($id)->update($data))
      → PostgreSQL (UPDATE)
      → API Resource
      → Return JSON (200 OK)
```

### **DELETE (DELETE /api/tools/{id})**
```
Client → Route → Controller (destroy method)
      → Service layer
      → Eloquent (Tool::destroy($id))
      → PostgreSQL (DELETE)
      → Return 204 No Content
```

### **LIST with FILTERS (GET /api/tools?department=Engineering)**
```
Client → Route → Controller (index method)
      → Service layer (builds query with filters)
      → Eloquent (Tool::where(...)->get())
      → PostgreSQL (SELECT WHERE)
      → API Resource collection
      → Return JSON array (200 OK)
```

## 🔥 PHP Laravel Advantages

✅ **Eloquent ORM** - Most elegant ORM (Active Record pattern)  
✅ **Convention over Configuration** - Minimal boilerplate  
✅ **Artisan CLI** - Code generation (controllers, models, migrations)  
✅ **FormRequest Validation** - Automatic validation before controller  
✅ **API Resources** - Clean response transformation  
✅ **Database Migrations** - Schema version control built-in  
✅ **Blade Templating** - Powerful template engine (if needed)  

## 🆚 PHP Laravel vs Other Stacks

| Feature | PHP Laravel | Python FastAPI | Ruby Rails |
|---------|-------------|----------------|------------|
| **ORM Pattern** | ⭐⭐⭐⭐⭐ Active Record | ⭐⭐⭐⭐ SQLAlchemy | ⭐⭐⭐⭐⭐ Active Record |
| **Performance** | ⭐⭐⭐ Fast (PHP 8+) | ⭐⭐⭐⭐⭐ Very fast | ⭐⭐⭐ Moderate |
| **Learning Curve** | ⭐⭐⭐⭐ Easy | ⭐⭐⭐ Easy | ⭐⭐⭐⭐ Moderate |
| **Auto-validation** | ✅ FormRequest | ✅ Pydantic | ✅ Strong Parameters |
| **Type Safety** | ❌ Dynamic (PHP 8+ types) | ✅ Type hints | ❌ Dynamic |
| **Async Support** | ⚠️ Limited (PHP 8.1+) | ✅ Native async/await | ⚠️ Limited |
| **Migrations** | ✅ Built-in | ⚠️ Alembic (external) | ✅ Built-in |
| **CLI Tool** | ✅ Artisan | ❌ No built-in | ✅ Rails CLI |
| **Ecosystem** | ⭐⭐⭐⭐⭐ Huge | ⭐⭐⭐⭐ Growing | ⭐⭐⭐⭐ Mature |

## 💡 Why PHP + Laravel?

1. **Eloquent Elegance** - Most expressive ORM syntax
2. **Convention over Configuration** - Laravel does the plumbing
3. **Rapid Development** - Artisan generates boilerplate code
4. **Mature Ecosystem** - Packages for everything (Laravel Nova, Sanctum, Passport)
5. **Easy Deployment** - Shared hosting support (unlike Node.js/Python)
6. **PHP 8+** - Modern features (JIT compiler, union types, attributes)

## 🏗️ Laravel Request Lifecycle

```
HTTP Request
    ↓
Public/index.php (entry point)
    ↓
Bootstrap Laravel
    ↓
Load Service Providers
    ↓
Route Matching (routes/api.php)
    ↓
Middleware Stack (auth, throttle, etc.)
    ↓
FormRequest Validation (auto!)
    ↓
Controller Method
    ↓
Service Layer (business logic)
    ↓
Eloquent ORM (database)
    ↓
API Resource (transform)
    ↓
JSON Response
```

## 🆕 Modern PHP Features (PHP 8+)

### **Attributes (Like Decorators)**
```php
use Illuminate\Routing\Controller;

#[Route('/api/tools')]
class ToolController extends Controller
{
    #[Get('/{id}')]
    public function show(int $id) { }
}
```

### **Named Arguments**
```php
Tool::create(
    name: 'Slack',
    vendor: 'Slack Technologies',
    monthly_cost: 8.00
);
```

### **Match Expression**
```php
$message = match ($tool->status) {
    'active' => 'Tool is active',
    'deprecated' => 'Tool is deprecated',
    'trial' => 'Tool is in trial',
    default => 'Unknown status'
};
```

### **Nullsafe Operator**
```php
// Old way
$categoryName = $tool->category ? $tool->category->name : null;

// PHP 8+ way
$categoryName = $tool->category?->name;
```

## ⚠️ PHP Laravel Trade-offs

- **Type Safety** - PHP is dynamically typed (but improving with PHP 8+)
- **Async Support** - Limited async/await (PHP 8.1+ fibers)
- **Performance** - Slower than Go/Rust (but PHP 8 JIT helps)
- **Thread Safety** - PHP-FPM process model (not multi-threaded)
- **But** → Trade for rapid development and elegant syntax! 🚀

---

**This PHP Laravel architecture ensures:**
✅ Eloquent ORM with Active Record pattern  
✅ Automatic validation via FormRequest  
✅ API Resources for clean responses  
✅ Database migrations for schema version control  
✅ Service layer for business logic  
✅ PostgreSQL ENUM support via raw SQL