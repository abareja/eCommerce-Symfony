export function triggerEvent(element, event_name = '', args = {}) {
    let event;
    if( document.createEvent ) {
        event = document.createEvent("HTMLEvents");
        event.initEvent(event_name, true, true);
    } else {
        event = document.createEventObject();
        event.eventType = event_name;
    }

    event.eventName = event_name;
    event.SmArgs = args;

    if( document.createEvent ) {
        element.dispatchEvent(event);
    } else {
        element.fireEvent("on" + event.eventType, event);
    }
}

export const isInViewport = elem => {
    const bounding = elem.getBoundingClientRect();
    const doc = document.documentElement
    const viewportHeight = (window.innerHeight || document.documentElement.clientHeight);
    const scroll = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0) + viewportHeight;
    return (
        scroll > bounding.top
    );
};

export function isScrolledIntoView(item, offset = 0) {
        const doc = document.documentElement;
        const scroll = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
    const bottom = scroll + window.innerHeight + offset;
    
        const elemTop = item.getBoundingClientRect().top - document.body.getBoundingClientRect().top;
        const elemBottom = elemTop + item.offsetHeight;
    
    return ((elemTop >= scroll) && (elemTop <= bottom));
}

export const slickPresets = {
    noPause: {
        pauseOnFocus: false,
        pauseOnHover: false
    },
    fade: {
        slidesToShow: 1,
        slidesToScroll: 1,
        fade: true,
        cssEase: 'linear',
    },
    autoplay: {
        autoplay: true,
        autoplaySpeed: 3000
    },
    noControls: {
        dots: false,
        arrows: false
    }
}