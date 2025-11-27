import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import {
  IsString,
  IsNumber,
  IsEnum,
  IsUrl,
  IsOptional,
  MinLength,
  MaxLength,
  Min,
} from 'class-validator';

export enum ToolStatus {
  ACTIVE = 'active',
  DEPRECATED = 'deprecated',
  TRIAL = 'trial',
}

export enum Department {
  ENGINEERING = 'Engineering',
  SALES = 'Sales',
  MARKETING = 'Marketing',
  HR = 'HR',
  FINANCE = 'Finance',
  OPERATIONS = 'Operations',
  DESIGN = 'Design',
}

export class CreateToolDto {
  @ApiProperty({ example: 'Slack', minLength: 2, maxLength: 100 })
  @IsString()
  @MinLength(2)
  @MaxLength(100)
  name: string;

  @ApiProperty({ example: 'Team messaging platform' })
  @IsString()
  description: string;

  @ApiProperty({ example: 'Slack Technologies', maxLength: 100 })
  @IsString()
  @MaxLength(100)
  vendor: string;

  @ApiPropertyOptional({ example: 'https://slack.com' })
  @IsOptional()
  @IsUrl()
  website_url?: string;

  @ApiProperty({ example: 1 })
  @IsNumber()
  category_id: number;

  @ApiProperty({ example: 8.0, minimum: 0 })
  @IsNumber({ maxDecimalPlaces: 2 })
  @Min(0)
  monthly_cost: number;

  @ApiProperty({ enum: Department, example: Department.ENGINEERING })
  @IsEnum(Department)
  owner_department: Department;

  @ApiPropertyOptional({ enum: ToolStatus, default: ToolStatus.ACTIVE })
  @IsOptional()
  @IsEnum(ToolStatus)
  status?: ToolStatus;

  @ApiPropertyOptional({ example: 0, default: 0 })
  @IsOptional()
  @IsNumber()
  @Min(0)
  active_users_count?: number;
}
