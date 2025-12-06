/**
 * Generated TypeScript Interfaces for Wiki-BAC-BJ Backend Entities
 * These interfaces provide complete type safety for API interactions
 */

// ============================================================================
// BASE TYPES & ENUMS
// ============================================================================

export type EntityStatus = 'active' | 'inactive' | 'draft' | 'published' | 'archived';
export type UserStatus = 'active' | 'inactive' | 'banned' | 'pending';
export type ResourceStatus = 'draft' | 'published' | 'archived' | 'pending_review';
export type SolutionType = 'text' | 'image' | 'video' | 'document';

// ============================================================================
// ROLE INTERFACE
// ============================================================================

export interface IRole {
  id: string; // UUID v7
  name: string;
  permissions?: Record<string, boolean> | null;
  createdAt: string; // ISO 8601 datetime
  users?: IUser[];
}

export interface ICreateRoleDto {
  name: string;
  permissions?: Record<string, boolean>;
}

export interface IUpdateRoleDto {
  name?: string;
  permissions?: Record<string, boolean>;
}

// ============================================================================
// USER INTERFACE
// ============================================================================

export interface IUser {
  id: string; // UUID v7
  email: string;
  username: string;
  fullName?: string | null;
  role?: IRole | null;
  avatarUrl?: string | null;
  bio?: string | null;
  status: UserStatus;
  createdAt: string; // ISO 8601 datetime
  lastLogin?: string | null; // ISO 8601 datetime
  resources?: IResource[];
  ratings?: IResourceRating[];
  progressEntries?: IUserProgress[];
  practiceSessions?: IPracticeSession[];
  forumTopics?: IForumTopic[];
  forumPosts?: IForumPost[];
}

export interface ICreateUserDto {
  email: string;
  username: string;
  password: string;
  fullName?: string;
  avatarUrl?: string;
  bio?: string;
  status?: UserStatus;
  roleId?: string;
}

export interface IUpdateUserDto {
  email?: string;
  username?: string;
  fullName?: string;
  avatarUrl?: string;
  bio?: string;
  status?: UserStatus;
  roleId?: string;
}

// ============================================================================
// RESOURCE TYPE INTERFACE
// ============================================================================

export interface IResourceType {
  id: string; // UUID v7
  name: string;
  slug?: string | null;
  description?: string | null;
  createdAt?: string; // ISO 8601 datetime
  resources?: IResource[];
}

export interface ICreateResourceTypeDto {
  name: string;
  slug?: string;
  description?: string;
}

export interface IUpdateResourceTypeDto {
  name?: string;
  slug?: string;
  description?: string;
}

// ============================================================================
// TAG INTERFACE
// ============================================================================

export interface ITag {
  id: string; // UUID v7
  name: string;
  slug?: string | null;
  resourceTags?: IResourceTag[];
}

export interface ICreateTagDto {
  name: string;
  slug?: string;
}

export interface IUpdateTagDto {
  name?: string;
  slug?: string;
}

// ============================================================================
// SUBJECT INTERFACE
// ============================================================================

export interface ISubject {
  id: string; // UUID v7
  name: string;
  code: string;
  slug?: string | null;
  icon?: string | null;
  chapters?: IChapter[];
  seriesSubjects?: ISeriesSubject[];
  resources?: IResource[];
  practiceSessions?: IPracticeSession[];
}

export interface ICreateSubjectDto {
  name: string;
  code: string;
  slug?: string;
  icon?: string;
}

export interface IUpdateSubjectDto {
  name?: string;
  code?: string;
  slug?: string;
  icon?: string;
}

// ============================================================================
// CHAPTER INTERFACE
// ============================================================================

export interface IChapter {
  id: string; // UUID v7
  title: string;
  slug?: string | null;
  subject: ISubject;
  orderNum?: number | null;
  description?: string | null;
  resources?: IResource[];
  progressEntries?: IUserProgress[];
}

export interface ICreateChapterDto {
  title: string;
  subjectId: string;
  slug?: string;
  orderNum?: number;
  description?: string;
}

export interface IUpdateChapterDto {
  title?: string;
  subjectId?: string;
  slug?: string;
  orderNum?: number;
  description?: string;
}

// ============================================================================
// SERIES INTERFACE
// ============================================================================

export interface ISeries {
  id: string; // UUID v7
  name: string;
  code: string;
  slug?: string | null;
  description?: string | null;
  seriesSubjects?: ISeriesSubject[];
  resources?: IResource[];
}

export interface ICreateSeriesDto {
  name: string;
  code: string;
  slug?: string;
  description?: string;
}

export interface IUpdateSeriesDto {
  name?: string;
  code?: string;
  slug?: string;
  description?: string;
}

// ============================================================================
// SERIES SUBJECT INTERFACE
// ============================================================================

export interface ISeriesSubject {
  id: string; // UUID v7
  series: ISeries;
  subject: ISubject;
  isCompulsory: boolean;
  orderNum?: number | null;
}

export interface ICreateSeriesSubjectDto {
  seriesId: string;
  subjectId: string;
  isCompulsory: boolean;
  orderNum?: number;
}

// ============================================================================
// RESOURCE INTERFACE
// ============================================================================

export interface IResource {
  id: string; // UUID v7
  title: string;
  slug?: string | null;
  description?: string | null;
  type: IResourceType;
  subject?: ISubject | null;
  chapter?: IChapter | null;
  series?: ISeries | null;
  user: IUser;
  year?: number | null;
  fileUrl?: string | null;
  thumbnailUrl?: string | null;
  status: ResourceStatus;
  viewCount: number;
  downloadCount: number;
  averageRating?: number | null;
  ratingCount?: number | null;
  createdAt: string; // ISO 8601 datetime
  updatedAt: string; // ISO 8601 datetime
  ratings?: IResourceRating[];
  resourceTags?: IResourceTag[];
  exercise?: IExercise | null;
  solution?: ISolution | null;
  examPaper?: IExamPaper | null;
  revisionSheet?: IRevisionSheet | null;
}

export interface ICreateResourceDto {
  title: string;
  slug?: string;
  description?: string;
  typeId: string;
  subjectId?: string;
  chapterId?: string;
  seriesId?: string;
  year?: number;
  fileUrl?: string;
  thumbnailUrl?: string;
  status?: ResourceStatus;
}

export interface IUpdateResourceDto {
  title?: string;
  slug?: string;
  description?: string;
  typeId?: string;
  subjectId?: string;
  chapterId?: string;
  seriesId?: string;
  year?: number;
  fileUrl?: string;
  thumbnailUrl?: string;
  status?: ResourceStatus;
}

// ============================================================================
// RESOURCE RATING INTERFACE
// ============================================================================

export interface IResourceRating {
  id: string; // UUID v7
  resource: IResource;
  user: IUser;
  rating: number; // 1-5
  review?: string | null;
  createdAt: string; // ISO 8601 datetime
  updatedAt: string; // ISO 8601 datetime
}

export interface ICreateResourceRatingDto {
  resourceId: string;
  rating: number;
  review?: string;
}

export interface IUpdateResourceRatingDto {
  rating?: number;
  review?: string;
}

// ============================================================================
// RESOURCE TAG INTERFACE
// ============================================================================

export interface IResourceTag {
  id: string; // UUID v7
  resource: IResource;
  tag: ITag;
}

export interface ICreateResourceTagDto {
  resourceId: string;
  tagId: string;
}

// ============================================================================
// EXERCISE INTERFACE
// ============================================================================

export interface IExercise {
  id: string; // UUID v7
  resource: IResource;
  question: string;
  answer?: string | null;
  difficultyLevel?: number | null; // 1-5
  solution?: ISolution;
}

export interface ICreateExerciseDto {
  resourceId: string;
  question: string;
  answer?: string;
  difficultyLevel?: number;
}

export interface IUpdateExerciseDto {
  question?: string;
  answer?: string;
  difficultyLevel?: number;
}

// ============================================================================
// SOLUTION INTERFACE
// ============================================================================

export interface ISolution {
  id: string; // UUID v7
  exercise: IExercise;
  solutionType: SolutionType;
  content: string;
  explanation?: string | null;
  createdAt: string; // ISO 8601 datetime
  updatedAt: string; // ISO 8601 datetime
}

export interface ICreateSolutionDto {
  exerciseId: string;
  solutionType: SolutionType;
  content: string;
  explanation?: string;
}

export interface IUpdateSolutionDto {
  solutionType?: SolutionType;
  content?: string;
  explanation?: string;
}

// ============================================================================
// EXAM PAPER INTERFACE
// ============================================================================

export interface IExamPaper {
  id: string; // UUID v7
  resource: IResource;
  examSession: string; // e.g., "BAC 2024"
  subject: ISubject;
  series: ISeries;
  duration?: number | null; // in minutes
  totalPoints?: number | null;
  examDate?: string | null; // ISO 8601 date
  questions?: string | null; // JSON array of questions
}

export interface ICreateExamPaperDto {
  resourceId: string;
  examSession: string;
  subjectId: string;
  seriesId: string;
  duration?: number;
  totalPoints?: number;
  examDate?: string;
  questions?: string;
}

export interface IUpdateExamPaperDto {
  examSession?: string;
  subjectId?: string;
  seriesId?: string;
  duration?: number;
  totalPoints?: number;
  examDate?: string;
  questions?: string;
}

// ============================================================================
// REVISION SHEET INTERFACE
// ============================================================================

export interface IRevisionSheet {
  id: string; // UUID v7
  resource: IResource;
  chapter: IChapter;
  keyPoints?: string | null; // JSON array
  formulasAndDefinitions?: string | null;
  commonMistakes?: string | null; // JSON array
  tips?: string | null; // JSON array
}

export interface ICreateRevisionSheetDto {
  resourceId: string;
  chapterId: string;
  keyPoints?: string;
  formulasAndDefinitions?: string;
  commonMistakes?: string;
  tips?: string;
}

export interface IUpdateRevisionSheetDto {
  chapterId?: string;
  keyPoints?: string;
  formulasAndDefinitions?: string;
  commonMistakes?: string;
  tips?: string;
}

// ============================================================================
// PRACTICE SESSION INTERFACE
// ============================================================================

export interface IPracticeSession {
  id: string; // UUID v7
  user: IUser;
  subject: ISubject;
  questionsCount: number;
  completedCount: number;
  correctCount: number;
  score?: number | null;
  status: 'in_progress' | 'completed' | 'abandoned';
  startedAt: string; // ISO 8601 datetime
  completedAt?: string | null; // ISO 8601 datetime
  duration?: number | null; // in minutes
}

export interface ICreatePracticeSessionDto {
  subjectId: string;
  questionsCount: number;
}

export interface IUpdatePracticeSessionDto {
  completedCount?: number;
  correctCount?: number;
  score?: number;
  status?: 'in_progress' | 'completed' | 'abandoned';
  completedAt?: string;
}

// ============================================================================
// USER PROGRESS INTERFACE
// ============================================================================

export interface IUserProgress {
  id: string; // UUID v7
  user: IUser;
  chapter: IChapter;
  progressPercentage: number; // 0-100
  completedAt?: string | null; // ISO 8601 datetime
  notes?: string | null;
  updatedAt: string; // ISO 8601 datetime
}

export interface ICreateUserProgressDto {
  userId: string;
  chapterId: string;
  progressPercentage: number;
  completedAt?: string;
  notes?: string;
}

export interface IUpdateUserProgressDto {
  progressPercentage?: number;
  completedAt?: string;
  notes?: string;
}

// ============================================================================
// FORUM CATEGORY INTERFACE
// ============================================================================

export interface IForumCategory {
  id: string; // UUID v7
  name: string;
  slug?: string | null;
  description?: string | null;
  orderNum?: number | null;
  topics?: IForumTopic[];
}

export interface ICreateForumCategoryDto {
  name: string;
  slug?: string;
  description?: string;
  orderNum?: number;
}

export interface IUpdateForumCategoryDto {
  name?: string;
  slug?: string;
  description?: string;
  orderNum?: number;
}

// ============================================================================
// FORUM TOPIC INTERFACE
// ============================================================================

export interface IForumTopic {
  id: string; // UUID v7
  category: IForumCategory;
  user: IUser;
  title: string;
  content: string;
  viewCount: number;
  isPinned: boolean;
  isLocked: boolean;
  createdAt: string; // ISO 8601 datetime
  updatedAt: string; // ISO 8601 datetime
  posts?: IForumPost[];
  lastPost?: IForumPost | null;
}

export interface ICreateForumTopicDto {
  categoryId: string;
  title: string;
  content: string;
  isPinned?: boolean;
  isLocked?: boolean;
}

export interface IUpdateForumTopicDto {
  title?: string;
  content?: string;
  isPinned?: boolean;
  isLocked?: boolean;
}

// ============================================================================
// FORUM POST INTERFACE
// ============================================================================

export interface IForumPost {
  id: string; // UUID v7
  topic: IForumTopic;
  user: IUser;
  content: string;
  isSolution: boolean;
  createdAt: string; // ISO 8601 datetime
  updatedAt: string; // ISO 8601 datetime
}

export interface ICreateForumPostDto {
  topicId: string;
  content: string;
  isSolution?: boolean;
}

export interface IUpdateForumPostDto {
  content?: string;
  isSolution?: boolean;
}

// ============================================================================
// API RESPONSE WRAPPER INTERFACES
// ============================================================================

export interface IPaginatedResponse<T> {
  data: T[];
  meta: {
    total: number;
    page: number;
    pageSize: number;
    totalPages: number;
  };
}

export interface IApiResponse<T> {
  data: T;
  message?: string;
  status: number;
}

export interface IApiErrorResponse {
  error: string;
  message: string;
  status: number;
  details?: Record<string, any>;
}

// ============================================================================
// FILTER & QUERY INTERFACES
// ============================================================================

export interface IResourceFilter {
  typeId?: string;
  subjectId?: string;
  chapterId?: string;
  seriesId?: string;
  year?: number;
  status?: ResourceStatus;
  userId?: string;
  search?: string;
  sortBy?: 'createdAt' | 'updatedAt' | 'viewCount' | 'averageRating';
  sortOrder?: 'asc' | 'desc';
  page?: number;
  pageSize?: number;
}

export interface IForumTopicFilter {
  categoryId?: string;
  userId?: string;
  search?: string;
  isPinned?: boolean;
  isLocked?: boolean;
  sortBy?: 'createdAt' | 'updatedAt' | 'viewCount';
  sortOrder?: 'asc' | 'desc';
  page?: number;
  pageSize?: number;
}

export interface IUserProgressFilter {
  userId?: string;
  chapterId?: string;
  minProgress?: number;
  maxProgress?: number;
  completed?: boolean;
}

// ============================================================================
// AGGREGATION & STATISTICS INTERFACES
// ============================================================================

export interface IResourceStatistics {
  totalResources: number;
  totalViews: number;
  totalDownloads: number;
  averageRating: number;
  resourcesByType: Record<string, number>;
  resourcesBySubject: Record<string, number>;
  resourcesByStatus: Record<string, number>;
}

export interface IUserStatistics {
  totalUsers: number;
  activeUsers: number;
  usersByRole: Record<string, number>;
  usersByStatus: Record<string, number>;
}

export interface IForumStatistics {
  totalTopics: number;
  totalPosts: number;
  totalCategories: number;
  topicsWithLock: number;
  pinnedTopics: number;
}
