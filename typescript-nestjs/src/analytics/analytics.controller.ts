import { Controller, Get, Query, ParseIntPipe } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiQuery } from '@nestjs/swagger';
import { AnalyticsService } from './analytics.service';

@ApiTags('analytics')
@Controller('analytics')
export class AnalyticsController {
  constructor(private readonly analyticsService: AnalyticsService) {}

  @Get('department-costs')
  @ApiOperation({ summary: 'Get department cost breakdown' })
  @ApiQuery({ name: 'year', required: true, type: Number })
  @ApiQuery({ name: 'month', required: true, type: Number })
  @ApiResponse({ status: 200, description: 'Department costs retrieved' })
  getDepartmentCosts(
    @Query('year', ParseIntPipe) year: number,
    @Query('month', ParseIntPipe) month: number,
  ) {
    return this.analyticsService.getDepartmentCosts(year, month);
  }

  @Get('expensive-tools')
  @ApiOperation({ summary: 'Get most expensive tools' })
  @ApiQuery({ name: 'limit', required: false, type: Number })
  @ApiResponse({ status: 200, description: 'Expensive tools retrieved' })
  getExpensiveTools(@Query('limit', ParseIntPipe) limit?: number) {
    return this.analyticsService.getExpensiveTools(limit);
  }

  @Get('tools-by-category')
  @ApiOperation({ summary: 'Get tools distribution by category' })
  @ApiResponse({ status: 200, description: 'Category distribution retrieved' })
  getToolsByCategory() {
    return this.analyticsService.getToolsByCategory();
  }

  @Get('low-usage-tools')
  @ApiOperation({ summary: 'Get low usage tools' })
  @ApiQuery({ name: 'year', required: true, type: Number })
  @ApiQuery({ name: 'month', required: true, type: Number })
  @ApiQuery({ name: 'threshold', required: false, type: Number })
  @ApiResponse({ status: 200, description: 'Low usage tools retrieved' })
  getLowUsageTools(
    @Query('year', ParseIntPipe) year: number,
    @Query('month', ParseIntPipe) month: number,
    @Query('threshold', ParseIntPipe) threshold?: number,
  ) {
    return this.analyticsService.getLowUsageTools(year, month, threshold);
  }

  @Get('vendor-summary')
  @ApiOperation({ summary: 'Get vendor summary' })
  @ApiResponse({ status: 200, description: 'Vendor summary retrieved' })
  getVendorSummary() {
    return this.analyticsService.getVendorSummary();
  }
}
