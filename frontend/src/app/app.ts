import { Component, inject, OnInit } from '@angular/core';
import { ThemeModeService } from './services/theme';
import {  RouterOutlet } from "@angular/router";
import { Header } from "./components/header/header";
import { Footer } from "./components/footer/footer";
import { UserAuthService } from './services/user-auth';
import { ZardToastComponent } from '@shared/components/toast/toast.component';

@Component({
  selector: 'app-root',
  imports: [ RouterOutlet, Header, Footer, ZardToastComponent],
  templateUrl: './app.html',
  styleUrls: ['./app.css']
})
export class App implements OnInit {

  private readonly themeModeService = inject(ThemeModeService);
  private readonly userAuthService = inject(UserAuthService);

  ngOnInit(): void {
    this.themeModeService.initTheme();
    this.userAuthService.checkAuth();
    
  }

}
