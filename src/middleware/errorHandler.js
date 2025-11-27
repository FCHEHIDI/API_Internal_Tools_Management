/**
 * Error handling middleware
 */

export class AppError extends Error {
  constructor(message, statusCode = 500) {
    super(message);
    this.statusCode = statusCode;
    this.isOperational = true;
    Error.captureStackTrace(this, this.constructor);
  }
}

export const notFoundHandler = (req, res, next) => {
  const error = new AppError(`Route ${req.originalUrl} not found`, 404);
  next(error);
};

export const errorHandler = (err, req, res, next) => {
  let { statusCode = 500, message } = err;

  // Handle validation errors
  if (err.name === 'ValidationError') {
    statusCode = 400;
    message = Object.values(err.errors).map(e => e.message).join(', ');
  }

  // Handle database errors
  if (err.code === '23505') { // Unique violation
    statusCode = 409;
    message = 'Resource already exists';
  }

  if (err.code === '23503') { // Foreign key violation
    statusCode = 400;
    message = 'Referenced resource does not exist';
  }

  res.status(statusCode).json({
    error: message,
    ...(process.env.NODE_ENV === 'development' && { stack: err.stack }),
  });
};

export const asyncHandler = (fn) => (req, res, next) => {
  Promise.resolve(fn(req, res, next)).catch(next);
};
