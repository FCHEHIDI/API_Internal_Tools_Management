class Tool < ApplicationRecord
  belongs_to :category

  validates :name, presence: true, uniqueness: true, length: { minimum: 2, maximum: 100 }
  validates :vendor, presence: true
  validates :category_id, presence: true
  validates :monthly_cost, presence: true, numericality: { greater_than_or_equal_to: 0 }
  validates :owner_department, presence: true, inclusion: { 
    in: %w[Engineering Sales Marketing HR Finance Operations], 
    message: "%{value} is not a valid department" 
  }
  validates :website_url, format: { with: URI::DEFAULT_PARSER.make_regexp(['http', 'https']), message: "must be a valid URL" }, allow_blank: true
  validates :status, inclusion: { in: %w[active inactive trial], message: "%{value} is not a valid status" }
  validates :active_users_count, numericality: { only_integer: true, greater_than_or_equal_to: 0 }, allow_nil: true

  # Scopes for filtering
  scope :active, -> { where(status: 'active') }
  scope :by_department, ->(department) { where(owner_department: department) if department.present? }
  scope :by_status, ->(status) { where(status: status) if status.present? }
  scope :by_category, ->(category_id) { where(category_id: category_id) if category_id.present? }
  scope :cost_between, ->(min, max) { where(monthly_cost: min..max) if min.present? && max.present? }
  scope :search_by_name, ->(query) { where("name ILIKE ?", "%#{query}%") if query.present? }
end
