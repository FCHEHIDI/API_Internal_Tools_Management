class CreateTools < ActiveRecord::Migration[8.1]
  def change
    create_table :tools do |t|
      t.string :name, null: false, limit: 100
      t.text :description
      t.string :vendor, null: false
      t.string :website_url
      t.references :category, null: false, foreign_key: true
      t.decimal :monthly_cost, precision: 10, scale: 2, null: false
      t.integer :active_users_count
      t.string :owner_department, null: false, limit: 50
      t.string :status, null: false, default: 'active', limit: 20

      t.timestamps
    end

    add_index :tools, :name, unique: true
    add_index :tools, :owner_department
    add_index :tools, :status
  end
end
