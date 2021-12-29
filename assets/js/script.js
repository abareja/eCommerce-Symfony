import "slick-carousel";
import "./import-jquery";

//LAZYLOAD
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

//ANIMATIONS ON SCROLL
const initAOS = () => {
  import(/* webpackChunkName: "AOS" */ "aos").then((AOS) => {
    AOS.init();
  });
}
initAOS();

//SLIDERS
const initSliders = () => {
  const sliders = document.querySelectorAll('.js-slider');
  if( sliders.length === 0 ) { return; }

  sliders.forEach( slider => {
      const settings = JSON.parse(slider.dataset.slickSettings);

      $(slider).slick({
          ...settings
      });
  });
}
initSliders();

//ADMIN
const initAdmin = () => {
  if( !document.body.classList.contains('admin-dashboard') ) return;
  import(/* webpackChunkName: "admin" */ "./admin").then((admin) => {
    
  });
}
initAdmin();

//SELECT2
const initSelect2 = async () => {
  const select2 = document.querySelectorAll('.js-select2');

  if( select2.length !== 0 ) {
      import(/* webpackChunkName: "select2" */ 'select2').then(() => {
          select2.forEach(el => {
              $(el).on("select2:select", (e) => {
                  triggerEvent(e.target, 'change');
              }).select2({
                  width: "100%",
                  theme: "shop",
                  minimumResultsForSearch: 5,
                  language: {
                      inputTooShort: function() {
                          return 'Wpisz co najmniej 3 znaki';
                      },
                      noResults: function() {
                          return 'Brak wyników';
                      },
                      searching: function() {
                          return 'Szukam...';
                      },
                      errorLoading: function() {
                          return 'Błąd wyszukiwania';
                      }
                  }
              });
              el.addEventListener("change", (e) => {
                  const select = e.target
                  const el = select.options[select.selectedIndex]
                  if(el.dataset.href) {
                      window.location.replace(el.dataset.href)
                  }
              });
          });
      });
  }
}
initSelect2();    

//QUANTITY
const initQuantity = async () => {
  const quantityInputs = document.querySelectorAll('.js-quantity');

  if( quantityInputs.length !== 0 ) {
      import(/* webpackChunkName: "quantity" */ './quantity').then(quantity => {
          quantityInputs.forEach(el => {
              quantity.initQuantity(el);
          });
      });
  }
}
initQuantity();

//FILE INPUTS
const initFileInput = async () => {
    const fileInputs = document.querySelectorAll('.js-file-input');
  
    if( fileInputs.length !== 0 ) {
        import(/* webpackChunkName: "fileInput" */ './fileInput').then(fileInput => {
            fileInputs.forEach(el => {
                fileInput.initFileInput(el);
            });
        });
    }
  }
initFileInput();

//FANCYBOX
import(/* webpackChunkName: "fancybox" */ "@fancyapps/fancybox").then(() => {
    $.fancybox.defaults.animationEffect = "fade";
    $.fancybox.defaults.backFocus = false;
});
