const el = {
    sidebar: document.querySelector('.js-sidebar')
};

(async () => {
    if( !el.sidebar ) return;

    const links = el.sidebar.querySelectorAll('a');

    links.forEach(link => {
        const parent = link.parentElement;
        if( parent.classList.contains('has-children')) {
            parent.addEventListener( "click", event => {
                //event.preventDefault();
                //event.stopImmediatePropagation();
                parent.classList.toggle('is-open');
            });
        }
    });   
})();

