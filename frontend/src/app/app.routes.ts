import { Routes } from '@angular/router';
import { Home } from './components/home/home';
import { Auth } from './components/user/auth/auth';
import { Profile } from './components/user/profile/profile';
import { authGuard } from './guards/auth-guard';
import { Subjects } from './components/subjects/subjects';
import { Detail } from './components/subjects/detail/detail';

export const routes: Routes = [
    {
        path: '',
        component: Home,
        title: 'Wiki-Bac-BJ - Home',

    },
    {
        path: 'subjects',
        component: Subjects,
        title: 'Wiki-Bac-BJ - Subjects',
    },
    {
        path: 'subjects/:slug',
        component: Detail,
        title: 'Wiki-Bac-BJ - Detail',
    },
    {
        path: 'user/auth',
        component: Auth,
        title: 'Wiki-Bac-BJ - Authentication',
        canActivate: [authGuard]
    },
    {
        path: 'user/profile',
        component: Profile,
        title: 'Wiki-Bac-BJ - Profile',
        // canActivate: [authGuard]
    },
    {
        path: '**',
        loadComponent: () => import('./components/not-found/not-found').then(m => m.NotFound),
        title: 'Wiki-Bac-BJ - Not Found'
    }
];
