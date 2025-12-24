import { HttpClient } from '@angular/common/http';
import { Component, inject, Input, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ISubject, ServerResponseData } from '@app/interfaces';
import { ZardButtonComponent } from '@shared/components/button/button.component';
import { ZardCardComponent } from '@shared/components/card/card.component';
import { ZardLoaderComponent } from '@shared/components/loader/loader.component';
import { BACKEND_URL } from 'src/app/utils/constants';

@Component({
  selector: 'app-subjects',
  imports: [ZardCardComponent, ZardLoaderComponent, RouterLink, ZardButtonComponent],
  templateUrl: './subjects.html',
  styleUrl: './subjects.css',
})
export class Subjects implements OnInit {

  @Input() subjects = signal<ISubject[] | null>(null);

  protected readonly data = signal<ISubject[]>([]);

  private readonly http = inject(HttpClient);

  readonly backendUrl = BACKEND_URL;

  ngOnInit(): void {

    if (this.subjects() !== null) {
      this.data.set(this.subjects()!);
      console.log("J'ai des données donc je n'ai pas besoin de fetch les matières depuis le component Subject");
    } else {
      console.log("Aucune donnée donc je fetch les matières depuis le component Subject");
      
      this.http.get<ServerResponseData<ISubject[]>>(`${this.backendUrl}/subjects`).subscribe(serverResponse => {
        // console.log(serverResponse);
        this.data.set(serverResponse.data ?? []);
      });
    }
  }

}
