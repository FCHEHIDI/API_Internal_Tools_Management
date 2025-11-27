//! Database connection pooling module.
//!
//! Provides async PostgreSQL connection pooling using deadpool-postgres.
//! Configuration is loaded from environment variables.

use deadpool_postgres::{Config, ManagerConfig, Pool, RecyclingMethod, Runtime};
use tokio_postgres::NoTls;

/// Type alias for the database connection pool.
///
/// Uses deadpool-postgres for async connection pooling with:
/// - Fast recycling method for optimal performance
/// - Tokio runtime for async operations
/// - NoTls for local development (use TLS in production)
pub type DbPool = Pool;

/// Creates and configures a database connection pool.
///
/// # Environment Variables
/// * `DB_HOST` - Database host (default: "localhost")
/// * `DB_PORT` - Database port (default: "5432")
/// * `DB_USER` - Database user (default: "dev")
/// * `DB_PASSWORD` - Database password (default: "dev123")
/// * `DB_NAME` - Database name (default: "internal_tools")
///
/// # Returns
/// * `Ok(DbPool)` - Configured connection pool ready for use
/// * `Err` - If pool creation fails
///
/// # Example
/// ```rust,no_run
/// use internal_tools_api::db::create_pool;
///
/// #[tokio::main]
/// async fn main() {
///     let pool = create_pool().expect("Failed to create pool");
///     let client = pool.get().await.expect("Failed to get connection");
/// }
/// ```
///
/// # Panics
/// Panics if environment variables cannot be parsed (e.g., invalid port number).
pub fn create_pool() -> Result<DbPool, Box<dyn std::error::Error>> {
    let mut cfg = Config::new();
    cfg.host = Some(std::env::var("DB_HOST").unwrap_or_else(|_| "localhost".to_string()));
    cfg.port = Some(
        std::env::var("DB_PORT")
            .unwrap_or_else(|_| "5432".to_string())
            .parse()
            .unwrap(),
    );
    cfg.user = Some(std::env::var("DB_USER").unwrap_or_else(|_| "dev".to_string()));
    cfg.password = Some(std::env::var("DB_PASSWORD").unwrap_or_else(|_| "dev123".to_string()));
    cfg.dbname = Some(std::env::var("DB_NAME").unwrap_or_else(|_| "internal_tools".to_string()));
    
    cfg.manager = Some(ManagerConfig {
        recycling_method: RecyclingMethod::Fast,
    });

    let pool = cfg.create_pool(Some(Runtime::Tokio1), NoTls)?;
    
    Ok(pool)
}
