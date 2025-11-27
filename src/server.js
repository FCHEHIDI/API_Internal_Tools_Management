/**
 * Server entry point
 */
import app from './app.js';
import { config } from './config/index.js';
import { pool } from './database/connection.js';

const PORT = config.port;

// Test database connection
async function testDatabaseConnection() {
  try {
    const client = await pool.connect();
    console.log('✓ Database connection successful');
    client.release();
  } catch (error) {
    console.error('✗ Database connection failed:', error.message);
    process.exit(1);
  }
}

// Start server
async function startServer() {
  try {
    await testDatabaseConnection();
    
    app.listen(PORT, () => {
      console.log(`🚀 Server running on http://localhost:${PORT}`);
      console.log(`📊 Database: ${config.database.host}:${config.database.port}/${config.database.database}`);
      console.log(`📝 Environment: ${config.env}`);
    });
  } catch (error) {
    console.error('Failed to start server:', error);
    process.exit(1);
  }
}

// Handle graceful shutdown
process.on('SIGTERM', async () => {
  console.log('SIGTERM signal received: closing HTTP server');
  await pool.end();
  process.exit(0);
});

process.on('SIGINT', async () => {
  console.log('\n🛑 Shutting down gracefully...');
  await pool.end();
  console.log('✓ Database connections closed');
  process.exit(0);
});

startServer();
