import { HttpClient } from '@angular/common/http';
import { Component, inject, OnInit, signal } from '@angular/core';
import { ServerResponseData } from '../../interfaces/server-response-data';
import { ZardCardComponent } from '@shared/components/card/card.component';
import { ZardButtonComponent } from '@shared/components/button/button.component';
import { generateId } from '@shared/utils/merge-classes';

@Component({
  selector: 'app-home',
  imports: [ZardCardComponent, ZardButtonComponent],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit {
  protected readonly idEmail = generateId('email');
  protected readonly idPassword = generateId('password');

  protected readonly title = signal<string>('wikibac Frontend');
  data!: null | ServerResponseData['data'];

  private readonly http = inject(HttpClient);

  ngOnInit(): void {
    this.http.get<ServerResponseData>('http://localhost:8998').subscribe(serverResponse => {
      // process the configuration.
      console.log(serverResponse.data);
      this.title.set('wikibac Frontend - ' + serverResponse.message);
      this.data = serverResponse.data;
    });
    this.http.get<ServerResponseData>('http://localhost:8998/api/users').subscribe(serverResponse => {
      console.log(serverResponse);
    });
  }
}

