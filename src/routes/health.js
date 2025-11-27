/**
 * Health check routes
 */
import express from 'express';
import { pool } from '../database/connection.js';
import { asyncHandler } from '../middleware/errorHandler.js';

const router = express.Router();

router.get('/', asyncHandler(async (req, res) => {
  const startTime = Date.now();
  
  try {
    // Test database connection
    const result = await pool.query('SELECT NOW()');
    const responseTime = Date.now() - startTime;
    
    res.json({
      status: 'healthy',
      timestamp: new Date().toISOString(),
      database: 'connected',
      responseTime: `${responseTime}ms`,
    });
  } catch (error) {
    res.status(503).json({
      status: 'unhealthy',
      timestamp: new Date().toISOString(),
      database: 'disconnected',
      error: error.message,
    });
  }
}));

export default router;
