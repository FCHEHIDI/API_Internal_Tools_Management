/**
 * Main Express application entry point
 */
import express from 'express';
import helmet from 'helmet';
import cors from 'cors';
import morgan from 'morgan';
import compression from 'compression';
import { config } from './config/index.js';
import { errorHandler, notFoundHandler } from './middleware/errorHandler.js';
import healthRouter from './routes/health.js';
import toolsRouter from './routes/tools.js';
import analyticsRouter from './routes/analytics.js';

const app = express();

// Security middleware
app.use(helmet());
app.use(cors({ origin: config.cors.origin }));

// Body parsing middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Compression middleware
app.use(compression());

// Logging middleware
if (config.env !== 'test') {
  app.use(morgan('combined'));
}

// Root endpoint
app.get('/', (req, res) => {
  res.json({
    message: 'Welcome to Internal Tools Management API',
    version: '1.0.0',
    docs: '/docs',
    health: '/health',
  });
});

// API routes
app.use('/health', healthRouter);
app.use('/api/tools', toolsRouter);
app.use('/api/analytics', analyticsRouter);

// Error handling
app.use(notFoundHandler);
app.use(errorHandler);

export default app;
