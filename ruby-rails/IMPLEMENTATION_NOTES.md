# Ruby + Rails Implementation - TODO

## Status: Project Structure Created, Pending Gem Installation

### Stack Information
- **Framework:** Ruby on Rails 8.1.1 (API-only mode)
- **Ruby Version:** 3.3.10
- **ORM:** ActiveRecord (built into Rails)
- **Database:** PostgreSQL with `pg` gem
- **API Documentation:** Can add `rswag` for Swagger/OpenAPI

### Current Issue
Native extension compilation failed on Windows for `psych` gem (YAML parser). This is a common Windows + Ruby issue.

### Solutions
1. **Use WSL2/Linux** - Rails works best on Linux/macOS
2. **Use Docker** - Run Rails in container
3. **Install MSYS2 dependencies** - Fix native extensions

### Project Structure Created
```
ruby-rails/
├── app/
│   ├── controllers/
│   ├── models/
│   └── jobs/
├── config/
│   ├── database.yml
│   ├── routes.rb
│   └── initializers/
├── db/
│   └── seeds.rb
├── Gemfile
└── config.ru
```

### Next Steps to Complete

#### 1. Fix Bundle Install
```bash
cd ruby-rails
bundle install
```

#### 2. Configure Database
Edit `config/database.yml`:
```yaml
default: &default
  adapter: postgresql
  encoding: unicode
  pool: <%= ENV.fetch("RAILS_MAX_THREADS") { 5 } %>
  host: localhost
  port: 5432
  username: dev
  password: dev123

development:
  <<: *default
  database: internal_tools
```

#### 3. Generate Models
```bash
rails generate model Tool name:string description:text vendor:string website_url:string category_id:integer monthly_cost:decimal active_users_count:integer owner_department:string status:string

rails generate model Category name:string description:text

rails db:create
rails db:migrate
```

#### 4. Create Controllers
```bash
rails generate controller api/tools
rails generate controller api/analytics
```

#### 5. Define Routes
In `config/routes.rb`:
```ruby
Rails.application.routes.draw do
  namespace :api do
    resources :tools
    namespace :analytics do
      get 'department-costs'
      get 'expensive-tools'
      get 'low-usage'
      get 'tools-by-category'
      get 'vendor-summary'
    end
  end
end
```

#### 6. Implement Controllers
Rails ActiveRecord provides powerful ORM:
```ruby
# app/controllers/api/tools_controller.rb
class Api::ToolsController < ApplicationController
  def index
    @tools = Tool.includes(:category)
                 .page(params[:page])
                 .per(params[:limit] || 50)
    render json: @tools
  end

  def show
    @tool = Tool.includes(:category).find(params[:id])
    render json: @tool
  end

  def create
    @tool = Tool.new(tool_params)
    if @tool.save
      render json: @tool, status: :created
    else
      render json: @tool.errors, status: :unprocessable_entity
    end
  end

  # ... update, destroy methods
end
```

### Rails Advantages
✅ **Convention over Configuration** - Minimal boilerplate
✅ **ActiveRecord ORM** - Powerful database abstraction
✅ **Migrations** - Version-controlled database schema
✅ **Validations** - Built-in model validation
✅ **Associations** - `belongs_to`, `has_many` relationships
✅ **Query Interface** - Chainable, readable queries
✅ **Serialization** - JSON/XML rendering built-in
✅ **Testing** - Built-in test framework

### Alternative: Sinatra (Lightweight)
If Rails is too heavy, consider Sinatra:
```ruby
# Gemfile
gem 'sinatra'
gem 'sinatra-activerecord'
gem 'pg'

# app.rb
require 'sinatra'
require 'sinatra/activerecord'

get '/api/tools' do
  Tool.all.to_json
end

post '/api/tools' do
  tool = Tool.create(JSON.parse(request.body.read))
  tool.to_json
end
```

### Recommended Next Action
Complete this implementation on Linux/WSL2 or using Docker for better Ruby native extension support.

---

**Note:** Ruby on Rails remains one of the best choices for rapid API development with its mature ecosystem, but Windows support for native gems can be challenging. Production Rails apps typically run on Linux servers.
