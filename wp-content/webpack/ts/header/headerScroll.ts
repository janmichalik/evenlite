export function setupHeaderScroll(): void {
    let lastScrollPosition: number = 0;
    const header: HTMLElement | null = document.querySelector('.header');
    if (!header) return;
    window.addEventListener('scroll', () => {
        const currentScrollPosition = window.scrollY;
        if (currentScrollPosition > window.innerHeight) {
            if (currentScrollPosition < lastScrollPosition) {
                header.classList.add('header--active');
            } else {
                header.classList.remove('header--active');
            }
        } else {
            header.classList.add('header--active');
        }
        lastScrollPosition = currentScrollPosition;
    });
}
