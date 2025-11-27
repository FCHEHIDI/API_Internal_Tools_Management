/**
 * Tools CRUD routes
 */
import express from 'express';
import { pool } from '../database/connection.js';
import { asyncHandler, AppError } from '../middleware/errorHandler.js';

const router = express.Router();

// GET /api/tools - List tools with filters
router.get('/', asyncHandler(async (req, res) => {
  const {
    category_id,
    status,
    vendor,
    search,
    skip = 0,
    limit = 100,
  } = req.query;

  let query = `
    SELECT t.*, c.name as category
    FROM tools t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE 1=1
  `;
  
  const params = [];
  let paramCount = 1;

  if (category_id) {
    query += ` AND t.category_id = $${paramCount++}`;
    params.push(category_id);
  }

  if (status) {
    query += ` AND t.status = $${paramCount++}`;
    params.push(status);
  }

  if (vendor) {
    query += ` AND t.vendor ILIKE $${paramCount++}`;
    params.push(`%${vendor}%`);
  }

  if (search) {
    query += ` AND (t.name ILIKE $${paramCount++} OR t.description ILIKE $${paramCount})`;
    params.push(`%${search}%`, `%${search}%`);
    paramCount++;
  }

  query += ` ORDER BY t.created_at DESC LIMIT $${paramCount++} OFFSET $${paramCount}`;
  params.push(limit, skip);

  const result = await pool.query(query, params);
  res.json(result.rows);
}));

// GET /api/tools/:id - Get tool by ID
router.get('/:id', asyncHandler(async (req, res) => {
  const { id } = req.params;

  const result = await pool.query(
    `SELECT t.*, c.name as category
     FROM tools t
     LEFT JOIN categories c ON t.category_id = c.id
     WHERE t.id = $1`,
    [id]
  );

  if (result.rows.length === 0) {
    throw new AppError(`Tool with ID ${id} not found`, 404);
  }

  res.json(result.rows[0]);
}));

// POST /api/tools - Create new tool
router.post('/', asyncHandler(async (req, res) => {
  const {
    name,
    description,
    vendor,
    website_url,
    category_id,
    monthly_cost,
    owner_department,
    status = 'active',
  } = req.body;

  // Basic validation
  if (!name || !vendor || !category_id || monthly_cost === undefined || !owner_department) {
    throw new AppError('Missing required fields', 400);
  }

  const result = await pool.query(
    `INSERT INTO tools (name, description, vendor, website_url, category_id, monthly_cost, owner_department, status)
     VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
     RETURNING *`,
    [name, description, vendor, website_url, category_id, monthly_cost, owner_department, status]
  );

  res.status(201).json(result.rows[0]);
}));

// PUT /api/tools/:id - Update tool
router.put('/:id', asyncHandler(async (req, res) => {
  const { id } = req.params;
  const updates = req.body;

  // Check if tool exists
  const existing = await pool.query('SELECT * FROM tools WHERE id = $1', [id]);
  if (existing.rows.length === 0) {
    throw new AppError(`Tool with ID ${id} not found`, 404);
  }

  const fields = [];
  const values = [];
  let paramCount = 1;

  Object.keys(updates).forEach((key) => {
    if (updates[key] !== undefined) {
      fields.push(`${key} = $${paramCount++}`);
      values.push(updates[key]);
    }
  });

  if (fields.length === 0) {
    throw new AppError('No fields to update', 400);
  }

  fields.push(`updated_at = CURRENT_TIMESTAMP`);
  values.push(id);

  const query = `UPDATE tools SET ${fields.join(', ')} WHERE id = $${paramCount} RETURNING *`;
  const result = await pool.query(query, values);

  res.json(result.rows[0]);
}));

// DELETE /api/tools/:id - Delete tool
router.delete('/:id', asyncHandler(async (req, res) => {
  const { id } = req.params;

  const result = await pool.query('DELETE FROM tools WHERE id = $1 RETURNING *', [id]);

  if (result.rows.length === 0) {
    throw new AppError(`Tool with ID ${id} not found`, 404);
  }

  res.status(204).send();
}));

export default router;
