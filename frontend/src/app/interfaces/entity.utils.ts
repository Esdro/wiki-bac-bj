/**
 * Utility functions and helpers for working with typed entities
 * These helpers make it easier to work with the interfaces and DTOs
 */

import {
  IUser,
  IResource,
  ICreateResourceDto,
  IUpdateResourceDto,
  IForumTopic,
  IExercise,
  IPaginatedResponse,
  IResourceFilter,
  IForumTopicFilter,
  ResourceStatus,
  EntityStatus,
  UserStatus
} from './entities';

// ============================================================================
// VALIDATION HELPERS
// ============================================================================

/**
 * Validates if a resource status is valid
 */
export function isValidResourceStatus(status: string): status is ResourceStatus {
  return ['draft', 'published', 'archived', 'pending_review'].includes(status);
}

/**
 * Validates if a user status is valid
 */
export function isValidUserStatus(status: string): status is UserStatus {
  return ['active', 'inactive', 'banned', 'pending'].includes(status);
}

/**
 * Validates if an entity status is valid
 */
export function isValidEntityStatus(status: string): status is EntityStatus {
  return ['active', 'inactive', 'draft', 'published', 'archived'].includes(status);
}

/**
 * Validates a rating value (should be 1-5)
 */
export function isValidRating(rating: number): boolean {
  return rating >= 1 && rating <= 5 && Number.isInteger(rating);
}

/**
 * Validates a progress percentage (should be 0-100)
 */
export function isValidProgress(progress: number): boolean {
  return progress >= 0 && progress <= 100;
}

// ============================================================================
// TYPE GUARDS
// ============================================================================

/**
 * Type guard to check if data is a valid IUser
 */
export function isUser(data: any): data is IUser {
  return (
    data &&
    typeof data.id === 'string' &&
    typeof data.email === 'string' &&
    typeof data.username === 'string'
  );
}

/**
 * Type guard to check if data is a valid IResource
 */
export function isResource(data: any): data is IResource {
  return (
    data &&
    typeof data.id === 'string' &&
    typeof data.title === 'string' &&
    data.type !== undefined
  );
}

/**
 * Type guard to check if data is a valid IForumTopic
 */
export function isForumTopic(data: any): data is IForumTopic {
  return (
    data &&
    typeof data.id === 'string' &&
    typeof data.title === 'string' &&
    data.category !== undefined &&
    data.user !== undefined
  );
}

/**
 * Type guard for paginated responses
 */
export function isPaginatedResponse<T>(data: any): data is IPaginatedResponse<T> {
  return data && Array.isArray(data.data) && data.meta && typeof data.meta.total === 'number';
}

// ============================================================================
// BUILDER HELPERS
// ============================================================================

/**
 * Helper to build ICreateResourceDto safely
 */
export function createResourceDto(params: {
  title: string;
  typeId: string;
  description?: string;
  subjectId?: string;
  chapterId?: string;
  seriesId?: string;
  year?: number;
  fileUrl?: string;
  thumbnailUrl?: string;
  status?: ResourceStatus;
}): ICreateResourceDto {
  return {
    title: params.title,
    typeId: params.typeId,
    description: params.description,
    subjectId: params.subjectId,
    chapterId: params.chapterId,
    seriesId: params.seriesId,
    year: params.year,
    fileUrl: params.fileUrl,
    thumbnailUrl: params.thumbnailUrl,
    status: params.status || 'draft'
  };
}

/**
 * Helper to build resource filter safely
 */
export function buildResourceFilter(params: Partial<IResourceFilter>): IResourceFilter {
  return {
    page: params.page || 1,
    pageSize: params.pageSize || 20,
    ...params
  };
}

/**
 * Helper to build forum topic filter safely
 */
export function buildForumTopicFilter(params: Partial<IForumTopicFilter>): IForumTopicFilter {
  return {
    page: params.page || 1,
    pageSize: params.pageSize || 20,
    ...params
  };
}

// ============================================================================
// MAPPING HELPERS
// ============================================================================

/**
 * Extract user display name (uses fullName or falls back to username)
 */
export function getUserDisplayName(user: IUser): string {
  return user.fullName || user.username;
}

/**
 * Get resource status badge color for UI
 */
export function getResourceStatusColor(status: ResourceStatus): string {
  const colors: Record<ResourceStatus, string> = {
    draft: '#FFA500',        // Orange
    published: '#28A745',    // Green
    archived: '#6C757D',     // Gray
    pending_review: '#007BFF' // Blue
  };
  return colors[status];
}

/**
 * Format resource year for display
 */
export function formatResourceYear(year: number | null | undefined): string {
  if (!year) return 'N/A';
  return `BAC ${year}`;
}

/**
 * Get rating display (e.g., "4.5/5" with star count)
 */
export function formatRating(rating: number | null | undefined): string {
  if (!rating) return 'Not rated';
  return `${rating.toFixed(1)}/5 ${getStars(rating)}`;
}

/**
 * Generate star string for rating display
 */
export function getStars(rating: number): string {
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 !== 0;
  let stars = '★'.repeat(fullStars);
  if (hasHalfStar) stars += '½';
  stars += '☆'.repeat(5 - Math.ceil(rating));
  return stars;
}

/**
 * Format date for display
 */
export function formatDate(dateString: string | null | undefined): string {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

/**
 * Format datetime for display
 */
export function formatDateTime(dateString: string | null | undefined): string {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

/**
 * Calculate time ago for relative dates (e.g., "2 heures ago")
 */
export function getTimeAgo(dateString: string | null | undefined): string {
  if (!dateString) return 'N/A';
  
  const now = new Date();
  const date = new Date(dateString);
  const seconds = Math.floor((now.getTime() - date.getTime()) / 1000);

  if (seconds < 60) return 'À l\'instant';
  if (seconds < 3600) return `${Math.floor(seconds / 60)} minute${Math.floor(seconds / 60) > 1 ? 's' : ''}`;
  if (seconds < 86400) return `${Math.floor(seconds / 3600)} heure${Math.floor(seconds / 3600) > 1 ? 's' : ''}`;
  if (seconds < 2592000) return `${Math.floor(seconds / 86400)} jour${Math.floor(seconds / 86400) > 1 ? 's' : ''}`;
  
  return `${Math.floor(seconds / 2592000)} mois`;
}

// ============================================================================
// SORTING HELPERS
// ============================================================================

/**
 * Sort resources by different criteria
 */
export function sortResources<T extends IResource>(
  resources: T[],
  sortBy: 'createdAt' | 'updatedAt' | 'viewCount' | 'title' = 'createdAt',
  order: 'asc' | 'desc' = 'desc'
): T[] {
  const sorted = [...resources];
  
  sorted.sort((a, b) => {
    let aValue: any;
    let bValue: any;

    switch (sortBy) {
      case 'createdAt':
        aValue = new Date(a.createdAt).getTime();
        bValue = new Date(b.createdAt).getTime();
        break;
      case 'updatedAt':
        aValue = new Date(a.updatedAt).getTime();
        bValue = new Date(b.updatedAt).getTime();
        break;
      case 'viewCount':
        aValue = a.viewCount;
        bValue = b.viewCount;
        break;
      case 'title':
        aValue = a.title.toLowerCase();
        bValue = b.title.toLowerCase();
        break;
    }

    if (order === 'asc') {
      return aValue > bValue ? 1 : -1;
    } else {
      return aValue < bValue ? 1 : -1;
    }
  });

  return sorted;
}

/**
 * Filter resources by multiple criteria
 */
export function filterResources<T extends IResource>(
  resources: T[],
  criteria: Partial<Record<keyof IResource, any>>
): T[] {
  return resources.filter(resource => {
    return Object.entries(criteria).every(([key, value]) => {
      if (value === null || value === undefined) return true;
      return (resource as any)[key] === value;
    });
  });
}

// ============================================================================
// PAGINATION HELPERS
// ============================================================================

/**
 * Helper to paginate array
 */
export function paginateArray<T>(
  items: T[],
  page: number = 1,
  pageSize: number = 20
): { items: T[]; total: number; page: number; totalPages: number } {
  const total = items.length;
  const totalPages = Math.ceil(total / pageSize);
  const start = (page - 1) * pageSize;
  const end = start + pageSize;

  return {
    items: items.slice(start, end),
    total,
    page,
    totalPages
  };
}

// ============================================================================
// SEARCH HELPERS
// ============================================================================

/**
 * Search resources by title and description
 */
export function searchResources<T extends IResource>(
  resources: T[],
  query: string
): T[] {
  const lowerQuery = query.toLowerCase();
  return resources.filter(resource =>
    resource.title.toLowerCase().includes(lowerQuery) ||
    (resource.description?.toLowerCase().includes(lowerQuery) ?? false)
  );
}

/**
 * Search forum topics
 */
export function searchForumTopics<T extends IForumTopic>(
  topics: T[],
  query: string
): T[] {
  const lowerQuery = query.toLowerCase();
  return topics.filter(topic =>
    topic.title.toLowerCase().includes(lowerQuery) ||
    topic.content.toLowerCase().includes(lowerQuery)
  );
}

// ============================================================================
// EXPORT ALL
// ============================================================================

export default {
  // Validation
  isValidResourceStatus,
  isValidUserStatus,
  isValidEntityStatus,
  isValidRating,
  isValidProgress,

  // Type Guards
  isUser,
  isResource,
  isForumTopic,
  isPaginatedResponse,

  // Builders
  createResourceDto,
  buildResourceFilter,
  buildForumTopicFilter,

  // Mapping
  getUserDisplayName,
  getResourceStatusColor,
  formatResourceYear,
  formatRating,
  getStars,
  formatDate,
  formatDateTime,
  getTimeAgo,

  // Sorting
  sortResources,
  filterResources,

  // Pagination
  paginateArray,

  // Search
  searchResources,
  searchForumTopics
};
