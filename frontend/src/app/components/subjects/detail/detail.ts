import { HttpClient } from '@angular/common/http';
import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ISubject, ServerResponseData } from '@app/interfaces';
import { BACKEND_URL } from 'src/app/utils/constants';
import { ZardLoaderComponent } from "@shared/components/loader/loader.component";
import { ZardCardComponent } from "@shared/components/card/card.component";

@Component({
  selector: 'app-detail',
  imports: [ZardLoaderComponent, ZardCardComponent],
  templateUrl: './detail.html',
  styleUrl: './detail.css',
})
export class Detail implements OnInit {

  private subjectSlug = signal<string | null>(null);
  private activatedRoute = inject(ActivatedRoute);
  readonly backendUrl = BACKEND_URL;
  httpClient = inject(HttpClient);

  subject = signal<ISubject | any>(null);

  ngOnInit(): void {
    this.activatedRoute.paramMap.subscribe(params => {
      console.log(params);
      
      const slugParam = params.get('slug');
      this.subjectSlug.set(slugParam ? slugParam : null);
      console.log('Subject Slug:', this.subjectSlug());
    });

    if (this.subjectSlug() !== null) {
      // Fetch subject details based on subjectSlug
      // For demonstration, we'll just log the subjectSlug
      console.log(`Fetching details for subject Slug: ${this.subjectSlug()}`);
      // Here you would typically make an HTTP request to fetch the subject details

      this.httpClient.get<ServerResponseData<ISubject>>(`${this.backendUrl}/subjects/${this.subjectSlug()}`).subscribe({
        next: (data) => {
          console.log(data.data);
          
          this.subject.set(data.data);
          console.log('Fetched Subject Details:', data);
        },
        error: (error) => {
          console.error('Error fetching subject details:', error);
        },
        complete: () => {
          console.log('Fetch subject details request completed.');
        }
      });
    }

  }
}
