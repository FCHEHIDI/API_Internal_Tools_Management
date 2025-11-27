class Category < ApplicationRecord
  has_many :tools, dependent: :destroy

  validates :name, presence: true, length: { minimum: 2, maximum: 50 }
  validates :color_hex, format: { with: /\A#[0-9A-Fa-f]{6}\z/, message: "must be a valid hex color" }, allow_blank: true
end
