import { HttpClient } from '@angular/common/http';
import { Component, inject, signal, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ServerResponseData } from './interfaces/server-response-data';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet],
  templateUrl: './app.html',
  styleUrls: ['./app.css']
})
export class App implements OnInit {
  protected readonly title = signal<string>('wikibac Frontend');
  protected readonly data = signal<any>(null);

  private readonly http = inject(HttpClient);

  ngOnInit(): void {
    this.http.get<ServerResponseData>('http://localhost:8998').subscribe(serverResponse => {
      // process the configuration.
      console.log(serverResponse.data);
      this.title.set('wikibac Frontend - ' + serverResponse.message);
      this.data.set(serverResponse.data);
    });
  }
}
