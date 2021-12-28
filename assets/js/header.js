import { triggerEvent, isScrolledIntoView } from "./helpers";

const el = {
    header: document.querySelector('.js-header'),
    toggleNav: document.querySelector('.js-toggle-nav'),
    searchToggle: document.querySelectorAll('.js-header-search-toggle'),
    search: document.querySelector('.js-header-search'),
};

const closeAllSubmenus = () => {
    const links = el.header.querySelectorAll('a');
    const header = el.header;
    const search = el.search;

    if( !header ) return;

    links.forEach(link => {
        const parent = link.parentElement;

        if( parent !== header ) {
            parent.classList.remove('is-open');
        }

        if( search ) {
            search.classList.remove('is-open');
        }
    });   
}

const initToggleNav = async () => {
    const { header, toggleNav } = el;
    if( header && toggleNav ) {
        toggleNav.addEventListener("click", (e) => {
            e.preventDefault();

            if( header.classList.contains('is-submenu') ) {
                closeAllSubmenus();
                header.classList.remove('is-submenu');
                toggleNav.classList.remove('is-submenu');
            } else {
                header.classList.toggle('is-open');
                toggleNav.classList.toggle('is-open');
            }
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

(async () => {
    if( !el.header ) return;
    const links = el.header.querySelectorAll('a');
    const header = el.header;

    links.forEach(link => {
        const parent = link.parentElement;
        if( parent.classList.contains('has-children')) {
            parent.addEventListener( "click", event => {
                if( !parent.classList.contains('is-open') ) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    const activeSiblings = parent.parentElement.querySelectorAll('li.is-open');
                    activeSiblings.forEach(sibling => { sibling.classList.remove('is-open')});
                    parent.classList.add('is-open');
                    header.classList.add('is-submenu');

                    if( el.toggleNav ) {
                        el.toggleNav.classList.add('is-submenu');
                    }
                }
            });
        }
    });   
})();

