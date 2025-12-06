import { Component, inject, signal, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { Home } from './components/home/home';
import { DarkModeService } from './services/dark';

@Component({
  selector: 'app-root',
  imports: [Home],
  templateUrl: './app.html',
  styleUrls: ['./app.css']
})
export class App implements OnInit {

  private readonly darkmodeService = inject(DarkModeService);

  ngOnInit(): void {
    this.darkmodeService.initTheme();
  }

}
