import { HttpClient } from '@angular/common/http';
import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { ZardCardComponent } from '@shared/components/card/card.component';
import { generateId } from '@shared/utils/merge-classes';
import { filterResources, formatDateTime, getTimeAgo, IResource, ISubject, IUser, ServerResponseData } from '@app/interfaces';
import { RouterModule } from '@angular/router';
import { ZardCarouselModule } from '@shared/components/carousel/carousel.module';
import { ZardLoaderComponent } from '@shared/components/loader/loader.component';
import { BACKEND_URL } from 'src/app/utils/constants';
import { Subjects } from '../subjects/subjects';

@Component({
  selector: 'app-home',
  imports: [ZardCardComponent, RouterModule, ZardCarouselModule, ZardLoaderComponent, Subjects],
  templateUrl: './home.html',
  styleUrl: './home.css'
})
export class Home implements OnInit {


  protected readonly title = signal<string>('wikibac Frontend');
  protected readonly data = signal<ISubject[]>([]);

  private readonly http = inject(HttpClient);

  readonly backendUrl = BACKEND_URL;

  ngOnInit(): void {
    this.http.get<ServerResponseData<ISubject[]>>(`${this.backendUrl}/subjects`).subscribe(serverResponse => {
      this.title.set('wikibac Frontend - ' + serverResponse.message);
      this.data.set(serverResponse.data ?? []);
    });

    // this.http.get<ServerResponseData<IUser[]>>(`${this.backendUrl}/users`).subscribe(serverResponse => {
    //   console.log('Users:');
    //   console.log(serverResponse);
    //   serverResponse.data?.forEach((element: IUser) => {
    //     console.log(`- ${element.id}: ${element.username} (${element.email}), créé le ${formatDateTime(element.createdAt)} (il y a ${getTimeAgo(element.createdAt)})`);
    //   });
    // });
    // this.http.get<ServerResponseData<IResource[]>>(`${this.backendUrl}/resources`).subscribe(serverResponse => {
    //   console.log('Resources:');
    //   console.log(serverResponse.data);
    //   let newData = serverResponse.data ?? [];
    //   if (serverResponse.data) {
    //     newData = filterResources(serverResponse.data, { id: "019af55c-8a29-79a1-997d-5e862792f821" });
    //   }
    //   console.log(newData);
    // });
  }
}

