import "slick-carousel";
import "./import-jquery";
import(/* webpackChunkName: "sidebar" */ "./sidebar");
import List from "list.js";

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
    AOS.init({
        once: true,
        offset: -100
    });
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

//EQUALIZER
const initEqualizer = async () => {
    const equalize = document.querySelectorAll('.js-equalize');

    if( equalize.length !== 0 ) {
        import(/* webpackChunkName: "equalizer" */ "./equalizer").then(() => {
            setTimeout(() => { 
                $(equalize).equalizer({use_tallest: true}); 
            }, 500);
        });
    }
}
initEqualizer();

//SCROLL TO TOP
const initScrollTop = () => {
    const button = document.querySelector(".js-to-top");
    if(button) {
        button.addEventListener("click", event => {
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: "smooth",
            });
        });
    
        window.addEventListener("scroll", event => {
            if(window.pageYOffset > 300){
                button.classList.add('is-visible');
            } else {
                button.classList.remove('is-visible');
            }
        });
    }
};
initScrollTop();

//ZOOM
const initZoom = () => {
    import(/* webpackChunkName: "zoom" */ "./zoom").then((script) => {
        const zoom = document.querySelectorAll('.js-zoom');
    
        if( zoom.length === 0 ) return;
    
        zoom.forEach(el => {
            script.initZoom(el);
        });
    });
}
initZoom();

//PRODUCT GALLERY
const initProductGallery = () => {
    import(/* webpackChunkName: "productGallery" */ "./productGallery").then((script) => {
        script.init();
    });
}
initProductGallery();

//LIST.JS
const initLists = () => {
    const lists = document.querySelectorAll('.js-list');

    if( lists.length === 0 ) return;

    lists.forEach(list => {
        const settings = JSON.parse(list.dataset.listSettings);
        let id = list.id;

        var options = {
            ...settings
        };

        var list = new List(id, options);
    });
}
initLists();

//COLLECTIONS
const initCollections = () => {
    import(/* webpackChunkName: "collection" */ "symfony-collection").then(() => {
        $('.js-collection').collection({
            add: '<span class="o-collection__button o-collection__button--full"><span class="o-icon o-icon--plus"></span></span>',
            remove: '<span class="o-collection__button"><span class="o-icon o-icon--minus"></span></span>',
            allow_up: false,
            allow_down: false,
            allow_duplicate: false,
            add_at_the_end: true,
            prefix: 'parent',
            prototype_name: '__parent_name__',
            name_prefix: '{{ formView.myCollectionField.vars.full_name }}',
            after_add: function (collection, element) {
                initSelect2();
            },
        }); 
    });
}
initCollections();