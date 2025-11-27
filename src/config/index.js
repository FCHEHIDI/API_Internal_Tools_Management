/**
 * Application configuration
 */
import dotenv from 'dotenv';

dotenv.config();

export const config = {
  env: process.env.NODE_ENV || 'development',
  port: parseInt(process.env.PORT, 10) || 8000,
  
  database: {
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.POSTGRES_PORT || process.env.DB_PORT, 10) || 5432,
    database: process.env.POSTGRES_DATABASE || process.env.DB_NAME || 'internal_tools',
    user: process.env.POSTGRES_USER || process.env.DB_USER || 'dev',
    password: process.env.POSTGRES_PASSWORD || process.env.DB_PASSWORD || 'dev123',
    min: parseInt(process.env.DB_POOL_MIN, 10) || 2,
    max: parseInt(process.env.DB_POOL_MAX, 10) || 10,
  },
  
  cors: {
    origin: process.env.CORS_ORIGIN || '*',
  },
};
