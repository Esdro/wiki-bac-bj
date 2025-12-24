import { Component, inject, OnInit, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ZardButtonComponent } from '@shared/components/button/button.component';
import { ZardCardComponent } from '@shared/components/card/card.component';
import { ZardFormModule } from '@shared/components/form/form.module';
import { ZardInputDirective } from '@shared/components/input/input.directive';
import { ZardLoaderComponent } from '@shared/components/loader/loader.component';
import { NotifService } from 'src/app/services/notif';
import { UserAuthService } from 'src/app/services/user-auth';

@Component({
  selector: 'app-auth',
  imports: [ReactiveFormsModule, ZardInputDirective, ZardButtonComponent, ZardFormModule, ZardCardComponent, ZardLoaderComponent],
  templateUrl: './auth.html',
  styleUrl: './auth.css',
})
export class Auth implements OnInit {


  private readonly userAuthService = inject(UserAuthService);
  private router: Router = inject(Router);
  private notifService = inject(NotifService);

  formIsLoading = signal<boolean>(false);


  profileForm = new FormGroup({
    username: new FormControl('', [Validators.required, Validators.minLength(3), Validators.maxLength(20)]),
    password: new FormControl('', [Validators.required, Validators.minLength(6), Validators.pattern(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/)]),
  });

  ngOnInit(): void {
    if (this.userAuthService.isAuthenticated()) {
      this.notifService.showToast({
        title: 'Already Logged In',
        description: 'You are already logged in.',
        accent: 'info'
      });
      this.router.navigate(['/user/profile']);
    }
  }

  onSubmit() {
    if (this.profileForm.valid) {
      const { username, password } = this.profileForm.value;
      this.formIsLoading.set(true);
      this.userAuthService.login(username!, password!).then(success => {
        if (success) {
          this.formIsLoading.set(false);
          this.notifService.showToast({
            title: 'Login Successful',
            description: `Welcome back, ${username}!`,
            accent: 'success'
          });
          this.router.navigate(['/user/profile']);
        } else {
          this.notifService.showToast({
            title: 'Login Failed',
            description: 'Please check your credentials and try again.',
            accent: 'error'
          });

        }
      }).finally(() => {
        this.formIsLoading.set(false);
      });

    }
  }



}
