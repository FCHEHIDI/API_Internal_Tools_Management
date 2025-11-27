import { Injectable, Inject } from '@nestjs/common';
import { Pool } from 'pg';
import { DATABASE_POOL } from '../database/database.module';

@Injectable()
export class HealthService {
  constructor(@Inject(DATABASE_POOL) private pool: Pool) {}

  async check() {
    const startTime = Date.now();
    let database = 'disconnected';

    try {
      await this.pool.query('SELECT 1');
      database = 'connected';
    } catch (error) {
      database = 'disconnected';
    }

    const responseTime = Date.now() - startTime;

    return {
      status: database === 'connected' ? 'healthy' : 'unhealthy',
      timestamp: new Date().toISOString(),
      database,
      responseTime: `${responseTime}ms`,
    };
  }
}
