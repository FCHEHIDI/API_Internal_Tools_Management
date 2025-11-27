<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get cost breakdown by department
     */
    public function departmentCosts(Request $request): JsonResponse
    {
        $sortBy = $request->get('sort_by', 'total_cost');
        $order = $request->get('order', 'desc');

        $departmentData = Tool::select('owner_department')
            ->selectRaw('SUM(monthly_cost) as total_cost')
            ->selectRaw('COUNT(*) as tools_count')
            ->selectRaw('SUM(active_users_count) as total_users')
            ->selectRaw('AVG(monthly_cost) as average_cost_per_tool')
            ->where('status', 'active')
            ->groupBy('owner_department')
            ->orderBy($sortBy, $order)
            ->get();

        $totalCompanyCost = $departmentData->sum('total_cost');

        $data = $departmentData->map(function($dept) use ($totalCompanyCost) {
            $costPercentage = $totalCompanyCost > 0 
                ? round(($dept->total_cost / $totalCompanyCost) * 100, 1)
                : 0;

            return [
                'department' => $dept->owner_department,
                'total_cost' => (float) $dept->total_cost,
                'tools_count' => $dept->tools_count,
                'total_users' => $dept->total_users,
                'average_cost_per_tool' => round($dept->average_cost_per_tool, 2),
                'cost_percentage' => $costPercentage
            ];
        });

        $mostExpensiveDept = $data->sortByDesc('total_cost')->first();

        return response()->json([
            'data' => $data->values(),
            'summary' => [
                'total_company_cost' => (float) $totalCompanyCost,
                'departments_count' => $data->count(),
                'most_expensive_department' => $mostExpensiveDept ? $mostExpensiveDept['department'] : null
            ]
        ]);
    }

    /**
     * Get top expensive tools with efficiency analysis
     */
    public function expensiveTools(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $minCost = $request->get('min_cost');

        // Calculate company average cost per user (excluding tools with 0 users)
        $avgCostPerUserCompany = Tool::where('status', 'active')
            ->where('active_users_count', '>', 0)
            ->selectRaw('SUM(monthly_cost) / SUM(active_users_count) as avg')
            ->value('avg') ?? 0;

        $query = Tool::select('id', 'name', 'monthly_cost', 'active_users_count', 'owner_department', 'vendor')
            ->where('status', 'active')
            ->orderBy('monthly_cost', 'desc')
            ->limit($limit);

        if ($minCost) {
            $query->where('monthly_cost', '>=', $minCost);
        }

        $tools = $query->get();
        $totalToolsAnalyzed = Tool::where('status', 'active')->count();

        $data = $tools->map(function($tool) use ($avgCostPerUserCompany) {
            $costPerUser = $tool->active_users_count > 0 
                ? $tool->monthly_cost / $tool->active_users_count 
                : $tool->monthly_cost;

            // Efficiency rating based on cost per user vs company average
            $efficiencyRating = 'average';
            if ($avgCostPerUserCompany > 0) {
                $ratio = $costPerUser / $avgCostPerUserCompany;
                if ($ratio < 0.5) {
                    $efficiencyRating = 'excellent';
                } elseif ($ratio < 0.8) {
                    $efficiencyRating = 'good';
                } elseif ($ratio > 1.2) {
                    $efficiencyRating = 'low';
                }
            }

            return [
                'id' => $tool->id,
                'name' => $tool->name,
                'monthly_cost' => (float) $tool->monthly_cost,
                'active_users_count' => $tool->active_users_count,
                'cost_per_user' => round($costPerUser, 2),
                'department' => $tool->owner_department,
                'vendor' => $tool->vendor,
                'efficiency_rating' => $efficiencyRating
            ];
        });

        // Calculate potential savings (sum of tools with 'low' efficiency)
        $potentialSavings = $data->where('efficiency_rating', 'low')->sum('monthly_cost');

        return response()->json([
            'data' => $data->values(),
            'analysis' => [
                'total_tools_analyzed' => $totalToolsAnalyzed,
                'avg_cost_per_user_company' => round($avgCostPerUserCompany, 2),
                'potential_savings_identified' => (float) $potentialSavings
            ]
        ]);
    }

    /**
     * Get tools distribution by category
     */
    public function toolsByCategory(): JsonResponse
    {
        $categoryData = Tool::join('categories', 'tools.category_id', '=', 'categories.id')
            ->select('categories.name as category_name')
            ->selectRaw('COUNT(tools.id) as tools_count')
            ->selectRaw('SUM(tools.monthly_cost) as total_cost')
            ->selectRaw('SUM(tools.active_users_count) as total_users')
            ->where('tools.status', 'active')
            ->groupBy('categories.name', 'categories.id')
            ->orderBy('total_cost', 'desc')
            ->get();

        $totalBudget = $categoryData->sum('total_cost');

        $data = $categoryData->map(function($cat) use ($totalBudget) {
            $percentageOfBudget = $totalBudget > 0 
                ? round(($cat->total_cost / $totalBudget) * 100, 1)
                : 0;

            $avgCostPerUser = $cat->total_users > 0 
                ? $cat->total_cost / $cat->total_users 
                : 0;

            return [
                'category_name' => $cat->category_name,
                'tools_count' => $cat->tools_count,
                'total_cost' => (float) $cat->total_cost,
                'total_users' => $cat->total_users,
                'percentage_of_budget' => $percentageOfBudget,
                'average_cost_per_user' => round($avgCostPerUser, 2)
            ];
        });

        // Find most expensive and most efficient categories
        $mostExpensive = $data->sortByDesc('total_cost')->first();
        $mostEfficient = $data->where('total_users', '>', 0)
            ->sortBy('average_cost_per_user')
            ->first();

        return response()->json([
            'data' => $data->values(),
            'insights' => [
                'most_expensive_category' => $mostExpensive ? $mostExpensive['category_name'] : null,
                'most_efficient_category' => $mostEfficient ? $mostEfficient['category_name'] : null
            ]
        ]);
    }

    /**
     * Get low usage tools with potential savings
     */
    public function lowUsageTools(Request $request): JsonResponse
    {
        $maxUsers = $request->get('max_users', 5);

        $tools = Tool::select('id', 'name', 'monthly_cost', 'active_users_count', 'owner_department', 'vendor')
            ->where('status', 'active')
            ->where('active_users_count', '<=', $maxUsers)
            ->orderBy('monthly_cost', 'desc')
            ->get();

        $data = $tools->map(function($tool) {
            $costPerUser = $tool->active_users_count > 0 
                ? $tool->monthly_cost / $tool->active_users_count 
                : $tool->monthly_cost;

            // Warning level based on cost per user
            $warningLevel = 'low';
            $potentialAction = 'Monitor usage trends';

            if ($tool->active_users_count === 0) {
                $warningLevel = 'high';
                $potentialAction = 'Consider canceling or downgrading';
            } elseif ($costPerUser > 50) {
                $warningLevel = 'high';
                $potentialAction = 'Consider canceling or downgrading';
            } elseif ($costPerUser >= 20) {
                $warningLevel = 'medium';
                $potentialAction = 'Review usage and consider optimization';
            }

            return [
                'id' => $tool->id,
                'name' => $tool->name,
                'monthly_cost' => (float) $tool->monthly_cost,
                'active_users_count' => $tool->active_users_count,
                'cost_per_user' => round($costPerUser, 2),
                'department' => $tool->owner_department,
                'vendor' => $tool->vendor,
                'warning_level' => $warningLevel,
                'potential_action' => $potentialAction
            ];
        });

        // Calculate potential savings
        $highWarnings = $data->where('warning_level', 'high');
        $mediumWarnings = $data->where('warning_level', 'medium');
        
        $potentialMonthlySavings = $highWarnings->sum('monthly_cost') + $mediumWarnings->sum('monthly_cost');
        $potentialAnnualSavings = $potentialMonthlySavings * 12;

        return response()->json([
            'data' => $data->values(),
            'savings_analysis' => [
                'total_underutilized_tools' => $data->count(),
                'potential_monthly_savings' => (float) $potentialMonthlySavings,
                'potential_annual_savings' => (float) $potentialAnnualSavings
            ]
        ]);
    }

    /**
     * Get vendor summary and analysis
     */
    public function vendorSummary(): JsonResponse
    {
        $vendorData = Tool::select('vendor')
            ->selectRaw('COUNT(*) as tools_count')
            ->selectRaw('SUM(monthly_cost) as total_monthly_cost')
            ->selectRaw('SUM(active_users_count) as total_users')
            ->where('status', 'active')
            ->groupBy('vendor')
            ->orderBy('total_monthly_cost', 'desc')
            ->get();

        $data = $vendorData->map(function($vendor) {
            // Get unique departments for this vendor
            $departments = Tool::where('vendor', $vendor->vendor)
                ->where('status', 'active')
                ->distinct()
                ->pluck('owner_department')
                ->sort()
                ->values()
                ->join(',');

            $avgCostPerUser = $vendor->total_users > 0 
                ? $vendor->total_monthly_cost / $vendor->total_users 
                : 0;

            // Vendor efficiency rating based on average cost per user
            $vendorEfficiency = 'average';
            if ($avgCostPerUser < 5) {
                $vendorEfficiency = 'excellent';
            } elseif ($avgCostPerUser < 15) {
                $vendorEfficiency = 'good';
            } elseif ($avgCostPerUser > 25) {
                $vendorEfficiency = 'poor';
            }

            return [
                'vendor' => $vendor->vendor,
                'tools_count' => $vendor->tools_count,
                'total_monthly_cost' => (float) $vendor->total_monthly_cost,
                'total_users' => $vendor->total_users,
                'departments' => $departments,
                'average_cost_per_user' => round($avgCostPerUser, 2),
                'vendor_efficiency' => $vendorEfficiency
            ];
        });

        // Find insights
        $mostExpensiveVendor = $data->sortByDesc('total_monthly_cost')->first();
        $mostEfficientVendor = $data->where('total_users', '>', 0)
            ->sortBy('average_cost_per_user')
            ->first();
        $singleToolVendors = $data->where('tools_count', 1)->count();

        return response()->json([
            'data' => $data->values(),
            'vendor_insights' => [
                'most_expensive_vendor' => $mostExpensiveVendor ? $mostExpensiveVendor['vendor'] : null,
                'most_efficient_vendor' => $mostEfficientVendor ? $mostEfficientVendor['vendor'] : null,
                'single_tool_vendors' => $singleToolVendors
            ]
        ]);
    }
}

