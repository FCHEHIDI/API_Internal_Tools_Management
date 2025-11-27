require 'swagger_helper'

RSpec.describe 'api/analytics', type: :request do
  path '/api/analytics/department-costs' do
    get('Department costs analysis') do
      tags 'Analytics'
      produces 'application/json'
      parameter name: :sort_by, in: :query, type: :string, required: false
      parameter name: :order, in: :query, type: :string, required: false

      response(200, 'successful') do
        schema type: :object,
          properties: {
            departments: {
              type: :array,
              items: {
                type: :object,
                properties: {
                  department: { type: :string },
                  tool_count: { type: :integer },
                  total_monthly_cost: { type: :number },
                  average_cost_per_tool: { type: :number },
                  percentage_of_budget: { type: :number }
                }
              }
            },
            summary: {
              type: :object,
              properties: {
                total_monthly_cost: { type: :number },
                total_annual_cost: { type: :number }
              }
            }
          }
        run_test!
      end
    end
  end

  path '/api/analytics/expensive-tools' do
    get('Most expensive tools') do
      tags 'Analytics'
      produces 'application/json'
      parameter name: :limit, in: :query, type: :integer, required: false

      response(200, 'successful') do
        schema type: :object,
          properties: {
            expensive_tools: {
              type: :array,
              items: {
                type: :object,
                properties: {
                  id: { type: :integer },
                  name: { type: :string },
                  vendor: { type: :string },
                  category_name: { type: :string },
                  monthly_cost: { type: :number },
                  annual_cost: { type: :number },
                  active_users_count: { type: :integer },
                  cost_per_user: { type: :number },
                  efficiency_rating: { type: :string },
                  potential_annual_savings: { type: :number }
                }
              }
            },
            summary: {
              type: :object,
              properties: {
                total_cost_top_tools: { type: :number },
                total_potential_annual_savings: { type: :number }
              }
            }
          }
        run_test!
      end
    end
  end

  path '/api/analytics/tools-by-category' do
    get('Tools distribution by category') do
      tags 'Analytics'
      produces 'application/json'

      response(200, 'successful') do
        schema type: :object,
          properties: {
            categories: {
              type: :array,
              items: {
                type: :object,
                properties: {
                  category_id: { type: :integer },
                  category_name: { type: :string },
                  tool_count: { type: :integer },
                  total_monthly_cost: { type: :number },
                  average_cost_per_tool: { type: :number },
                  percentage_of_total_cost: { type: :number }
                }
              }
            },
            summary: {
              type: :object,
              properties: {
                total_categories: { type: :integer },
                most_efficient_category: { type: :string }
              }
            }
          }
        run_test!
      end
    end
  end

  path '/api/analytics/low-usage-tools' do
    get('Tools with low usage') do
      tags 'Analytics'
      produces 'application/json'
      parameter name: :max_users, in: :query, type: :integer, required: false

      response(200, 'successful') do
        schema type: :object,
          properties: {
            low_usage_tools: {
              type: :array,
              items: {
                type: :object,
                properties: {
                  id: { type: :integer },
                  name: { type: :string },
                  vendor: { type: :string },
                  category_name: { type: :string },
                  monthly_cost: { type: :number },
                  active_users_count: { type: :integer },
                  warning_level: { type: :string },
                  potential_monthly_savings: { type: :number },
                  recommendation: { type: :string }
                }
              }
            },
            summary: {
              type: :object,
              properties: {
                total_underutilized_tools: { type: :integer },
                total_potential_monthly_savings: { type: :number },
                total_potential_annual_savings: { type: :number }
              }
            }
          }
        run_test!
      end
    end
  end

  path '/api/analytics/vendor-summary' do
    get('Vendor summary analysis') do
      tags 'Analytics'
      produces 'application/json'

      response(200, 'successful') do
        schema type: :object,
          properties: {
            vendors: {
              type: :array,
              items: {
                type: :object,
                properties: {
                  vendor: { type: :string },
                  tool_count: { type: :integer },
                  total_monthly_cost: { type: :number },
                  average_cost_per_tool: { type: :number },
                  total_active_users: { type: :integer },
                  unique_departments: { type: :integer },
                  efficiency_rating: { type: :string },
                  consolidation_opportunity: { type: :boolean },
                  recommendation: { type: :string }
                }
              }
            },
            summary: {
              type: :object,
              properties: {
                total_vendors: { type: :integer },
                total_monthly_spend: { type: :number }
              }
            }
          }
        run_test!
      end
    end
  end
end
