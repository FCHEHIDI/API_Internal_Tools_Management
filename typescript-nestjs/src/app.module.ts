import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { ToolsModule } from './tools/tools.module';
import { AnalyticsModule } from './analytics/analytics.module';
import { HealthModule } from './health/health.module';
import { DatabaseModule } from './database/database.module';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: '.env',
    }),
    DatabaseModule,
    ToolsModule,
    AnalyticsModule,
    HealthModule,
  ],
})
export class AppModule {}
