//! Internal Tools Management API
//!
//! A high-performance REST API built with Rust and Axum for managing
//! internal software tools, subscriptions, and cost analytics.
//!
//! # Features
//! - Complete CRUD operations for tools management
//! - Advanced analytics endpoints
//! - OpenAPI/Swagger documentation
//! - Async database operations with connection pooling
//! - Type-safe request validation
//!
//! # Example
//! ```rust,no_run
//! use internal_tools_api::models::Tool;
//! ```

pub mod db;
pub mod handlers;
pub mod models;
