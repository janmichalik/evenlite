import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { HomeModule } from "./home/home.module";
import { TestComponent } from "../../tests/test/test.component";
import { WordpressService } from './services/api/wordpress.service';
import { WordpressRootResponse, WpUser } from './models/wordpress.types';
import { CommonModule, JsonPipe } from '@angular/common';

@Component({
  selector: 'els-root',
  standalone: true,
  imports: [RouterModule, HomeModule, TestComponent, JsonPipe, CommonModule],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  wpResponse: WordpressRootResponse | null = null;
  users: any[] = [];

  constructor(private wp: WordpressService) {
    wp.pingWordpress().then(response => {
      this.wpResponse = response;
    });
  }

  async ngOnInit() {
    try {
      const users = await this.wp.getUsersPromise();
      console.log('Użytkownicy:', users);
      this.users = users;
    } catch (err) {
      console.error('Błąd pobierania użytkowników:', err);
    }
  }

  getRolesString(user: WpUser): string {
    return (user.roles ?? []).join(', ');
  }

}
