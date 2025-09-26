Kompilacja projektu przy użyciu Webpacka

Ten projekt korzysta z Webpack do kompilacji TypeScript i SCSS.

1. Instalacja zależności

W katalogu projektu uruchom:
```npm install```

To polecenie zainstaluje wszystkie wymagane paczki zdefiniowane w package.json.

2. Kompilacja zasobów

a) Jednorazowa kompilacja:
```npm run build```

W rezultacie powstaną pliki bundle.js i bundle.css w folderze public/.

b) Kompilacja z obserwacją zmian (watcher):
```npm run watch```

Webpack wykryje każdą zmianę w plikach .ts oraz .scss w folderze src i automatycznie przebuduje output.

3. Struktura projektu

- src/
-   ts/      → TypeScript (np. main.ts)
-  scss/    → SCSS (np. main.scss)
- public/    → Kompilowane pliki: bundle.js, bundle.css
- webpack.config.js → Konfiguracja Webpacka
- package.json      → Skrypty i zależności

4. Dodatkowe informacje

- Konfiguracja Webpacka (webpack.config.js) definiuje punkty wejścia, ścieżki wyjściowe oraz używane pluginy.
- Skrypty build i watch są zdefiniowane w sekcji scripts w package.json.
