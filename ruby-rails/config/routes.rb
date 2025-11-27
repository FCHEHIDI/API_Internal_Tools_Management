Rails.application.routes.draw do
  # Define your application routes per the DSL in https://guides.rubyonrails.org/routing.html

  # Reveal health status on /up that returns 200 if the app boots with no exceptions, otherwise 500.
  # Can be used by load balancers and uptime monitors to verify that the app is live.
  get "up" => "rails/health#show", as: :rails_health_check

  # API routes
  namespace :api do
    resources :tools

    get 'analytics/department-costs', to: 'analytics#department_costs'
    get 'analytics/expensive-tools', to: 'analytics#expensive_tools'
    get 'analytics/tools-by-category', to: 'analytics#tools_by_category'
    get 'analytics/low-usage-tools', to: 'analytics#low_usage_tools'
    get 'analytics/vendor-summary', to: 'analytics#vendor_summary'
  end

  # Defines the root path route ("/")
  # root "posts#index"
end
