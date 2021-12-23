const initLazyload = () => {
    import(/* webpackChunkName: "Lazyload" */ "vanilla-lazyload/dist/lazyload").then((lazyload) => {
      const lazyLoadOptions = {
          elements_selector: ".js-lazyload",
          // callback_loaded: () => {
          //     initMasonry();
          // }
      };
      const lazyloadInstance = new lazyload.default(lazyLoadOptions);
    });
  }
initLazyload();
  
//PLACEHOLDERS
const initPlaceholders = () => {
    const elements = document.querySelectorAll('.placeholder');

    if( elements.length === 0 ) return;

    window.addEventListener('load', () => {
        elements.forEach(el => {
        el.classList.remove('placeholder');
        });
    });
}
initPlaceholders();

//HEADER CART
const initHeaderCart = () => {
  const headerCart = document.querySelector('.js-header-cart');

  if( !headerCart ) return; 
  import(/* webpackChunkName: "headerCart" */ "./headerCart").then(script => {
      script.initHeaderCart(headerCart);
  });
}
initHeaderCart();

//FORMS
const initForms = () => {
  import(/* webpackChunkName: "forms" */ "./forms").then(script => {
      script.init();
  });
}
initForms();