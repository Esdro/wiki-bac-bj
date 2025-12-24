import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ZardButtonComponent } from '@shared/components/button/button.component';
import { ZardEmptyComponent } from "@shared/components/empty/empty.component";

@Component({
  selector: 'app-not-found',
  imports: [ZardEmptyComponent, RouterLink, ZardButtonComponent],
  templateUrl: './not-found.html',
  styleUrl: './not-found.css',
})
export class NotFound {


}
