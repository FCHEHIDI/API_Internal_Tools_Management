import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  Query,
  ParseIntPipe,
  HttpCode,
  HttpStatus,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse } from '@nestjs/swagger';
import { ToolsService } from './tools.service';
import { CreateToolDto } from './dto/create-tool.dto';
import { UpdateToolDto } from './dto/update-tool.dto';
import { FilterToolsDto } from './dto/filter-tools.dto';
import { Tool } from './entities/tool.entity';
import { ToolsListResponseDto } from './dto/response.dto';

@ApiTags('tools')
@Controller('tools')
export class ToolsController {
  constructor(private readonly toolsService: ToolsService) {}

  @Get()
  @ApiOperation({ summary: 'Get all tools with filters' })
  @ApiResponse({
    status: 200,
    description: 'List of tools retrieved',
    type: ToolsListResponseDto,
  })
  findAll(@Query() filterDto: FilterToolsDto): Promise<ToolsListResponseDto> {
    return this.toolsService.findAll(filterDto);
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get tool by ID' })
  @ApiResponse({ status: 200, description: 'Tool found', type: Tool })
  @ApiResponse({ status: 404, description: 'Tool not found' })
  findOne(@Param('id', ParseIntPipe) id: number): Promise<Tool> {
    return this.toolsService.findOne(id);
  }

  @Post()
  @HttpCode(HttpStatus.CREATED)
  @ApiOperation({ summary: 'Create new tool' })
  @ApiResponse({ status: 201, description: 'Tool created', type: Tool })
  @ApiResponse({ status: 400, description: 'Bad request' })
  create(@Body() createToolDto: CreateToolDto): Promise<Tool> {
    return this.toolsService.create(createToolDto);
  }

  @Put(':id')
  @ApiOperation({ summary: 'Update tool' })
  @ApiResponse({ status: 200, description: 'Tool updated', type: Tool })
  @ApiResponse({ status: 404, description: 'Tool not found' })
  update(
    @Param('id', ParseIntPipe) id: number,
    @Body() updateToolDto: UpdateToolDto,
  ): Promise<Tool> {
    return this.toolsService.update(id, updateToolDto);
  }

  @Delete(':id')
  @HttpCode(HttpStatus.NO_CONTENT)
  @ApiOperation({ summary: 'Delete tool' })
  @ApiResponse({ status: 204, description: 'Tool deleted' })
  @ApiResponse({ status: 404, description: 'Tool not found' })
  remove(@Param('id', ParseIntPipe) id: number): Promise<void> {
    return this.toolsService.remove(id);
  }
}
