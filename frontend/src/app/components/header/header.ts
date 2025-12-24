import {  Component, inject, OnInit, signal } from '@angular/core';
import { ZardButtonComponent } from '@shared/components/button/button.component';
import { ZardIconComponent } from '@shared/components/icon/icon.component';
import { ThemeModeService, EThemeModes } from 'src/app/services/theme';
import { RouterLink, RouterLinkActive } from "@angular/router";
import { ZardDropdownDirective } from "@shared/components/dropdown/dropdown-trigger.directive";
import { ZardDropdownMenuItemComponent } from "@shared/components/dropdown/dropdown-item.component";
import { ZardDropdownMenuContentComponent } from '@shared/components/dropdown/dropdown-menu-content.component';
import { ZardIcon } from '@shared/components/icon/icons';



@Component({
  selector: 'app-header',
  imports: [ZardButtonComponent, ZardIconComponent, ZardDropdownDirective, ZardDropdownMenuItemComponent, RouterLink, RouterLinkActive, ZardDropdownDirective, ZardDropdownMenuContentComponent],
  templateUrl: './header.html',
  styleUrl: './header.css',
})
export class Header implements OnInit {

  themeModeService = inject(ThemeModeService);

  currentTheme = signal<ZardIcon>("settings");

  ngOnInit(): void {

    const theme = this.themeModeService.getCurrentTheme();
    console.log(theme);
    
    if (theme === EThemeModes.LIGHT) {
      this.currentTheme.set("sun");
    } else if (theme === EThemeModes.DARK) {
      this.currentTheme.set("moon");
    } else {
      this.currentTheme.set("settings");
    }

  }



  isCurrentRoute(): string | string[] {
    throw new Error('Method not implemented.');
  }


  setLightMode(): void {
    this.themeModeService.activateTheme(EThemeModes.LIGHT);
    this.currentTheme.set("sun");
  }

  setDarkMode(): void {
    this.themeModeService.activateTheme(EThemeModes.DARK);
    this.currentTheme.set("moon");
  }

  setSystemMode(): void {
    this.themeModeService.activateTheme(EThemeModes.SYSTEM);
    this.currentTheme.set("settings");
  }

  isCurrentTheme(theme: string): boolean {
    const currentTheme = this.themeModeService.getCurrentTheme();
    return currentTheme === theme;
  }

}
