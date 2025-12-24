/**
 * Generic server response wrapper
 * Used for all API responses from the backend
 */
export interface ServerResponseData<T = any> {
  status?: number;
  message?: string;
  data?: T;
  totalCount?: number;
}

/**
 * Typed server response for better type safety
 */
export interface ApiResponse<T> {
  status: number;
  message?: string;
  data: T;
  totalCount?: number;
}

/**
 * Error response from server
 */
export interface ApiErrorResponse {
  status: number;
  message: string;
  error: string;
  details?: Record<any, any>;
}

/**
 * Paginated response wrapper
 */
export interface PaginatedServerResponse<T> {
  status?: number;
  message?: string;
  data: {
    items: T[];
    pagination: {
      total: number;
      page: number;
      pageSize: number;
      totalPages: number;
    };
  };
}
