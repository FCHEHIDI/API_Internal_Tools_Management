/**
 * Analytics endpoint tests
 */
import request from 'supertest';
import app from '../src/app.js';

describe('Analytics Endpoints', () => {
  describe('GET /api/analytics/department-costs', () => {
    it('should require year and month parameters', async () => {
      const response = await request(app)
        .get('/api/analytics/department-costs')
        .expect(400);

      expect(response.body).toHaveProperty('error');
      expect(response.body.error.toLowerCase()).toContain('year');
    });

    it('should return department cost breakdown', async () => {
      const response = await request(app)
        .get('/api/analytics/department-costs?year=2025&month=11')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      if (response.body.length > 0) {
        expect(response.body[0]).toHaveProperty('department');
        expect(response.body[0]).toHaveProperty('total_cost');
        expect(response.body[0]).toHaveProperty('tool_count');
      }
    });

    it('should return departments ordered by cost', async () => {
      const response = await request(app)
        .get('/api/analytics/department-costs?year=2025&month=11')
        .expect(200);

      if (response.body.length > 1) {
        const costs = response.body.map(d => parseFloat(d.total_cost));
        const sortedCosts = [...costs].sort((a, b) => b - a);
        expect(costs).toEqual(sortedCosts);
      }
    });

    it('should have numeric cost values', async () => {
      const response = await request(app)
        .get('/api/analytics/department-costs?year=2025&month=11')
        .expect(200);

      response.body.forEach(dept => {
        expect(typeof dept.total_cost).toBe('string');
        expect(parseFloat(dept.total_cost)).not.toBeNaN();
        expect(parseInt(dept.tool_count)).toBeGreaterThanOrEqual(0);
      });
    });
  });

  describe('GET /api/analytics/expensive-tools', () => {
    it('should return most expensive tools', async () => {
      const response = await request(app)
        .get('/api/analytics/expensive-tools')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      if (response.body.length > 0) {
        expect(response.body[0]).toHaveProperty('id');
        expect(response.body[0]).toHaveProperty('name');
        expect(response.body[0]).toHaveProperty('monthly_cost');
        expect(response.body[0]).toHaveProperty('vendor');
      }
    });

    it('should respect limit parameter', async () => {
      const limit = 5;
      const response = await request(app)
        .get(`/api/analytics/expensive-tools?limit=${limit}`)
        .expect(200);

      expect(response.body.length).toBeLessThanOrEqual(limit);
    });

    it('should return tools ordered by cost descending', async () => {
      const response = await request(app)
        .get('/api/analytics/expensive-tools?limit=10')
        .expect(200);

      if (response.body.length > 1) {
        const costs = response.body.map(t => parseFloat(t.monthly_cost));
        const sortedCosts = [...costs].sort((a, b) => b - a);
        expect(costs).toEqual(sortedCosts);
      }
    });

    it('should include category information', async () => {
      const response = await request(app)
        .get('/api/analytics/expensive-tools?limit=5')
        .expect(200);

      response.body.forEach(tool => {
        expect(tool).toHaveProperty('category_name');
      });
    });

    it('should use default limit of 10', async () => {
      const response = await request(app)
        .get('/api/analytics/expensive-tools')
        .expect(200);

      expect(response.body.length).toBeLessThanOrEqual(10);
    });
  });

  describe('GET /api/analytics/tools-by-category', () => {
    it('should return category distribution', async () => {
      const response = await request(app)
        .get('/api/analytics/tools-by-category')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      if (response.body.length > 0) {
        expect(response.body[0]).toHaveProperty('category_id');
        expect(response.body[0]).toHaveProperty('category_name');
        expect(response.body[0]).toHaveProperty('tool_count');
        expect(response.body[0]).toHaveProperty('total_monthly_cost');
      }
    });

    it('should order categories by total cost descending', async () => {
      const response = await request(app)
        .get('/api/analytics/tools-by-category')
        .expect(200);

      if (response.body.length > 1) {
        const costs = response.body.map(c => parseFloat(c.total_monthly_cost));
        const sortedCosts = [...costs].sort((a, b) => b - a);
        expect(costs).toEqual(sortedCosts);
      }
    });

    it('should handle categories with no tools', async () => {
      const response = await request(app)
        .get('/api/analytics/tools-by-category')
        .expect(200);

      const emptyCats = response.body.filter(c => parseInt(c.tool_count) === 0);
      emptyCats.forEach(cat => {
        expect(parseFloat(cat.total_monthly_cost)).toBe(0);
      });
    });

    it('should have valid numeric values', async () => {
      const response = await request(app)
        .get('/api/analytics/tools-by-category')
        .expect(200);

      response.body.forEach(category => {
        expect(parseInt(category.tool_count)).toBeGreaterThanOrEqual(0);
        expect(parseFloat(category.total_monthly_cost)).toBeGreaterThanOrEqual(0);
      });
    });
  });

  describe('GET /api/analytics/low-usage-tools', () => {
    it('should require year and month parameters', async () => {
      const response = await request(app)
        .get('/api/analytics/low-usage-tools')
        .expect(400);

      expect(response.body).toHaveProperty('error');
    });

    it('should return low usage tools', async () => {
      const response = await request(app)
        .get('/api/analytics/low-usage-tools?year=2025&month=11')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      if (response.body.length > 0) {
        expect(response.body[0]).toHaveProperty('id');
        expect(response.body[0]).toHaveProperty('name');
        expect(response.body[0]).toHaveProperty('active_users_count');
        expect(response.body[0]).toHaveProperty('monthly_cost');
      }
    });

    it('should respect threshold parameter', async () => {
      const threshold = 3;
      const response = await request(app)
        .get(`/api/analytics/low-usage-tools?year=2025&month=11&threshold=${threshold}`)
        .expect(200);

      response.body.forEach(tool => {
        expect(tool.active_users_count).toBeLessThanOrEqual(threshold);
      });
    });

    it('should use default threshold of 5', async () => {
      const response = await request(app)
        .get('/api/analytics/low-usage-tools?year=2025&month=11')
        .expect(200);

      response.body.forEach(tool => {
        expect(tool.active_users_count).toBeLessThanOrEqual(5);
      });
    });

    it('should include department and vendor info', async () => {
      const response = await request(app)
        .get('/api/analytics/low-usage-tools?year=2025&month=11&threshold=10')
        .expect(200);

      response.body.forEach(tool => {
        expect(tool).toHaveProperty('department');
        expect(tool).toHaveProperty('vendor');
      });
    });
  });

  describe('GET /api/analytics/vendor-summary', () => {
    it('should return vendor summary', async () => {
      const response = await request(app)
        .get('/api/analytics/vendor-summary')
        .expect(200);

      expect(Array.isArray(response.body)).toBe(true);
      if (response.body.length > 0) {
        expect(response.body[0]).toHaveProperty('vendor');
        expect(response.body[0]).toHaveProperty('tools_count');
        expect(response.body[0]).toHaveProperty('total_monthly_cost');
        expect(response.body[0]).toHaveProperty('total_users');
      }
    });

    it('should order vendors by total cost descending', async () => {
      const response = await request(app)
        .get('/api/analytics/vendor-summary')
        .expect(200);

      if (response.body.length > 1) {
        const costs = response.body.map(v => parseFloat(v.total_monthly_cost));
        const sortedCosts = [...costs].sort((a, b) => b - a);
        expect(costs).toEqual(sortedCosts);
      }
    });

    it('should have valid numeric aggregations', async () => {
      const response = await request(app)
        .get('/api/analytics/vendor-summary')
        .expect(200);

      response.body.forEach(vendor => {
        expect(parseInt(vendor.tools_count)).toBeGreaterThan(0);
        expect(parseFloat(vendor.total_monthly_cost)).toBeGreaterThan(0);
        expect(parseInt(vendor.total_users)).toBeGreaterThanOrEqual(0);
      });
    });
  });

  describe('Analytics - All endpoints accessible', () => {
    it('should access all analytics endpoints without errors', async () => {
      const endpoints = [
        '/api/analytics/department-costs?year=2025&month=11',
        '/api/analytics/expensive-tools',
        '/api/analytics/tools-by-category',
        '/api/analytics/low-usage-tools?year=2025&month=11',
        '/api/analytics/vendor-summary',
      ];

      for (const endpoint of endpoints) {
        const response = await request(app).get(endpoint);
        expect(response.status).toBe(200);
        expect(Array.isArray(response.body)).toBe(true);
      }
    });
  });
});
