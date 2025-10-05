import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '@env/environment';
import { firstValueFrom, Observable } from 'rxjs';
import { WordpressSettings, WpUser } from 'src/app/models/wordpress.types';

@Injectable({ providedIn: 'root' })
export class WordpressService {
  private nonce = (window as any).EvenliteAPI?.nonce;
  private apiUrl = (window as any).EvenliteAPI?.restUrl;

  constructor(private http: HttpClient) { }

  getSettings(): Observable<WordpressSettings> {
    return this.http.get<WordpressSettings>(`${this.apiUrl}wp/v2/settings`, {
      headers: new HttpHeaders({
        'X-WP-Nonce': this.nonce
      })
    });
  }

  getUsers(): Observable<WpUser[]> {
    return this.http.get<WpUser[]>(`${this.apiUrl}wp/v2/users`, {
      headers: new HttpHeaders({
        'X-WP-Nonce': this.nonce
      })
    });
  }

  getUsersPromise(): Promise<WpUser[]> {
    return this.http.get<WpUser[]>(`${this.apiUrl}wp/v2/users`, {
      headers: new HttpHeaders({
        'X-WP-Nonce': this.nonce
      })
    }).toPromise() as Promise<WpUser[]>;
  }

  getAcfOptions(): Observable<any> {
    return this.http.get(`${this.apiUrl}acf/v3/options/options`, {
      headers: new HttpHeaders({
        'X-WP-Nonce': this.nonce
      })
    });
  }

  pingWordpress(): Promise<any> {
    const url = environment.wordpressApiUrl;
    return this.http.get(url)
      .toPromise()
      .then(response => {
        return response;
      })
      .catch(error => {
        throw error;
      });
  }
}
