export const initHeaderCart = el => {
    const button = el.querySelectorAll(".js-header-cart__toggle");
    const close = el.querySelectorAll(".js-header-cart__close");

    if( button.length === 0 ) return; 

    button.forEach(item => {
        item.addEventListener("click", event => {
            event.preventDefault();
            el.classList.add("is-open");
        });
    });

    if( close.length === 0 ) return; 

    close.forEach(item => {
        item.addEventListener("click", event => {
            event.preventDefault();
            el.classList.remove("is-open");
        });
    });
}