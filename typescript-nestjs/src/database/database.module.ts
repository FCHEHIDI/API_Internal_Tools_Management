import { Module, Global } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { Pool } from 'pg';

export const DATABASE_POOL = 'DATABASE_POOL';

const databasePoolFactory = {
  provide: DATABASE_POOL,
  useFactory: (configService: ConfigService) => {
    return new Pool({
      host: configService.get('POSTGRES_HOST', 'localhost'),
      port: configService.get('POSTGRES_PORT', 5432),
      database: configService.get('POSTGRES_DB', 'internal_tools'),
      user: configService.get('POSTGRES_USER', 'dev'),
      password: configService.get('POSTGRES_PASSWORD', 'dev123'),
      min: 2,
      max: 10,
      idleTimeoutMillis: 30000,
      connectionTimeoutMillis: 2000,
    });
  },
  inject: [ConfigService],
};

@Global()
@Module({
  providers: [databasePoolFactory],
  exports: [DATABASE_POOL],
})
export class DatabaseModule {}
