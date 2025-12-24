export   const BACKEND_URL = "http://localhost:8998/api";

export interface LoginResponseData {
    token: string;
    user: {
        id: string;
        username: string;
        email: string;
    };
}

export interface ToastAction {
  label: string;
  onClick: () => void;
}

export interface ToastOptions {
  title: string;
  description?: string;
  action?: ToastAction;
  accent: 'info' | 'success' | 'error';
  position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left';
}

export interface LoginData {
    username: string;
    password: string;
}