import { Injectable, Inject, BadRequestException } from '@nestjs/common';
import { Pool } from 'pg';
import { DATABASE_POOL } from '../database/database.module';

@Injectable()
export class AnalyticsService {
  constructor(@Inject(DATABASE_POOL) private pool: Pool) {}

  async getDepartmentCosts(year: number, month: number) {
    if (!year || !month) {
      throw new BadRequestException('Year and month parameters are required');
    }

    const result = await this.pool.query(
      `SELECT 
        t.owner_department as department,
        SUM(t.monthly_cost * COALESCE(ud.active_users, t.active_users_count, 0)) as total_cost,
        COUNT(t.id) as tool_count,
        AVG(t.monthly_cost) as avg_cost_per_tool
      FROM tools t
      LEFT JOIN usage_data ud ON t.id = ud.tool_id 
        AND ud.year = $1 AND ud.month = $2
      WHERE t.status = 'active'
      GROUP BY t.owner_department
      ORDER BY total_cost DESC`,
      [year, month],
    );

    return {
      year,
      month,
      departments: result.rows,
    };
  }

  async getExpensiveTools(limit: number = 10) {
    const result = await this.pool.query(
      `SELECT t.id, t.name, t.monthly_cost, 
              c.name as category, t.owner_department
       FROM tools t
       LEFT JOIN categories c ON t.category_id = c.id
       WHERE t.status = 'active'
       ORDER BY t.monthly_cost DESC
       LIMIT $1`,
      [Math.min(limit, 50)],
    );

    return {
      limit,
      tools: result.rows,
    };
  }

  async getToolsByCategory() {
    const result = await this.pool.query(
      `SELECT 
        COALESCE(c.name, 'Uncategorized') as category,
        COUNT(t.id) as tool_count,
        SUM(t.monthly_cost) as total_monthly_cost,
        ROUND(AVG(t.monthly_cost)::numeric, 2) as avg_cost
      FROM tools t
      LEFT JOIN categories c ON t.category_id = c.id
      WHERE t.status = 'active'
      GROUP BY c.name
      ORDER BY total_monthly_cost DESC`,
    );

    return {
      categories: result.rows,
    };
  }

  async getLowUsageTools(year: number, month: number, threshold: number = 5) {
    if (!year || !month) {
      throw new BadRequestException('Year and month parameters are required');
    }

    const result = await this.pool.query(
      `SELECT t.id, t.name, t.monthly_cost, t.active_users_count,
              t.owner_department, t.vendor
       FROM tools t
       WHERE t.status = 'active' 
       AND t.active_users_count < $1
       ORDER BY t.monthly_cost DESC`,
      [threshold],
    );

    return {
      year,
      month,
      threshold,
      tools: result.rows,
    };
  }

  async getVendorSummary() {
    const result = await this.pool.query(
      `SELECT 
        vendor,
        COUNT(id) as tool_count,
        SUM(monthly_cost) as total_monthly_cost,
        ROUND(AVG(monthly_cost)::numeric, 2) as avg_cost
      FROM tools
      WHERE status = 'active'
      GROUP BY vendor
      ORDER BY total_monthly_cost DESC`,
    );

    return {
      vendors: result.rows,
    };
  }
}
