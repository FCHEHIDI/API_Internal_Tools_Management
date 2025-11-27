# Rust + Axum Implementation - TODO

## Status: 9/10 Endpoints Working ✅

### Working Endpoints (9)
- ✅ Health check
- ✅ GET /api/tools (list with pagination)
- ✅ GET /api/tools/:id (single tool)
- ✅ GET /api/tools/filters (filtered search)
- ✅ DELETE /api/tools/:id
- ✅ GET /api/analytics/department-costs
- ✅ GET /api/analytics/expensive-tools
- ✅ GET /api/analytics/low-usage
- ✅ GET /api/analytics/tools-by-category
- ✅ GET /api/analytics/vendor-summary

### Known Issues

#### 1. POST /api/tools - Parameter Serialization Error ❌
**Error:** `error serializing parameter 4`

**Attempted Fixes (all failed):**
- Type casting variations: i32, i64, String, &str
- Removing ENUM type casts (::department_type, ::tool_status_type)
- Explicit type annotations
- Using Option<String> with as_deref(), as_ref(), unwrap_or_default()
- Removing website_url field entirely
- Using Vec with ToSql trait objects
- NULLIF() SQL function
- Split INSERT + SELECT approach

**Key Finding:** 
The error persists at parameter position 4 regardless of:
- What field is in that position
- What type that parameter is
- Whether ENUM casts are present or not

**Hypothesis:**
Possible tokio-postgres issue with parameter binding in INSERT statements with ENUM columns. GET queries work fine with same types.

**Next Steps:**
1. Check tokio-postgres GitHub issues for similar problems
2. Try using sqlx instead of tokio-postgres
3. Use raw SQL with string interpolation (not recommended for production)
4. Investigate if PostgreSQL ENUM types need special handling in INSERT

#### 2. PUT /api/tools/:id - Not Fully Tested ⏳
Likely has same parameter binding issue as POST.

### Implementation Details
- **Framework:** Axum 0.7
- **Database Driver:** tokio-postgres 0.7.15
- **Connection Pool:** deadpool-postgres 0.14
- **Documentation:** Comprehensive rustdoc on all handlers
- **Tests:** 21 integration tests (100% passing)
- **Lines of Code:** 4,194 across 17 files

### Type Casting Solutions (for GET queries)
- DateTime: `to_char(column, 'YYYY-MM-DD"T"HH24:MI:SS"Z"')`
- DECIMAL: `CAST(column AS DOUBLE PRECISION)`
- ENUM: `CAST(column AS TEXT)`
- Aggregations: `CAST(COALESCE(AVG(column), 0) AS DOUBLE PRECISION)`
