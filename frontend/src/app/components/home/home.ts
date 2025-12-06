import { HttpClient } from '@angular/common/http';
import { Component, inject, OnInit, signal } from '@angular/core';
import { ServerResponseData } from '../../interfaces/server-response-data';
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';
import { Card } from 'primeng/card';

@Component({
  selector: 'app-home',
  imports: [TableModule, CommonModule, Card],
  templateUrl: './home.html',
  styleUrls: ['./home.css'],
})
export class Home implements OnInit {

  protected readonly title = signal<string>('wikibac Frontend');
  data! : null | ServerResponseData['data'];

  private readonly http = inject(HttpClient);

  ngOnInit(): void {
    this.http.get<ServerResponseData>('http://localhost:8998').subscribe(serverResponse => {
      // process the configuration.
      console.log(serverResponse.data);
      this.title.set('wikibac Frontend - ' + serverResponse.message);
      this.data = serverResponse.data;
    });
  }
}
