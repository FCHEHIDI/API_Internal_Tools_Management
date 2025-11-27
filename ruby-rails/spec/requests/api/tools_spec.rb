require 'swagger_helper'

RSpec.describe 'api/tools', type: :request do
  path '/api/tools' do
    get('List all tools') do
      tags 'Tools'
      produces 'application/json'
      parameter name: :department, in: :query, type: :string, required: false, description: 'Filter by department'
      parameter name: :status, in: :query, type: :string, required: false, description: 'Filter by status'
      parameter name: :category_id, in: :query, type: :integer, required: false, description: 'Filter by category'
      parameter name: :search, in: :query, type: :string, required: false, description: 'Search by name'
      parameter name: :min_cost, in: :query, type: :number, required: false, description: 'Minimum monthly cost'
      parameter name: :max_cost, in: :query, type: :number, required: false, description: 'Maximum monthly cost'
      parameter name: :sort_by, in: :query, type: :string, required: false, description: 'Sort field'
      parameter name: :order, in: :query, type: :string, required: false, description: 'Sort order (asc/desc)'
      parameter name: :limit, in: :query, type: :integer, required: false, description: 'Number of results (max 100)'

      response(200, 'successful') do
        schema type: :array,
          items: { '$ref' => '#/components/schemas/Tool' }
        run_test!
      end
    end

    post('Create a tool') do
      tags 'Tools'
      consumes 'application/json'
      produces 'application/json'
      parameter name: :tool, in: :body, schema: {
        type: :object,
        properties: {
          tool: {
            type: :object,
            properties: {
              name: { type: :string },
              description: { type: :string },
              vendor: { type: :string },
              website_url: { type: :string },
              category_id: { type: :integer },
              monthly_cost: { type: :number },
              active_users_count: { type: :integer },
              owner_department: { type: :string },
              status: { type: :string }
            },
            required: ['name', 'vendor', 'category_id', 'monthly_cost', 'owner_department']
          }
        }
      }

      response(201, 'created') do
        schema '$ref' => '#/components/schemas/Tool'
        run_test!
      end

      response(422, 'unprocessable entity') do
        run_test!
      end
    end
  end

  path '/api/tools/{id}' do
    parameter name: 'id', in: :path, type: :integer, description: 'Tool ID'

    get('Show a tool') do
      tags 'Tools'
      produces 'application/json'

      response(200, 'successful') do
        schema '$ref' => '#/components/schemas/Tool'
        run_test!
      end

      response(404, 'not found') do
        run_test!
      end
    end

    put('Update a tool') do
      tags 'Tools'
      consumes 'application/json'
      produces 'application/json'
      parameter name: :tool, in: :body, schema: {
        type: :object,
        properties: {
          tool: {
            type: :object,
            properties: {
              name: { type: :string },
              description: { type: :string },
              vendor: { type: :string },
              website_url: { type: :string },
              category_id: { type: :integer },
              monthly_cost: { type: :number },
              active_users_count: { type: :integer },
              owner_department: { type: :string },
              status: { type: :string }
            }
          }
        }
      }

      response(200, 'successful') do
        schema '$ref' => '#/components/schemas/Tool'
        run_test!
      end

      response(404, 'not found') do
        run_test!
      end

      response(422, 'unprocessable entity') do
        run_test!
      end
    end

    delete('Delete a tool') do
      tags 'Tools'

      response(204, 'no content') do
        run_test!
      end

      response(404, 'not found') do
        run_test!
      end
    end
  end
end
