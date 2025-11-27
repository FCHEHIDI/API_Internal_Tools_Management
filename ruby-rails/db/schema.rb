# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# This file is the source Rails uses to define your schema when running `bin/rails
# db:schema:load`. When creating a new database, `bin/rails db:schema:load` tends to
# be faster and is potentially less error prone than running all of your
# migrations from scratch. Old migrations may fail to apply correctly if those
# migrations use external dependencies or application code.
#
# It's strongly recommended that you check this file into your version control system.

ActiveRecord::Schema[8.1].define(version: 2024_11_27_000002) do
  # These are extensions that must be enabled in order to support this database
  enable_extension "pg_catalog.plpgsql"

  # Custom types defined in this database.
  # Note that some types may not work with other database engines. Be careful if changing database.
  create_enum "access_status_type", ["active", "revoked"]
  create_enum "department_type", ["Engineering", "Sales", "Marketing", "HR", "Finance", "Operations", "Design"]
  create_enum "request_status_type", ["pending", "approved", "rejected"]
  create_enum "tool_status_type", ["active", "deprecated", "trial"]
  create_enum "user_role_type", ["employee", "manager", "admin"]
  create_enum "user_status_type", ["active", "inactive"]

  create_table "access_requests", id: :serial, force: :cascade do |t|
    t.text "business_justification", null: false
    t.datetime "processed_at", precision: nil
    t.integer "processed_by"
    t.text "processing_notes"
    t.datetime "requested_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.enum "status", default: "pending", enum_type: "request_status_type"
    t.integer "tool_id", null: false
    t.integer "user_id", null: false
    t.index ["requested_at"], name: "idx_requests_date"
    t.index ["status"], name: "idx_requests_status"
    t.index ["user_id"], name: "idx_requests_user"
    t.check_constraint "processed_at IS NULL OR processed_at >= requested_at", name: "chk_process_after_request"
  end

  create_table "categories", id: :serial, force: :cascade do |t|
    t.string "color_hex", limit: 7, default: "#6366f1"
    t.datetime "created_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.text "description"
    t.string "name", limit: 50, null: false

    t.unique_constraint ["name"], name: "categories_name_key"
  end

  create_table "cost_tracking", id: :serial, force: :cascade do |t|
    t.integer "active_users_count", default: 0, null: false
    t.datetime "created_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.date "month_year", null: false
    t.integer "tool_id", null: false
    t.decimal "total_monthly_cost", precision: 10, scale: 2, null: false
    t.index ["month_year", "tool_id"], name: "idx_cost_month_tool"
    t.check_constraint "total_monthly_cost >= 0::numeric AND active_users_count >= 0", name: "chk_positive_tracking"
    t.unique_constraint ["tool_id", "month_year"], name: "cost_tracking_tool_id_month_year_key"
  end

  create_table "migrations", id: :serial, force: :cascade do |t|
    t.integer "batch", null: false
    t.string "migration", limit: 255, null: false
  end

  create_table "tools", id: :serial, force: :cascade do |t|
    t.integer "active_users_count", default: 0, null: false
    t.integer "category_id", null: false
    t.datetime "created_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.text "description"
    t.decimal "monthly_cost", precision: 10, scale: 2, null: false
    t.string "name", limit: 100, null: false
    t.enum "owner_department", null: false, enum_type: "department_type"
    t.enum "status", default: "active", enum_type: "tool_status_type"
    t.datetime "updated_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.string "vendor", limit: 100
    t.string "website_url", limit: 255
    t.index ["active_users_count"], name: "idx_tools_active_users", order: :desc
    t.index ["category_id"], name: "idx_tools_category"
    t.index ["monthly_cost"], name: "idx_tools_cost_desc", order: :desc
    t.index ["owner_department"], name: "idx_tools_department"
    t.index ["status"], name: "idx_tools_status"
    t.check_constraint "active_users_count >= 0", name: "chk_positive_users"
    t.check_constraint "monthly_cost >= 0::numeric", name: "chk_positive_cost"
  end

  create_table "usage_logs", id: :serial, force: :cascade do |t|
    t.integer "actions_count", default: 0
    t.datetime "created_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.date "session_date", null: false
    t.integer "tool_id", null: false
    t.integer "usage_minutes", default: 0
    t.integer "user_id", null: false
    t.index ["session_date", "tool_id"], name: "idx_usage_date_tool"
    t.index ["user_id", "session_date"], name: "idx_usage_user_date"
  end

  create_table "user_tool_access", id: :serial, force: :cascade do |t|
    t.datetime "granted_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.integer "granted_by", null: false
    t.datetime "revoked_at", precision: nil
    t.integer "revoked_by"
    t.enum "status", default: "active", enum_type: "access_status_type"
    t.integer "tool_id", null: false
    t.integer "user_id", null: false
    t.index ["granted_at"], name: "idx_access_granted_date"
    t.index ["status"], name: "idx_access_status"
    t.index ["tool_id"], name: "idx_access_tool"
    t.index ["user_id"], name: "idx_access_user"
    t.check_constraint "revoked_at IS NULL OR revoked_at >= granted_at", name: "chk_revoke_after_grant"
    t.unique_constraint ["user_id", "tool_id", "status"], name: "user_tool_access_user_id_tool_id_status_key"
  end

  create_table "users", id: :serial, force: :cascade do |t|
    t.datetime "created_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.enum "department", null: false, enum_type: "department_type"
    t.string "email", limit: 150, null: false
    t.date "hire_date"
    t.string "name", limit: 100, null: false
    t.enum "role", default: "employee", enum_type: "user_role_type"
    t.enum "status", default: "active", enum_type: "user_status_type"
    t.datetime "updated_at", precision: nil, default: -> { "CURRENT_TIMESTAMP" }
    t.index ["department"], name: "idx_users_department"
    t.index ["status"], name: "idx_users_status"
    t.unique_constraint ["email"], name: "users_email_key"
  end

  add_foreign_key "access_requests", "tools", name: "access_requests_tool_id_fkey", on_delete: :cascade
  add_foreign_key "access_requests", "users", column: "processed_by", name: "access_requests_processed_by_fkey", on_delete: :nullify
  add_foreign_key "access_requests", "users", name: "access_requests_user_id_fkey", on_delete: :cascade
  add_foreign_key "cost_tracking", "tools", name: "cost_tracking_tool_id_fkey", on_delete: :cascade
  add_foreign_key "tools", "categories", name: "tools_category_id_fkey", on_delete: :restrict
  add_foreign_key "usage_logs", "tools", name: "usage_logs_tool_id_fkey", on_delete: :cascade
  add_foreign_key "usage_logs", "users", name: "usage_logs_user_id_fkey", on_delete: :cascade
  add_foreign_key "user_tool_access", "tools", name: "user_tool_access_tool_id_fkey", on_delete: :cascade
  add_foreign_key "user_tool_access", "users", column: "granted_by", name: "user_tool_access_granted_by_fkey", on_delete: :restrict
  add_foreign_key "user_tool_access", "users", column: "revoked_by", name: "user_tool_access_revoked_by_fkey", on_delete: :nullify
  add_foreign_key "user_tool_access", "users", name: "user_tool_access_user_id_fkey", on_delete: :cascade
end
