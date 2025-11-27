import { ApiPropertyOptional } from '@nestjs/swagger';
import { IsOptional, IsString, IsEnum, IsNumber, Min } from 'class-validator';
import { ToolStatus } from './create-tool.dto';

export class FilterToolsDto {
  @ApiPropertyOptional({ example: 1 })
  @IsOptional()
  @IsNumber()
  category_id?: number;

  @ApiPropertyOptional({ enum: ToolStatus, example: ToolStatus.ACTIVE })
  @IsOptional()
  @IsEnum(ToolStatus)
  status?: ToolStatus;

  @ApiPropertyOptional({ example: 'GitHub' })
  @IsOptional()
  @IsString()
  vendor?: string;

  @ApiPropertyOptional({ example: 'messaging' })
  @IsOptional()
  @IsString()
  search?: string;

  @ApiPropertyOptional({ example: 0, default: 0 })
  @IsOptional()
  @IsNumber()
  @Min(0)
  skip?: number;

  @ApiPropertyOptional({ example: 100, default: 100 })
  @IsOptional()
  @IsNumber()
  @Min(1)
  limit?: number;
}
