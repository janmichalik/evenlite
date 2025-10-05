import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';

@Injectable({
  providedIn: 'root'
})
export class WordpressService {
  constructor(private http: HttpClient) { }

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
