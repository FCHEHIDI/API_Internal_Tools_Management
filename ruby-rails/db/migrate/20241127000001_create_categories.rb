class CreateCategories < ActiveRecord::Migration[8.1]
  def change
    create_table :categories do |t|
      t.string :name, null: false, limit: 50
      t.text :description
      t.string :color_hex, limit: 7

      t.timestamp :created_at, null: false
    end

    add_index :categories, :name, unique: true
  end
end
