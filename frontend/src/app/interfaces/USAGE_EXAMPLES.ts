/**
 * Example usage guide for entity interfaces and type-safe API calls
 * 
 * This file demonstrates best practices for using the generated interfaces
 * in your Angular services and components.
 */

// ============================================================================
// EXAMPLE 1: TYPED SERVICE WITH COMPLETE TYPE SAFETY
// ============================================================================

/*
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import {
  IResource,
  ICreateResourceDto,
  IUpdateResourceDto,
  IPaginatedResponse,
  IApiResponse,
  IResourceFilter
} from '@app/interfaces';

@Injectable({
  providedIn: 'root'
})
export class ResourceService {
  private apiUrl = 'http://localhost:8000/api/resources';

  constructor(private http: HttpClient) {}

  // Get all resources with pagination
  getResources(filter?: IResourceFilter): Observable<IPaginatedResponse<IResource>> {
    let params = new HttpParams();
    
    if (filter) {
      if (filter.typeId) params = params.set('typeId', filter.typeId);
      if (filter.subjectId) params = params.set('subjectId', filter.subjectId);
      if (filter.status) params = params.set('status', filter.status);
      if (filter.page) params = params.set('page', filter.page.toString());
      if (filter.pageSize) params = params.set('pageSize', filter.pageSize.toString());
    }

    return this.http.get<IPaginatedResponse<IResource>>(this.apiUrl, { params });
  }

  // Get single resource
  getResource(id: string): Observable<IApiResponse<IResource>> {
    return this.http.get<IApiResponse<IResource>>(`${this.apiUrl}/${id}`);
  }

  // Create resource with full type checking
  createResource(dto: ICreateResourceDto): Observable<IApiResponse<IResource>> {
    return this.http.post<IApiResponse<IResource>>(this.apiUrl, dto);
  }

  // Update resource
  updateResource(id: string, dto: IUpdateResourceDto): Observable<IApiResponse<IResource>> {
    return this.http.patch<IApiResponse<IResource>>(`${this.apiUrl}/${id}`, dto);
  }

  // Delete resource
  deleteResource(id: string): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }
}
*/

// ============================================================================
// EXAMPLE 2: TYPED COMPONENT USING THE SERVICE
// ============================================================================

/*
import { Component, OnInit, signal } from '@angular/core';
import { IResource, ICreateResourceDto, IResourceFilter } from '@app/interfaces';
import { ResourceService } from './resource.service';

@Component({
  selector: 'app-resources-list',
  template: \`
    <div>
      <div *ngFor="let resource of resources()">
        <h3>{{ resource.title }}</h3>
        <p>{{ resource.description }}</p>
        <span>Views: {{ resource.viewCount }}</span>
      </div>
    </div>
  \`
})
export class ResourcesListComponent implements OnInit {
  resources = signal<IResource[]>([]);
  loading = signal(false);

  constructor(private resourceService: ResourceService) {}

  ngOnInit(): void {
    this.loadResources();
  }

  loadResources(filter?: IResourceFilter): void {
    this.loading.set(true);
    this.resourceService.getResources(filter).subscribe({
      next: (response) => {
        this.resources.set(response.data);
        this.loading.set(false);
      },
      error: (err) => {
        console.error('Error loading resources', err);
        this.loading.set(false);
      }
    });
  }

  createResource(dto: ICreateResourceDto): void {
    this.resourceService.createResource(dto).subscribe({
      next: (response) => {
        const newResource: IResource = response.data;
        this.resources.update(resources => [...resources, newResource]);
      }
    });
  }
}
*/

// ============================================================================
// EXAMPLE 3: FORM TYPING WITH DTO INTERFACES
// ============================================================================

/*
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ICreateUserDto } from '@app/interfaces';
import { UserService } from './user.service';

@Component({
  selector: 'app-create-user',
  template: \`
    <form [formGroup]="userForm" (ngSubmit)="onSubmit()">
      <input formControlName="email" type="email" placeholder="Email">
      <input formControlName="username" placeholder="Username">
      <input formControlName="password" type="password" placeholder="Password">
      <input formControlName="fullName" placeholder="Full Name">
      <button type="submit">Create User</button>
    </form>
  \`
})
export class CreateUserComponent {
  userForm: FormGroup;

  constructor(
    private fb: FormBuilder,
    private userService: UserService
  ) {
    this.userForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      username: ['', [Validators.required, Validators.minLength(3)]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      fullName: [''],
      avatarUrl: [''],
      bio: ['']
    });
  }

  onSubmit(): void {
    if (this.userForm.valid) {
      const dto: ICreateUserDto = this.userForm.value;
      this.userService.createUser(dto).subscribe({
        next: (response) => {
          console.log('User created:', response.data);
        }
      });
    }
  }
}
*/

// ============================================================================
// EXAMPLE 4: INTERCEPTOR FOR API RESPONSE HANDLING
// ============================================================================

/*
import { Injectable } from '@angular/core';
import {
  HttpInterceptor,
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpResponse
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { IApiResponse, IApiErrorResponse } from '@app/interfaces';

@Injectable()
export class ApiResponseInterceptor implements HttpInterceptor {
  intercept(
    req: HttpRequest<any>,
    next: HttpHandler
  ): Observable<HttpEvent<any>> {
    return next.handle(req).pipe(
      map(event => {
        if (event instanceof HttpResponse) {
          // The response is already typed through the generic service
          // This interceptor can log or transform the response if needed
          console.log('API Response:', event.body);
        }
        return event;
      })
    );
  }
}
*/

// ============================================================================
// EXAMPLE 5: USING SIGNALS WITH TYPED INTERFACES
// ============================================================================

/*
import { Injectable, signal, computed } from '@angular/core';
import { IUser, ICreateUserDto } from '@app/interfaces';
import { UserService } from './user.service';

@Injectable({
  providedIn: 'root'
})
export class UserStore {
  private usersSignal = signal<IUser[]>([]);
  private loadingSignal = signal(false);

  readonly users = this.usersSignal.asReadonly();
  readonly loading = this.loadingSignal.asReadonly();
  readonly userCount = computed(() => this.usersSignal().length);

  constructor(private userService: UserService) {}

  loadUsers(): void {
    this.loadingSignal.set(true);
    this.userService.getUsers().subscribe({
      next: (response) => {
        this.usersSignal.set(response.data);
        this.loadingSignal.set(false);
      }
    });
  }

  addUser(dto: ICreateUserDto): void {
    this.userService.createUser(dto).subscribe({
      next: (response) => {
        this.usersSignal.update(users => [...users, response.data]);
      }
    });
  }

  updateUser(id: string, user: IUser): void {
    this.usersSignal.update(users =>
      users.map(u => u.id === id ? user : u)
    );
  }
}
*/

// ============================================================================
// QUICK REFERENCE: IMPORT STRUCTURE
// ============================================================================

/*
// Import individual interfaces as needed
import { IUser, IResource, ICreateResourceDto } from '@app/interfaces';

// Or import everything
import * as Models from '@app/interfaces';

// In services:
getResources(): Observable<IPaginatedResponse<IResource>>

// In components:
resources: Signal<IResource[]> = signal([]);

// In forms:
const dto: ICreateResourceDto = { ... };

// Type checking at compile time!
// If you pass wrong types, TypeScript will catch it immediately
*/

export const INTERFACE_USAGE_EXAMPLES = 'See code comments above for complete examples';
