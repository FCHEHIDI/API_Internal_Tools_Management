/**
 * Tools CRUD endpoint tests
 */
import request from 'supertest';
import app from '../src/app.js';
import { pool } from '../src/database/connection.js';

describe('Tools CRUD Endpoints', () => {
  let testCategoryId;

  beforeAll(async () => {
    // Get a valid category ID for tests
    const result = await pool.query('SELECT id FROM categories LIMIT 1');
    testCategoryId = result.rows[0]?.id || 1;
  });

  afterAll(async () => {
    // Close database connection
    await pool.end();
  });

  describe('GET /api/tools', () => {
    it('should return list of tools', async () => {
      const response = await request(app)
        .get('/api/tools')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      if (response.body.length > 0) {
        expect(response.body[0]).toHaveProperty('id');
        expect(response.body[0]).toHaveProperty('name');
        expect(response.body[0]).toHaveProperty('vendor');
      }
    });

    it('should filter tools by status', async () => {
      const response = await request(app)
        .get('/api/tools?status=active')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      response.body.forEach(tool => {
        expect(tool.status).toBe('active');
      });
    });

    it('should filter tools by vendor', async () => {
      const response = await request(app)
        .get('/api/tools?vendor=GitHub')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      response.body.forEach(tool => {
        expect(tool.vendor.toLowerCase()).toContain('github');
      });
    });

    it('should search tools by name or description', async () => {
      const response = await request(app)
        .get('/api/tools?search=test')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
    });

    it('should support pagination with limit', async () => {
      const response = await request(app)
        .get('/api/tools?limit=5')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      expect(response.body.length).toBeLessThanOrEqual(5);
    });

    it('should support pagination with skip and limit', async () => {
      const response = await request(app)
        .get('/api/tools?skip=10&limit=5')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      expect(response.body.length).toBeLessThanOrEqual(5);
    });
  });

  describe('GET /api/tools/:id', () => {
    let toolId;

    beforeAll(async () => {
      // Get a valid tool ID
      const result = await pool.query('SELECT id FROM tools LIMIT 1');
      toolId = result.rows[0]?.id;
    });

    it('should return tool details by ID', async () => {
      if (!toolId) {
        console.log('Skipping: No tools in database');
        return;
      }

      const response = await request(app)
        .get(`/api/tools/${toolId}`)
        .expect(200);

      expect(response.body).toHaveProperty('id', toolId);
      expect(response.body).toHaveProperty('name');
      expect(response.body).toHaveProperty('vendor');
      expect(response.body).toHaveProperty('monthly_cost');
    });

    it('should return 404 for non-existent tool', async () => {
      const response = await request(app)
        .get('/api/tools/999999999')
        .expect(404);

      expect(response.body).toHaveProperty('error');
    });

    it('should include category information', async () => {
      if (!toolId) {
        console.log('Skipping: No tools in database');
        return;
      }

      const response = await request(app)
        .get(`/api/tools/${toolId}`)
        .expect(200);

      expect(response.body).toHaveProperty('category');
    });
  });

  describe('POST /api/tools', () => {
    it('should create a new tool', async () => {
      const newTool = {
        name: `Test Tool ${Date.now()}`,
        description: 'A tool created by automated tests',
        vendor: 'Test Vendor',
        monthly_cost: 29.99,
        owner_department: 'Engineering',
        category_id: testCategoryId,
      };

      const response = await request(app)
        .post('/api/tools')
        .send(newTool)
        .expect(201);

      expect(response.body).toHaveProperty('id');
      expect(response.body.name).toBe(newTool.name);
      expect(response.body.vendor).toBe(newTool.vendor);
      expect(parseFloat(response.body.monthly_cost)).toBe(newTool.monthly_cost);

      // Cleanup
      await pool.query('DELETE FROM tools WHERE id = $1', [response.body.id]);
    });

    it('should return 400 when required fields are missing', async () => {
      const invalidTool = {
        name: 'Incomplete Tool',
        // Missing required fields
      };

      const response = await request(app)
        .post('/api/tools')
        .send(invalidTool)
        .expect(400);

      expect(response.body).toHaveProperty('error');
    });

    it('should set default status to active', async () => {
      const newTool = {
        name: `Test Tool Status ${Date.now()}`,
        description: 'Testing default status',
        vendor: 'Test Vendor',
        monthly_cost: 19.99,
        owner_department: 'Engineering',
        category_id: testCategoryId,
      };

      const response = await request(app)
        .post('/api/tools')
        .send(newTool)
        .expect(201);

      expect(response.body.status).toBe('active');

      // Cleanup
      await pool.query('DELETE FROM tools WHERE id = $1', [response.body.id]);
    });
  });

  describe('PUT /api/tools/:id', () => {
    let toolId;

    beforeEach(async () => {
      // Create a test tool
      const result = await pool.query(
        `INSERT INTO tools (name, vendor, category_id, monthly_cost, owner_department)
         VALUES ($1, $2, $3, $4, $5) RETURNING id`,
        [`Test Tool Update ${Date.now()}`, 'Test Vendor', testCategoryId, 25.00, 'Engineering']
      );
      toolId = result.rows[0].id;
    });

    afterEach(async () => {
      // Cleanup
      if (toolId) {
        await pool.query('DELETE FROM tools WHERE id = $1', [toolId]);
      }
    });

    it('should update tool successfully', async () => {
      const updates = {
        monthly_cost: 35.00,
        status: 'deprecated',
        description: 'Updated by test',
      };

      const response = await request(app)
        .put(`/api/tools/${toolId}`)
        .send(updates)
        .expect(200);

      expect(parseFloat(response.body.monthly_cost)).toBe(updates.monthly_cost);
      expect(response.body.status).toBe(updates.status);
      expect(response.body.description).toBe(updates.description);
    });

    it('should return 404 for non-existent tool', async () => {
      const response = await request(app)
        .put('/api/tools/999999999')
        .send({ monthly_cost: 50.00 })
        .expect(404);

      expect(response.body).toHaveProperty('error');
    });

    it('should preserve unmodified fields', async () => {
      const response = await request(app)
        .put(`/api/tools/${toolId}`)
        .send({ monthly_cost: 40.00 })
        .expect(200);

      expect(response.body).toHaveProperty('name');
      expect(response.body).toHaveProperty('vendor');
    });
  });

  describe('DELETE /api/tools/:id', () => {
    let toolId;

    beforeEach(async () => {
      // Create a test tool
      const result = await pool.query(
        `INSERT INTO tools (name, vendor, category_id, monthly_cost, owner_department)
         VALUES ($1, $2, $3, $4, $5) RETURNING id`,
        [`Test Tool Delete ${Date.now()}`, 'Test Vendor', testCategoryId, 15.00, 'Engineering']
      );
      toolId = result.rows[0].id;
    });

    it('should delete tool successfully', async () => {
      await request(app)
        .delete(`/api/tools/${toolId}`)
        .expect(204);

      // Verify deletion
      const result = await pool.query('SELECT * FROM tools WHERE id = $1', [toolId]);
      expect(result.rows.length).toBe(0);
    });

    it('should return 404 for non-existent tool', async () => {
      const response = await request(app)
        .delete('/api/tools/999999999')
        .expect(404);

      expect(response.body).toHaveProperty('error');
    });
  });

  describe('GET / (root)', () => {
    it('should return API information', async () => {
      const response = await request(app)
        .get('/')
        .expect(200);

      expect(response.body).toHaveProperty('message');
      expect(response.body).toHaveProperty('version');
      expect(response.body).toHaveProperty('health');
    });
  });
});
