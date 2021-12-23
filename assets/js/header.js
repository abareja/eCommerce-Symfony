import { triggerEvent, isScrolledIntoView } from "./helpers";

const el = {
    header: document.querySelector('.js-header'),
    toggleNav: document.querySelector('.js-toggle-nav'),
    searchToggle: document.querySelectorAll('.js-header-search-toggle'),
    search: document.querySelector('.js-header-search'),
};

const initToggleNav = async () => {
    const { header, toggleNav } = el;
    if( header && toggleNav ) {
        toggleNav.addEventListener("click", (e) => {
            e.preventDefault();
            header.classList.toggle('is-open');
            toggleNav.classList.toggle('is-open');
        });
    }
}
initToggleNav();

const initSearch = async () => {
    const { search, searchToggle } = el;
    if (search && searchToggle.length !== 0) {
        searchToggle.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                search.classList.toggle('is-open');
            });
        });
    }
}
initSearch();

(async () => {
    const { header } = el;
    if( header ) {
        const doc = document.documentElement;
        window.addEventListener("scroll", () => {
            const scroll = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
            if( scroll > 90 ) {
                header.classList.add('is-sticky');
            } else {
                header.classList.remove('is-sticky');
            }
        });
        triggerEvent(window, 'scroll');
    }
})();
