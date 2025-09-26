import { bootstrapApplication } from '@angular/platform-browser';
import { AppComponent } from './app/app.component';
import { appConfig } from './app/app.config';

function waitForElement(selector: string, timeout = 3000): Promise<Element> {
  return new Promise((resolve, reject) => {
    const start = performance.now();
    const check = () => {
      const el = document.querySelector(selector);
      if (el) return resolve(el);
      if (performance.now() - start > timeout) return reject(`Element ${selector} not found`);
      requestAnimationFrame(check);
    };
    check();
  });
}

waitForElement('els-root')
  .then(() => bootstrapApplication(AppComponent, appConfig))
  .catch(err => console.error('Bootstrap failed:', err));
