/**
 * Test setup and global configuration
 */

// Set test environment
process.env.NODE_ENV = 'test';
process.env.DB_HOST = 'localhost';
process.env.POSTGRES_USER = 'dev';
process.env.POSTGRES_PASSWORD = 'dev123';
process.env.POSTGRES_DATABASE = 'internal_tools';
process.env.POSTGRES_PORT = '5432';

