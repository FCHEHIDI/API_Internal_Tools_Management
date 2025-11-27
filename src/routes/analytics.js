/**
 * Analytics routes
 */
import express from 'express';
import { pool } from '../database/connection.js';
import { asyncHandler, AppError } from '../middleware/errorHandler.js';

const router = express.Router();

// GET /api/analytics/department-costs
router.get('/department-costs', asyncHandler(async (req, res) => {
  const { year, month } = req.query;

  if (!year || !month) {
    throw new AppError('Year and month parameters are required', 400);
  }

  const query = `
    SELECT 
      owner_department as department,
      SUM(monthly_cost) as total_cost,
      COUNT(*) as tool_count
    FROM tools
    WHERE status = 'active'
    GROUP BY owner_department
    ORDER BY total_cost DESC
  `;

  const result = await pool.query(query);
  res.json(result.rows);
}));

// GET /api/analytics/expensive-tools
router.get('/expensive-tools', asyncHandler(async (req, res) => {
  const { limit = 10 } = req.query;

  const query = `
    SELECT 
      t.id,
      t.name,
      t.vendor,
      t.monthly_cost,
      t.active_users_count,
      c.name as category_name
    FROM tools t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.status = 'active'
    ORDER BY t.monthly_cost DESC
    LIMIT $1
  `;

  const result = await pool.query(query, [limit]);
  res.json(result.rows);
}));

// GET /api/analytics/tools-by-category
router.get('/tools-by-category', asyncHandler(async (req, res) => {
  const query = `
    SELECT 
      c.id as category_id,
      c.name as category_name,
      COUNT(t.id) as tool_count,
      COALESCE(SUM(t.monthly_cost), 0) as total_monthly_cost
    FROM categories c
    LEFT JOIN tools t ON c.id = t.category_id AND t.status = 'active'
    GROUP BY c.id, c.name
    ORDER BY total_monthly_cost DESC
  `;

  const result = await pool.query(query);
  res.json(result.rows);
}));

// GET /api/analytics/low-usage-tools
router.get('/low-usage-tools', asyncHandler(async (req, res) => {
  const { year, month, threshold = 5 } = req.query;

  if (!year || !month) {
    throw new AppError('Year and month parameters are required', 400);
  }

  const query = `
    SELECT 
      t.id,
      t.name,
      t.monthly_cost,
      t.active_users_count,
      t.owner_department as department,
      t.vendor
    FROM tools t
    WHERE t.status = 'active'
      AND t.active_users_count <= $1
    ORDER BY t.monthly_cost DESC
  `;

  const result = await pool.query(query, [threshold]);
  res.json(result.rows);
}));

// GET /api/analytics/vendor-summary
router.get('/vendor-summary', asyncHandler(async (req, res) => {
  const query = `
    SELECT 
      vendor,
      COUNT(*) as tools_count,
      SUM(monthly_cost) as total_monthly_cost,
      SUM(active_users_count) as total_users
    FROM tools
    WHERE status = 'active'
    GROUP BY vendor
    ORDER BY total_monthly_cost DESC
  `;

  const result = await pool.query(query);
  res.json(result.rows);
}));

export default router;
