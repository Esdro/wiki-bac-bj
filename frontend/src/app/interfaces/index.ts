/**
 * Central export point for all application interfaces
 * Import from this file to access all entity interfaces with full type safety
 *
 * Usage:
 * import { IUser, IResource, ICreateUserDto } from '@app/interfaces';
 * import { getUserDisplayName, formatDate } from '@app/interfaces';
 */

// Entity Interfaces
export * from './entities';

// Server Response Data
export * from './server-response-data';

// Utility Functions
export * from './entity.utils';
