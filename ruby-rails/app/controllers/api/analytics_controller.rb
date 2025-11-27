module Api
  class AnalyticsController < ApplicationController
    # GET /api/analytics/department-costs
    def department_costs
      results = Tool.group(:owner_department)
                    .select(
                      'owner_department',
                      'COUNT(*) as tool_count',
                      'SUM(monthly_cost) as total_monthly_cost',
                      'AVG(monthly_cost) as average_cost_per_tool'
                    )
                    .order('total_monthly_cost DESC')

      total_cost = results.sum(&:total_monthly_cost)

      departments = results.map do |r|
        {
          department: r.owner_department,
          tool_count: r.tool_count,
          total_monthly_cost: r.total_monthly_cost.to_f.round(2),
          average_cost_per_tool: r.average_cost_per_tool.to_f.round(2),
          percentage_of_budget: total_cost > 0 ? ((r.total_monthly_cost / total_cost) * 100).round(2) : 0
        }
      end

      # Apply sorting if requested
      if params[:sort_by].present?
        sort_field = params[:sort_by]
        sort_order = params[:order] == 'asc' ? 1 : -1
        departments.sort_by! { |d| d[sort_field.to_sym] * sort_order }
      end

      render json: {
        departments: departments,
        summary: {
          total_monthly_cost: total_cost.to_f.round(2),
          total_annual_cost: (total_cost * 12).to_f.round(2)
        }
      }
    end

    # GET /api/analytics/expensive-tools
    def expensive_tools
      limit = [params[:limit].to_i, 50].min
      limit = 10 if limit <= 0

      tools = Tool.includes(:category)
                  .order(monthly_cost: :desc)
                  .limit(limit)

      results = tools.map do |tool|
        efficiency_ratio = tool.active_users_count.to_i > 0 ? 
                          (tool.monthly_cost / tool.active_users_count).to_f.round(2) : 
                          tool.monthly_cost.to_f

        efficiency_rating = case efficiency_ratio
                           when 0..10 then 'excellent'
                           when 10..50 then 'good'
                           when 50..100 then 'average'
                           else 'low'
                           end

        potential_savings = tool.monthly_cost > 500 ? (tool.monthly_cost * 0.15).round(2) : 0

        {
          id: tool.id,
          name: tool.name,
          vendor: tool.vendor,
          category_name: tool.category.name,
          monthly_cost: tool.monthly_cost.to_f.round(2),
          annual_cost: (tool.monthly_cost * 12).to_f.round(2),
          active_users_count: tool.active_users_count,
          cost_per_user: efficiency_ratio,
          efficiency_rating: efficiency_rating,
          potential_annual_savings: potential_savings * 12
        }
      end

      render json: {
        expensive_tools: results,
        summary: {
          total_cost_top_tools: results.sum { |t| t[:monthly_cost] }.round(2),
          total_potential_annual_savings: results.sum { |t| t[:potential_annual_savings] }.round(2)
        }
      }
    end

    # GET /api/analytics/tools-by-category
    def tools_by_category
      results = Tool.joins(:category)
                    .group('categories.id', 'categories.name')
                    .select(
                      'categories.id as category_id',
                      'categories.name as category_name',
                      'COUNT(tools.id) as tool_count',
                      'SUM(tools.monthly_cost) as total_cost',
                      'AVG(tools.monthly_cost) as average_cost'
                    )
                    .order('total_cost DESC')

      total_cost = results.sum(&:total_cost)

      categories = results.map do |r|
        {
          category_id: r.category_id,
          category_name: r.category_name,
          tool_count: r.tool_count,
          total_monthly_cost: r.total_cost.to_f.round(2),
          average_cost_per_tool: r.average_cost.to_f.round(2),
          percentage_of_total_cost: total_cost > 0 ? ((r.total_cost / total_cost) * 100).round(2) : 0
        }
      end

      most_efficient = categories.min_by { |c| c[:average_cost_per_tool] } || {}

      render json: {
        categories: categories,
        summary: {
          total_categories: categories.length,
          most_efficient_category: most_efficient[:category_name]
        }
      }
    end

    # GET /api/analytics/low-usage-tools
    def low_usage_tools
      max_users = params[:max_users].to_i
      max_users = 10 if max_users <= 0

      tools = Tool.includes(:category)
                  .where('active_users_count <= ? OR active_users_count IS NULL', max_users)
                  .order(:active_users_count)

      results = tools.map do |tool|
        users = tool.active_users_count || 0
        warning_level = users == 0 ? 'high' : (users <= 3 ? 'medium' : 'low')
        potential_savings = (tool.monthly_cost * 0.5).round(2)

        {
          id: tool.id,
          name: tool.name,
          vendor: tool.vendor,
          category_name: tool.category.name,
          monthly_cost: tool.monthly_cost.to_f.round(2),
          active_users_count: users,
          warning_level: warning_level,
          potential_monthly_savings: potential_savings,
          recommendation: users == 0 ? 'Consider immediate cancellation' : 'Review usage and negotiate better pricing'
        }
      end

      render json: {
        low_usage_tools: results,
        summary: {
          total_underutilized_tools: results.length,
          total_potential_monthly_savings: results.sum { |t| t[:potential_monthly_savings] }.round(2),
          total_potential_annual_savings: (results.sum { |t| t[:potential_monthly_savings] } * 12).round(2)
        }
      }
    end

    # GET /api/analytics/vendor-summary
    def vendor_summary
      results = Tool.group(:vendor)
                    .select(
                      'vendor',
                      'COUNT(*) as tool_count',
                      'SUM(monthly_cost) as total_monthly_cost',
                      'AVG(monthly_cost) as average_cost',
                      'SUM(active_users_count) as total_users',
                      'COUNT(DISTINCT owner_department) as department_count'
                    )
                    .order('total_monthly_cost DESC')

      vendors = results.map do |r|
        total_users = r.total_users.to_i
        total_cost = r.total_monthly_cost.to_f
        
        efficiency_ratio = total_users > 0 ? (total_cost / total_users).round(2) : total_cost
        efficiency_rating = efficiency_ratio < 20 ? 'excellent' : (efficiency_ratio < 50 ? 'good' : 'needs_review')
        
        consolidation_opportunity = r.tool_count > 1 && r.department_count == 1

        {
          vendor: r.vendor,
          tool_count: r.tool_count,
          total_monthly_cost: total_cost.round(2),
          average_cost_per_tool: r.average_cost.to_f.round(2),
          total_active_users: total_users,
          unique_departments: r.department_count,
          efficiency_rating: efficiency_rating,
          consolidation_opportunity: consolidation_opportunity,
          recommendation: consolidation_opportunity ? 'Consolidate tools from this vendor' : 'Current setup is efficient'
        }
      end

      render json: {
        vendors: vendors,
        summary: {
          total_vendors: vendors.length,
          total_monthly_spend: vendors.sum { |v| v[:total_monthly_cost] }.round(2)
        }
      }
    end
  end
end
