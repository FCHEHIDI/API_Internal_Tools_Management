import {
  Injectable,
  Inject,
  NotFoundException,
  BadRequestException,
} from '@nestjs/common';
import { Pool } from 'pg';
import { DATABASE_POOL } from '../database/database.module';
import { CreateToolDto } from './dto/create-tool.dto';
import { UpdateToolDto } from './dto/update-tool.dto';
import { FilterToolsDto } from './dto/filter-tools.dto';
import { Tool } from './entities/tool.entity';

@Injectable()
export class ToolsService {
  constructor(@Inject(DATABASE_POOL) private pool: Pool) {}

  async findAll(filterDto: FilterToolsDto) {
    const {
      category_id,
      status,
      vendor,
      search,
      skip = 0,
      limit = 100,
    } = filterDto;

    const params: any[] = [];
    let paramCount = 1;
    const filtersApplied: any = {};
    let whereClause = 'WHERE 1=1';

    if (category_id) {
      whereClause += ` AND t.category_id = $${paramCount++}`;
      params.push(category_id);
      filtersApplied.category_id = category_id;
    }

    if (status) {
      whereClause += ` AND t.status = $${paramCount++}`;
      params.push(status);
      filtersApplied.status = status;
    }

    if (vendor) {
      whereClause += ` AND t.vendor ILIKE $${paramCount++}`;
      params.push(`%${vendor}%`);
      filtersApplied.vendor = vendor;
    }

    if (search) {
      whereClause += ` AND (t.name ILIKE $${paramCount} OR t.description ILIKE $${paramCount})`;
      params.push(`%${search}%`);
      paramCount++;
      filtersApplied.search = search;
    }

    // Get total count (all records)
    const totalResult = await this.pool.query('SELECT COUNT(*) FROM tools');
    const total = parseInt(totalResult.rows[0].count);

    // Get filtered count
    const filteredResult = await this.pool.query(
      `SELECT COUNT(*) FROM tools t ${whereClause}`,
      params,
    );
    const filtered = parseInt(filteredResult.rows[0].count);

    // Get paginated data
    const dataQuery = `
      SELECT t.*, c.name as category
      FROM tools t
      LEFT JOIN categories c ON t.category_id = c.id
      ${whereClause}
      ORDER BY t.created_at DESC
      LIMIT $${paramCount++} OFFSET $${paramCount}
    `;
    params.push(limit, skip);

    const result = await this.pool.query(dataQuery, params);

    return {
      data: result.rows,
      total,
      filtered,
      filters_applied: filtersApplied,
    };
  }

  async findOne(id: number): Promise<Tool> {
    const result = await this.pool.query(
      `SELECT t.*, c.name as category
       FROM tools t
       LEFT JOIN categories c ON t.category_id = c.id
       WHERE t.id = $1`,
      [id],
    );

    if (result.rows.length === 0) {
      throw new NotFoundException(`Tool with ID ${id} not found`);
    }

    return result.rows[0];
  }

  async create(createToolDto: CreateToolDto): Promise<Tool> {
    const {
      name,
      description,
      vendor,
      website_url,
      category_id,
      monthly_cost,
      owner_department,
      status = 'active',
      active_users_count = 0,
    } = createToolDto;

    try {
      const result = await this.pool.query(
        `INSERT INTO tools (
          name, description, vendor, website_url, category_id,
          monthly_cost, owner_department, status, active_users_count
        ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)
        RETURNING *`,
        [
          name,
          description,
          vendor,
          website_url,
          category_id,
          monthly_cost,
          owner_department,
          status,
          active_users_count,
        ],
      );

      // Fetch with category name
      return this.findOne(result.rows[0].id);
    } catch (error: any) {
      if (error.code === '23505') {
        throw new BadRequestException('Tool with this name already exists');
      }
      if (error.code === '23503') {
        throw new BadRequestException('Invalid category_id');
      }
      throw error;
    }
  }

  async update(id: number, updateToolDto: UpdateToolDto): Promise<Tool> {
    // Check if tool exists
    await this.findOne(id);

    const fields: string[] = [];
    const values: any[] = [];
    let paramCount = 1;

    Object.entries(updateToolDto).forEach(([key, value]) => {
      if (value !== undefined) {
        fields.push(`${key} = $${paramCount++}`);
        values.push(value);
      }
    });

    if (fields.length === 0) {
      return this.findOne(id);
    }

    fields.push(`updated_at = CURRENT_TIMESTAMP`);
    values.push(id);

    const query = `
      UPDATE tools
      SET ${fields.join(', ')}
      WHERE id = $${paramCount}
      RETURNING *
    `;

    try {
      await this.pool.query(query, values);
      return this.findOne(id);
    } catch (error: any) {
      if (error.code === '23505') {
        throw new BadRequestException('Tool with this name already exists');
      }
      if (error.code === '23503') {
        throw new BadRequestException('Invalid category_id');
      }
      throw error;
    }
  }

  async remove(id: number): Promise<void> {
    const result = await this.pool.query(
      'DELETE FROM tools WHERE id = $1 RETURNING id',
      [id],
    );

    if (result.rows.length === 0) {
      throw new NotFoundException(`Tool with ID ${id} not found`);
    }
  }
}
