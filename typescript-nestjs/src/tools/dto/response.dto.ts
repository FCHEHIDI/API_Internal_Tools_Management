import { ApiProperty } from '@nestjs/swagger';
import { Tool } from '../entities/tool.entity';

export class ToolsListResponseDto {
  @ApiProperty({ type: [Tool], description: 'Array of tools' })
  data: Tool[];

  @ApiProperty({ example: 50, description: 'Total number of tools in database' })
  total: number;

  @ApiProperty({ example: 20, description: 'Number of tools matching filters' })
  filtered: number;

  @ApiProperty({
    example: { status: 'active', vendor: 'GitHub' },
    description: 'Applied filters',
  })
  filters_applied: Record<string, any>;
}

export class DeleteResponseDto {
  @ApiProperty({ example: 'Tool deleted successfully' })
  message: string;
}
