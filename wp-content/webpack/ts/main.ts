import '../scss/main.scss';
import { setupHeaderScroll } from './header/headerScroll';
import { restTest } from './http/restTest';
import { fetchPosts } from './http/fetchPosts';

type PageInitMap = {
  [key: string]: () => void;
};

function initDOM(): void {
  setupHeaderScroll();
  const bodyClassList = document.body.classList;
  const pageInitializers: PageInitMap = {
    home: initHomePage,
    single: initSinglePage,
    archive: initArchivePage,
  };
  initGlobal();
  bodyClassList.forEach((cls) => {
    const initFn = pageInitializers[cls];
    if (initFn) {
      initFn();
    }
  });
}

function initGlobal(): void {
  initFetchPosts();
}

function initHomePage(): void {
  restTest();
}

function initSinglePage(): void {
}

function initArchivePage(): void {
}

function initFetchPosts(): void {
  const postContainer: HTMLElement | null = document.getElementById('post-container');
  if (!postContainer) return;
  fetchPosts();
}

document.addEventListener('DOMContentLoaded', initDOM);
