<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreToolRequest;
use App\Http\Requests\UpdateToolRequest;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ToolController extends Controller
{
    /**
     * Display a listing of tools with optional filters.
     * 
     * Filters: department, status, category, min_cost, max_cost, search
     * Sorting: sort_by (name|monthly_cost|created_at), order (asc|desc)
     * Pagination: page, limit
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tool::with('category');

        // Apply filters
        if ($request->has('department')) {
            $query->department($request->department);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->category($request->category);
        }

        if ($request->has('min_cost') && $request->has('max_cost')) {
            $query->costBetween($request->min_cost, $request->max_cost);
        } elseif ($request->has('min_cost')) {
            $query->where('monthly_cost', '>=', $request->min_cost);
        } elseif ($request->has('max_cost')) {
            $query->where('monthly_cost', '<=', $request->max_cost);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%")
                  ->orWhere('vendor', 'ILIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sortBy, $order);

        // Get total before pagination
        $total = Tool::count();
        $filtered = $query->count();

        // Pagination
        $limit = $request->get('limit', 20);
        $tools = $query->paginate($limit);

        return response()->json([
            'data' => $tools->map(function($tool) {
                return [
                    'id' => $tool->id,
                    'name' => $tool->name,
                    'description' => $tool->description,
                    'vendor' => $tool->vendor,
                    'category' => $tool->category->name,
                    'monthly_cost' => (float) $tool->monthly_cost,
                    'owner_department' => $tool->owner_department,
                    'status' => $tool->status,
                    'website_url' => $tool->website_url,
                    'active_users_count' => $tool->active_users_count,
                    'created_at' => $tool->created_at->toISOString(),
                ];
            }),
            'total' => $total,
            'filtered' => $filtered,
            'current_page' => $tools->currentPage(),
            'last_page' => $tools->lastPage(),
            'per_page' => $tools->perPage(),
            'filters_applied' => $request->only(['department', 'status', 'category', 'min_cost', 'max_cost', 'search'])
        ]);
    }

    /**
     * Store a newly created tool in storage.
     */
    public function store(StoreToolRequest $request): JsonResponse
    {
        $tool = Tool::create($request->validated());
        $tool->load('category');

        return response()->json([
            'id' => $tool->id,
            'name' => $tool->name,
            'description' => $tool->description,
            'vendor' => $tool->vendor,
            'website_url' => $tool->website_url,
            'category' => $tool->category->name,
            'monthly_cost' => (float) $tool->monthly_cost,
            'owner_department' => $tool->owner_department,
            'status' => $tool->status,
            'active_users_count' => $tool->active_users_count,
            'created_at' => $tool->created_at->toISOString(),
            'updated_at' => $tool->updated_at->toISOString(),
        ], 201);
    }

    /**
     * Display the specified tool with full details.
     */
    public function show(string $id): JsonResponse
    {
        $tool = Tool::with('category')->find($id);

        if (!$tool) {
            return response()->json([
                'error' => 'Tool not found',
                'message' => "Tool with ID {$id} does not exist"
            ], 404);
        }

        $totalMonthlyCost = $tool->monthly_cost * $tool->active_users_count;

        return response()->json([
            'id' => $tool->id,
            'name' => $tool->name,
            'description' => $tool->description,
            'vendor' => $tool->vendor,
            'website_url' => $tool->website_url,
            'category' => $tool->category->name,
            'monthly_cost' => (float) $tool->monthly_cost,
            'owner_department' => $tool->owner_department,
            'status' => $tool->status,
            'active_users_count' => $tool->active_users_count,
            'total_monthly_cost' => (float) $totalMonthlyCost,
            'created_at' => $tool->created_at->toISOString(),
            'updated_at' => $tool->updated_at->toISOString(),
        ]);
    }

    /**
     * Update the specified tool in storage.
     */
    public function update(UpdateToolRequest $request, string $id): JsonResponse
    {
        $tool = Tool::find($id);

        if (!$tool) {
            return response()->json([
                'error' => 'Tool not found',
                'message' => "Tool with ID {$id} does not exist"
            ], 404);
        }

        $tool->update($request->validated());
        $tool->load('category');

        return response()->json([
            'id' => $tool->id,
            'name' => $tool->name,
            'description' => $tool->description,
            'vendor' => $tool->vendor,
            'website_url' => $tool->website_url,
            'category' => $tool->category->name,
            'monthly_cost' => (float) $tool->monthly_cost,
            'owner_department' => $tool->owner_department,
            'status' => $tool->status,
            'active_users_count' => $tool->active_users_count,
            'created_at' => $tool->created_at->toISOString(),
            'updated_at' => $tool->updated_at->toISOString(),
        ]);
    }

    /**
     * Remove the specified tool from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $tool = Tool::find($id);

        if (!$tool) {
            return response()->json([
                'error' => 'Tool not found',
                'message' => "Tool with ID {$id} does not exist"
            ], 404);
        }

        $tool->delete();

        return response()->json([
            'message' => 'Tool deleted successfully'
        ], 200);
    }
}
