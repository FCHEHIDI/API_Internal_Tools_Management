package com.techcorp.internaltools.service;

import com.techcorp.internaltools.dto.analytics.*;
import com.techcorp.internaltools.model.Department;
import com.techcorp.internaltools.model.Tool;
import com.techcorp.internaltools.model.ToolStatus;
import com.techcorp.internaltools.repository.ToolRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.util.*;
import java.util.stream.Collectors;

/**
 * AnalyticsService - Business intelligence and cost optimization analytics
 * 
 * Implements 5 analytics endpoints for financial decision-making:
 * 1. Department Costs - Budget allocation by department
 * 2. Expensive Tools - High-cost tools prioritization for negotiation
 * 3. Tools by Category - Technology stack analysis
 * 4. Low Usage Tools - Underutilization identification for savings
 * 5. Vendor Summary - Vendor consolidation opportunities
 * 
 * Key Business Rules:
 * - All analytics filter to status='active' tools only
 * - Cost calculations: monthly_cost × active_users_count
 * - Percentages rounded to 1 decimal, must sum to 100% (±0.1% tolerance)
 * - Efficiency ratings based on cost-per-user ratios
 * - Decimal precision: 2 decimals for costs, 1 for percentages
 */
@Service
@RequiredArgsConstructor
public class AnalyticsService {

    private final ToolRepository toolRepository;

    /**
     * Get department costs analysis (Analytics Endpoint #1)
     * 
     * Business Use Case: Jennifer (CFO) needs to see budget allocation by department
     * to identify optimization opportunities and present to board.
     * 
     * Calculations:
     * - totalCost = SUM(monthly_cost × active_users_count) per department
     * - costPercentage = (dept_cost / total_company_cost) × 100
     * - averageCostPerTool = totalCost / toolsCount
     * 
     * Sorting:
     * - sort_by=total_cost, order=desc (default) - Most expensive first
     * - sort_by=department - Alphabetical order
     * 
     * @param sortBy Field to sort by (total_cost or department)
     * @param order Sort order (asc or desc)
     * @return DepartmentCostsResponse with per-department metrics and summary
     */
    @Transactional(readOnly = true)
    public DepartmentCostsResponse getDepartmentCosts(String sortBy, String order) {
        // STEP 1: Load all active tools (business rule: only active status)
        List<Tool> activeTools = toolRepository.findByStatus(ToolStatus.active);
        
        // STEP 2: Group tools by department for aggregation
        Map<Department, List<Tool>> toolsByDept = activeTools.stream()
            .collect(Collectors.groupingBy(Tool::getOwnerDepartment));
        
        BigDecimal totalCompanyCost = activeTools.stream()
            .map(t -> t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())))
            .reduce(BigDecimal.ZERO, BigDecimal::add);
        
        List<DepartmentCostDto> departmentCosts = new ArrayList<>();
        
        for (Map.Entry<Department, List<Tool>> entry : toolsByDept.entrySet()) {
            Department dept = entry.getKey();
            List<Tool> tools = entry.getValue();
            
            BigDecimal deptTotalCost = tools.stream()
                .map(t -> t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())))
                .reduce(BigDecimal.ZERO, BigDecimal::add);
            
            long toolsCount = tools.size();
            long totalUsers = tools.stream()
                .mapToLong(Tool::getActiveUsersCount)
                .sum();
            
            BigDecimal avgCostPerTool = toolsCount > 0 
                ? deptTotalCost.divide(BigDecimal.valueOf(toolsCount), 2, RoundingMode.HALF_UP)
                : BigDecimal.ZERO;
            
            BigDecimal costPercentage = totalCompanyCost.compareTo(BigDecimal.ZERO) > 0
                ? deptTotalCost.divide(totalCompanyCost, 3, RoundingMode.HALF_UP)
                    .multiply(BigDecimal.valueOf(100))
                    .setScale(1, RoundingMode.HALF_UP)
                : BigDecimal.ZERO;
            
            departmentCosts.add(DepartmentCostDto.builder()
                .department(dept)
                .totalCost(deptTotalCost.setScale(2, RoundingMode.HALF_UP))
                .toolsCount(toolsCount)
                .totalUsers(totalUsers)
                .averageCostPerTool(avgCostPerTool)
                .costPercentage(costPercentage)
                .build());
        }
        
        // Sort results
        if ("total_cost".equals(sortBy)) {
            departmentCosts.sort((a, b) -> "desc".equals(order) 
                ? b.getTotalCost().compareTo(a.getTotalCost())
                : a.getTotalCost().compareTo(b.getTotalCost()));
        } else {
            departmentCosts.sort(Comparator.comparing(d -> d.getDepartment().name()));
        }
        
        String mostExpensive = departmentCosts.stream()
            .max(Comparator.comparing(DepartmentCostDto::getTotalCost))
            .map(d -> d.getDepartment().name())
            .orElse(null);
        
        DepartmentCostsResponse.Summary summary = new DepartmentCostsResponse.Summary(
            totalCompanyCost.setScale(2, RoundingMode.HALF_UP),
            toolsByDept.size(),
            mostExpensive
        );
        
        return new DepartmentCostsResponse(departmentCosts, summary);
    }

    /**
     * Get expensive tools analysis (Analytics Endpoint #2)
     * 
     * Business Use Case: Jennifer (CFO) wants to prioritize vendor negotiations
     * by identifying the most expensive tools first.
     * 
     * Efficiency Rating Logic (per instructions):
     * - "excellent": cost_per_user < 50% of company average
     * - "good": 50-80% of average
     * - "average": 80-120% of average
     * - "low": > 120% of average (candidates for optimization)
     * 
     * Potential Savings = SUM of tools with "low" efficiency rating
     * 
     * @param limit Maximum tools to return (1-100, default 10)
     * @param minCost Optional minimum cost filter
     * @return ExpensiveToolsResponse with ranked tools and savings analysis
     */
    @Transactional(readOnly = true)
    public ExpensiveToolsResponse getExpensiveTools(Integer limit, BigDecimal minCost) {
        List<Tool> activeTools = toolRepository.findByStatus(ToolStatus.active);
        
        // STEP 1: Calculate company-wide average cost per user (baseline for ratings)
        BigDecimal totalCost = BigDecimal.ZERO;
        long totalUsers = 0;
        for (Tool tool : activeTools) {
            if (tool.getActiveUsersCount() > 0) {
                totalCost = totalCost.add(tool.getMonthlyCost().multiply(BigDecimal.valueOf(tool.getActiveUsersCount())));
                totalUsers += tool.getActiveUsersCount();
            }
        }
        
        BigDecimal avgCostPerUserCompany = totalUsers > 0
            ? totalCost.divide(BigDecimal.valueOf(totalUsers), 2, RoundingMode.HALF_UP)
            : BigDecimal.ZERO;
        
        // Filter and map tools
        List<ExpensiveToolDto> expensiveTools = activeTools.stream()
            .filter(t -> minCost == null || t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())).compareTo(minCost) >= 0)
            .map(tool -> {
                BigDecimal totalToolCost = tool.getMonthlyCost().multiply(BigDecimal.valueOf(tool.getActiveUsersCount()));
                BigDecimal costPerUser = tool.getActiveUsersCount() > 0
                    ? totalToolCost.divide(BigDecimal.valueOf(tool.getActiveUsersCount()), 2, RoundingMode.HALF_UP)
                    : BigDecimal.ZERO;
                
                String efficiencyRating = calculateEfficiencyRating(costPerUser, avgCostPerUserCompany);
                
                return ExpensiveToolDto.builder()
                    .id(tool.getId())
                    .name(tool.getName())
                    .monthlyCost(tool.getMonthlyCost())
                    .activeUsersCount(tool.getActiveUsersCount())
                    .costPerUser(costPerUser)
                    .department(tool.getOwnerDepartment())
                    .vendor(tool.getVendor())
                    .efficiencyRating(efficiencyRating)
                    .build();
            })
            .sorted((a, b) -> b.getMonthlyCost().multiply(BigDecimal.valueOf(b.getActiveUsersCount()))
                .compareTo(a.getMonthlyCost().multiply(BigDecimal.valueOf(a.getActiveUsersCount()))))
            .limit(limit != null ? limit : 10)
            .collect(Collectors.toList());
        
        // Calculate potential savings (tools with "low" efficiency)
        BigDecimal potentialSavings = expensiveTools.stream()
            .filter(t -> "low".equals(t.getEfficiencyRating()))
            .map(t -> t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())))
            .reduce(BigDecimal.ZERO, BigDecimal::add);
        
        ExpensiveToolsResponse.Analysis analysis = new ExpensiveToolsResponse.Analysis(
            (int) activeTools.stream().filter(t -> t.getActiveUsersCount() > 0).count(),
            avgCostPerUserCompany,
            potentialSavings.setScale(2, RoundingMode.HALF_UP)
        );
        
        return new ExpensiveToolsResponse(expensiveTools, analysis);
    }

    /**
     * Get tools by category analysis
     */
    @Transactional(readOnly = true)
    public ToolsByCategoryResponse getToolsByCategory() {
        List<Tool> activeTools = toolRepository.findByStatus(ToolStatus.active);
        
        BigDecimal totalCompanyCost = activeTools.stream()
            .map(t -> t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())))
            .reduce(BigDecimal.ZERO, BigDecimal::add);
        
        Map<String, List<Tool>> toolsByCategory = activeTools.stream()
            .collect(Collectors.groupingBy(t -> t.getCategory().getName()));
        
        List<CategoryDto> categories = new ArrayList<>();
        
        for (Map.Entry<String, List<Tool>> entry : toolsByCategory.entrySet()) {
            String categoryName = entry.getKey();
            List<Tool> tools = entry.getValue();
            
            BigDecimal categoryTotalCost = tools.stream()
                .map(t -> t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())))
                .reduce(BigDecimal.ZERO, BigDecimal::add);
            
            long totalUsers = tools.stream()
                .mapToLong(Tool::getActiveUsersCount)
                .sum();
            
            BigDecimal avgCostPerUser = totalUsers > 0
                ? categoryTotalCost.divide(BigDecimal.valueOf(totalUsers), 2, RoundingMode.HALF_UP)
                : BigDecimal.ZERO;
            
            BigDecimal percentageOfBudget = totalCompanyCost.compareTo(BigDecimal.ZERO) > 0
                ? categoryTotalCost.divide(totalCompanyCost, 3, RoundingMode.HALF_UP)
                    .multiply(BigDecimal.valueOf(100))
                    .setScale(1, RoundingMode.HALF_UP)
                : BigDecimal.ZERO;
            
            categories.add(CategoryDto.builder()
                .categoryName(categoryName)
                .toolsCount((long) tools.size())
                .totalCost(categoryTotalCost.setScale(2, RoundingMode.HALF_UP))
                .totalUsers(totalUsers)
                .percentageOfBudget(percentageOfBudget)
                .averageCostPerUser(avgCostPerUser)
                .build());
        }
        
        categories.sort(Comparator.comparing(CategoryDto::getCategoryName));
        
        String mostExpensive = categories.stream()
            .max(Comparator.comparing(CategoryDto::getTotalCost))
            .map(CategoryDto::getCategoryName)
            .orElse(null);
        
        String mostEfficient = categories.stream()
            .filter(c -> c.getTotalUsers() > 0)
            .min(Comparator.comparing(CategoryDto::getAverageCostPerUser))
            .map(CategoryDto::getCategoryName)
            .orElse(null);
        
        ToolsByCategoryResponse.Insights insights = new ToolsByCategoryResponse.Insights(
            mostExpensive,
            mostEfficient
        );
        
        return new ToolsByCategoryResponse(categories, insights);
    }

    /**
     * Get low usage tools analysis
     */
    @Transactional(readOnly = true)
    public LowUsageToolsResponse getLowUsageTools(Integer maxUsers) {
        int threshold = maxUsers != null ? maxUsers : 5;
        
        List<Tool> lowUsageTools = toolRepository.findLowUsageTools(threshold);
        
        List<LowUsageToolDto> tools = lowUsageTools.stream()
            .map(tool -> {
                BigDecimal totalCost = tool.getMonthlyCost().multiply(BigDecimal.valueOf(tool.getActiveUsersCount()));
                BigDecimal costPerUser = tool.getActiveUsersCount() > 0
                    ? totalCost.divide(BigDecimal.valueOf(tool.getActiveUsersCount()), 2, RoundingMode.HALF_UP)
                    : tool.getMonthlyCost();
                
                String warningLevel = calculateWarningLevel(costPerUser, tool.getActiveUsersCount());
                String potentialAction = getPotentialAction(warningLevel);
                
                return LowUsageToolDto.builder()
                    .id(tool.getId())
                    .name(tool.getName())
                    .monthlyCost(tool.getMonthlyCost())
                    .activeUsersCount(tool.getActiveUsersCount())
                    .costPerUser(costPerUser)
                    .department(tool.getOwnerDepartment())
                    .vendor(tool.getVendor())
                    .warningLevel(warningLevel)
                    .potentialAction(potentialAction)
                    .build();
            })
            .sorted((a, b) -> b.getMonthlyCost().multiply(BigDecimal.valueOf(b.getActiveUsersCount()))
                .compareTo(a.getMonthlyCost().multiply(BigDecimal.valueOf(a.getActiveUsersCount()))))
            .collect(Collectors.toList());
        
        // Calculate potential savings (high + medium warning tools)
        BigDecimal potentialMonthlySavings = tools.stream()
            .filter(t -> "high".equals(t.getWarningLevel()) || "medium".equals(t.getWarningLevel()))
            .map(t -> t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())))
            .reduce(BigDecimal.ZERO, BigDecimal::add);
        
        BigDecimal potentialAnnualSavings = potentialMonthlySavings.multiply(BigDecimal.valueOf(12));
        
        LowUsageToolsResponse.SavingsAnalysis savingsAnalysis = new LowUsageToolsResponse.SavingsAnalysis(
            tools.size(),
            potentialMonthlySavings.setScale(2, RoundingMode.HALF_UP),
            potentialAnnualSavings.setScale(2, RoundingMode.HALF_UP)
        );
        
        return new LowUsageToolsResponse(tools, savingsAnalysis);
    }

    /**
     * Get vendor summary analysis
     */
    @Transactional(readOnly = true)
    public VendorSummaryResponse getVendorSummary() {
        List<Tool> activeTools = toolRepository.findByStatus(ToolStatus.active);
        
        Map<String, List<Tool>> toolsByVendor = activeTools.stream()
            .collect(Collectors.groupingBy(Tool::getVendor));
        
        List<VendorDto> vendors = new ArrayList<>();
        
        for (Map.Entry<String, List<Tool>> entry : toolsByVendor.entrySet()) {
            String vendor = entry.getKey();
            List<Tool> tools = entry.getValue();
            
            BigDecimal totalMonthlyCost = tools.stream()
                .map(t -> t.getMonthlyCost().multiply(BigDecimal.valueOf(t.getActiveUsersCount())))
                .reduce(BigDecimal.ZERO, BigDecimal::add);
            
            long totalUsers = tools.stream()
                .mapToLong(Tool::getActiveUsersCount)
                .sum();
            
            Set<String> departments = tools.stream()
                .map(t -> t.getOwnerDepartment().name())
                .collect(Collectors.toSet());
            String departmentsStr = departments.stream()
                .sorted()
                .collect(Collectors.joining(","));
            
            BigDecimal avgCostPerUser = totalUsers > 0
                ? totalMonthlyCost.divide(BigDecimal.valueOf(totalUsers), 2, RoundingMode.HALF_UP)
                : BigDecimal.ZERO;
            
            String vendorEfficiency = calculateVendorEfficiency(avgCostPerUser);
            
            vendors.add(VendorDto.builder()
                .vendor(vendor)
                .toolsCount((long) tools.size())
                .totalMonthlyCost(totalMonthlyCost.setScale(2, RoundingMode.HALF_UP))
                .totalUsers(totalUsers)
                .departments(departmentsStr)
                .averageCostPerUser(avgCostPerUser)
                .vendorEfficiency(vendorEfficiency)
                .build());
        }
        
        vendors.sort(Comparator.comparing(VendorDto::getVendor));
        
        String mostExpensive = vendors.stream()
            .max(Comparator.comparing(VendorDto::getTotalMonthlyCost))
            .map(VendorDto::getVendor)
            .orElse(null);
        
        String mostEfficient = vendors.stream()
            .filter(v -> v.getTotalUsers() > 0)
            .min(Comparator.comparing(VendorDto::getAverageCostPerUser))
            .map(VendorDto::getVendor)
            .orElse(null);
        
        int singleToolVendors = (int) vendors.stream()
            .filter(v -> v.getToolsCount() == 1)
            .count();
        
        VendorSummaryResponse.VendorInsights insights = new VendorSummaryResponse.VendorInsights(
            mostExpensive,
            mostEfficient,
            singleToolVendors
        );
        
        return new VendorSummaryResponse(vendors, insights);
    }

    // ========== HELPER METHODS ==========
    // Business logic calculations used across multiple analytics endpoints

    /**
     * Calculate efficiency rating based on cost-per-user vs company average
     * 
     * Per instructions specification:
     * - excellent: < 50% of average (very cost-effective)
     * - good: 50-80% of average
     * - average: 80-120% of average (acceptable)
     * - low: > 120% of average (optimization opportunity)
     * 
     * Used by: Expensive Tools endpoint
     */
    private String calculateEfficiencyRating(BigDecimal costPerUser, BigDecimal avgCostPerUser) {
        if (avgCostPerUser.compareTo(BigDecimal.ZERO) == 0) {
            return "average";  // Edge case: no baseline available
        }
        
        // Calculate ratio: tool_cost / company_average
        BigDecimal ratio = costPerUser.divide(avgCostPerUser, 2, RoundingMode.HALF_UP);
        
        // Apply business rules per instructions
        if (ratio.compareTo(new BigDecimal("0.5")) < 0) {
            return "excellent";  // Less than 50% of average
        } else if (ratio.compareTo(new BigDecimal("0.8")) < 0) {
            return "good";  // 50-80% of average
        } else if (ratio.compareTo(new BigDecimal("1.2")) <= 0) {
            return "average";  // 80-120% of average
        } else {
            return "low";  // Over 120% - needs optimization
        }
    }

    /**
     * Calculate warning level for low usage tools
     * 
     * Per instructions specification:
     * - high: cost_per_user > €50 OR active_users = 0
     * - medium: cost_per_user €20-50
     * - low: cost_per_user < €20
     * 
     * High/medium tools are candidates for cancellation/downgrading
     * Used by: Low Usage Tools endpoint
     */
    private String calculateWarningLevel(BigDecimal costPerUser, int activeUsers) {
        // Business rule: 0 users = automatic high warning (wasted money)
        if (activeUsers == 0) {
            return "high";
        }
        
        // Apply cost thresholds per instructions
        if (costPerUser.compareTo(new BigDecimal("50")) > 0) {
            return "high";  // Over €50/user - serious concern
        } else if (costPerUser.compareTo(new BigDecimal("20")) >= 0) {
            return "medium";  // €20-50/user - review needed
        } else {
            return "low";  // Under €20/user - acceptable for low usage
        }
    }

    private String getPotentialAction(String warningLevel) {
        switch (warningLevel) {
            case "high":
                return "Consider canceling or downgrading";
            case "medium":
                return "Review usage and consider optimization";
            case "low":
            default:
                return "Monitor usage trends";
        }
    }

    private String calculateVendorEfficiency(BigDecimal avgCostPerUser) {
        if (avgCostPerUser.compareTo(new BigDecimal("5")) < 0) {
            return "excellent";
        } else if (avgCostPerUser.compareTo(new BigDecimal("15")) < 0) {
            return "good";
        } else if (avgCostPerUser.compareTo(new BigDecimal("25")) <= 0) {
            return "average";
        } else {
            return "poor";
        }
    }
}
