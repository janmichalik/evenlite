import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { HomeModule } from "./home/home.module";
import { TestComponent } from "../../tests/test/test.component";
import { WordpressService } from './services/api/wordpress.service';
import { WordpressRootResponse } from './models/wordpress.types';

@Component({
  selector: 'els-root',
  standalone: true,
  imports: [RouterModule, HomeModule, TestComponent],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  wpResponse: WordpressRootResponse | null = null;

  constructor(private wp: WordpressService) {
    wp.pingWordpress().then(response => {
      this.wpResponse = response;
    });
  }
}
