import { Component, ViewEncapsulation } from '@angular/core';

@Component({
  selector: 'els-root',
  standalone: true,
  imports: [],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss',
  encapsulation: ViewEncapsulation.Emulated
})

export class AppComponent {
  title = 'settings-app';
}
