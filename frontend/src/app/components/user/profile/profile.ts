import { Component, computed, inject, OnDestroy, OnInit, signal } from '@angular/core';
import {  ActivatedRoute, Router, RouterLink } from '@angular/router';
import { UserAuthService } from 'src/app/services/user-auth';
import { ZardCardComponent } from "@shared/components/card/card.component";
import { ZardButtonComponent } from '@shared/components/button/button.component';
import { NotifService } from 'src/app/services/notif';
import { LoginResponseData } from 'src/app/utils/constants';

@Component({
  selector: 'app-profile',
  imports: [ZardCardComponent, ZardButtonComponent],
  templateUrl: './profile.html',
  styleUrl: './profile.css',
})
export class Profile implements OnInit, OnDestroy {

  private router: Router = inject(Router);
  private readonly userAuthService = inject(UserAuthService);
  private notifService = inject(NotifService);
  currentUser = signal<LoginResponseData | null>(null);
  private activatedRoute = inject(ActivatedRoute);

  ngOnInit(): void {
    console.log('Profile component initialized.');
    if (!this.userAuthService.isAuthenticated() ) {
      console.log('User is not authenticated. Redirecting to login page...');
      this.notifService.showToast({
        title: 'Authentication Required',
        description: 'Please log in to access your profile.',
        accent: 'error'
      });
      this.router.navigate(['/user/auth']);
    }else {

    this.currentUser.set(this.userAuthService.currentUser());
    console.log('Current User:', this.currentUser());
    }

    this.activatedRoute.paramMap.subscribe(params => {
      console.log(params);
      
    });

  }

  logout(): void {
    this.userAuthService.logout();
    this.notifService.showToast({
      title: 'Logged Out',
      description: 'You have been successfully logged out.',
      accent: 'info',
      position: 'top-left'
    });
    this.router.navigate(['/user/auth']);
  }


  ngOnDestroy(): void {
    console.log('Profile component destroyed.');
    // console.clear();
  }

}
