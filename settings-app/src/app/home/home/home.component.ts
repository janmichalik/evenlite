import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {MatTabsModule} from '@angular/material/tabs';

@Component({
  standalone: false,
  selector: 'els-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss'],
})
export class HomeComponent {
  private readonly API_URL: string = "http://localhost/evenlite/wp-json/wp/v2/posts";

  hello = '';

  constructor(private http: HttpClient) {}

  ngOnInit() {
  }

}
