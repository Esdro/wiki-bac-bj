import { inject, Injectable, signal } from '@angular/core';
import { BACKEND_URL, LoginResponseData } from '../utils/constants';
import { HttpClient } from '@angular/common/http';
import { ApiResponse } from '@app/interfaces';



@Injectable({
  providedIn: 'root',
})
export class UserAuthService {

  private readonly loginUrl = BACKEND_URL + '/users/login';

  private httpClient = inject(HttpClient);

  readonly cookieName = 'token';

  currentUser = signal<LoginResponseData | null>(null);

  async login(username: string, password: string): Promise<boolean> {
    return new Promise((resolve) => {
      this.httpClient.post<ApiResponse<LoginResponseData>>(this.loginUrl, {
        username,
        password
      }).subscribe({
        next: (res) => {
          if (res.status == 200 && res.data?.token) {
            this.persistAuth(res.data);
            this.currentUser.set(res.data);
            resolve(true);
          } else {
            this.clearAuth();
            this.currentUser.set(null);
            resolve(false);
          }
        },
        error: (err) => {
          console.error('Login error:', err);
          this.clearAuth();
          this.currentUser.set(null);
          resolve(false);
        },
        complete: () => {
          console.log('Login request completed.');
        }
      });
    });
  }

  logout(): void {
    this.clearAuth();
  }

  isAuthenticated(): boolean {
    try {
      const token = this.getToken();
      // token is valid if it exists and has length >= 10
      return !!token && token.length >= 10;
    } catch (error) {
      console.error('Error checking authentication:', error);
      return false;
    }
  }


  getToken(): string | null {
    try {
      const value = document.cookie;
      const parts = value.split("; " + this.cookieName + "=");
      console.log(parts && parts.length == 2 ? "Token found in cookies." : "Token not found in cookies.");
      
      if (parts && parts.length == 2) {
        const tokenPart = parts.pop();
        return tokenPart ? tokenPart.split(";").shift()! : null;
      } else {
        return null;
      }
    } catch (error) {
      console.error('Error reading token from cookie:', error);
      return null;
    }
  }

  checkAuth(): void {
    try {
      const token = this.getToken();
      if (token && token.length >= 10) {
        // Hydrate currentUser depuis le storage si l'app a été rechargée
        if (!this.currentUser()) {
          const storedUser = this.getUserFromStorage();
          if (storedUser) {
            this.currentUser.set({ token, user: storedUser });
          }
        }
        return;
      }
      // Pas de token valide -> on nettoie
      this.clearAuth();
    } catch (error) {
      console.error('Error in checkAuth:', error);
      this.clearAuth();
    }
  }

  getCurrentUser(): LoginResponseData | null {
    try {
      return this.isAuthenticated() ? this.currentUser() : null;
    } catch (error) {
      console.error('Error getting current user:', error);
      return null;
    }
  }

  private persistAuth(data: LoginResponseData): void {
    try {
      const expires = new Date(Date.now() + 3600 * 1000).toUTCString();
      document.cookie = `token=${data.token}; path=/; expires=${expires}`;
      localStorage.setItem('userEmail', data.user.email);
      localStorage.setItem('userId', data.user.id);
      localStorage.setItem('userName', data.user.username);
    } catch (error) {
      console.error('Error persisting authentication:', error);
    }
  }

  /**
   * Retrieves the cached user data from browser local storage.
   * @returns The user object containing id, email, and username if all values exist in storage, otherwise null.
   * @throws Catches and logs any errors that occur while reading from localStorage, returning null in such cases.
   */
  private getUserFromStorage(): LoginResponseData['user'] | null {
    try {
      const id = localStorage.getItem('userId');
      const email = localStorage.getItem('userEmail');
      const username = localStorage.getItem('userName');
      if (id && email && username) {
        return { id, email, username } as LoginResponseData['user'];
      }
      return null;
    } catch (error) {
      console.error('Error reading user from storage:', error);
      return null;
    }
  }

  private clearAuth(): void {
    try {
      document.cookie = `token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
      localStorage.removeItem('userEmail');
      localStorage.removeItem('userId');
      localStorage.removeItem('userName');
      this.currentUser.set(null);
    } catch (error) {
      console.error('Error clearing authentication:', error);
      this.currentUser.set(null);
    }
  }

}
