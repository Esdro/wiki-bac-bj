import type { ResolveFn } from '@angular/router';

export const ttResolver: ResolveFn<boolean> = (route, state) => {

    localStorage.setItem('lastVisitedUrl', state.url);
      localStorage.setItem('lastVisitedTimestamp', new Date().toISOString());
      let routeUrl =  route.params['id'] ? `/${route.params['id']}` : state.url;
      if (routeUrl.startsWith('/tt/')) {
        routeUrl = routeUrl.replace('/tt/', '/');
      }
      localStorage.setItem('lastVisitedRoute', routeUrl);
  return true;
};
