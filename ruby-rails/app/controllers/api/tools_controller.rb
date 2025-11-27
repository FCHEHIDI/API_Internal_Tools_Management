module Api
  class ToolsController < ApplicationController
    before_action :set_tool, only: [:show, :update, :destroy]

    # GET /api/tools
    def index
      tools = Tool.includes(:category)
                  .by_department(params[:department])
                  .by_status(params[:status])
                  .by_category(params[:category_id])
                  .search_by_name(params[:search])

      # Cost range filter
      if params[:min_cost].present? && params[:max_cost].present?
        tools = tools.cost_between(params[:min_cost], params[:max_cost])
      end

      # Sorting
      sort_by = params[:sort_by] || 'created_at'
      order = params[:order] || 'desc'
      tools = tools.order("#{sort_by} #{order}")

      # Pagination
      limit = [params[:limit].to_i, 100].min
      limit = 20 if limit <= 0
      tools = tools.limit(limit)

      render json: tools.map { |tool| tool_with_category(tool) }
    end

    # POST /api/tools
    def create
      tool = Tool.new(tool_params)

      if tool.save
        render json: tool_with_category(tool), status: :created
      else
        render json: { errors: tool.errors.full_messages }, status: :unprocessable_entity
      end
    end

    # GET /api/tools/:id
    def show
      render json: tool_with_total_cost(@tool)
    end

    # PUT/PATCH /api/tools/:id
    def update
      if @tool.update(tool_params)
        render json: tool_with_category(@tool)
      else
        render json: { errors: @tool.errors.full_messages }, status: :unprocessable_entity
      end
    end

    # DELETE /api/tools/:id
    def destroy
      @tool.destroy
      head :no_content
    end

    private

    def set_tool
      @tool = Tool.includes(:category).find(params[:id])
    rescue ActiveRecord::RecordNotFound
      render json: { error: 'Tool not found' }, status: :not_found
    end

    def tool_params
      params.require(:tool).permit(
        :name, :description, :vendor, :website_url, :category_id,
        :monthly_cost, :active_users_count, :owner_department, :status
      )
    end

    def tool_with_category(tool)
      tool.as_json.merge(category_name: tool.category.name)
    end

    def tool_with_total_cost(tool)
      total = tool.monthly_cost * 12
      tool_with_category(tool).merge(total_monthly_cost: total)
    end
  end
end
