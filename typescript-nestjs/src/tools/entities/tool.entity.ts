import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class Tool {
  @ApiProperty({ example: 1 })
  id: number;

  @ApiProperty({ example: 'Slack' })
  name: string;

  @ApiProperty({ example: 'Team messaging platform' })
  description: string;

  @ApiProperty({ example: 'Slack Technologies' })
  vendor: string;

  @ApiPropertyOptional({ example: 'https://slack.com' })
  website_url?: string;

  @ApiProperty({ example: 1 })
  category_id: number;

  @ApiPropertyOptional({ example: 'Communication' })
  category?: string;

  @ApiProperty({ example: 8.0 })
  monthly_cost: number;

  @ApiProperty({ example: 'Engineering' })
  owner_department: string;

  @ApiProperty({ example: 'active' })
  status: string;

  @ApiProperty({ example: 25 })
  active_users_count: number;

  @ApiProperty({ example: '2025-05-01T09:00:00Z' })
  created_at: Date;

  @ApiProperty({ example: '2025-05-01T09:00:00Z' })
  updated_at: Date;
}
