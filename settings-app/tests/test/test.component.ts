import { Component } from '@angular/core';
import { delay, of } from 'rxjs';

@Component({
  selector: 'els-test',
  imports: [],
  templateUrl: './test.component.html',
  styleUrl: './test.component.scss',
  standalone: true
})
export class TestComponent {
  ngOnInit() {
    this.rxjsTest();
  }

  rxjsTest() {

    const source$ = of('Witaj Jan').pipe(
      delay(2000) // opóźnienie 2 sekundy
    );

    source$.subscribe(value => {
      console.log(value); // wypisze "Witaj Jan" po 2 sekundach
    });
    const source = 'Witaj Jan';

    () => {
      console.log(source)
    }
  }
}
