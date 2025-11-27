/**
 * Global teardown - runs after all tests
 */
import { pool } from '../src/database/connection.js';

export default async () => {
  // Close database pool
  await pool.end();
  console.log('Database pool closed');
};
