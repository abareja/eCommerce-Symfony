import isNull from "lodash/isNull";

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

export const createElement = (tag, props, ...children) => {
    if (typeof tag === "function") return tag(props, ...children);
    const element = document.createElement(tag);

    Object.entries(props || {}).forEach(([name, value]) => {
        if (name.startsWith("on") && name.toLowerCase() in window)
            element.addEventListener(name.toLowerCase().substr(2), value);
        else if (name.toLowerCase() == "selected" || name.toLowerCase() == "checked") {
            if (value == true) {
                element.setAttribute(name, true);
              }
        } else if (name.toLowerCase() == 'setinnerhtml' ) {
            element.innerHTML = value;
        }else if ( ! isNull(value) )
            element.setAttribute(name, value.toString());
    });
    children.forEach(child => {
        appendChild(element, child);
    });

    return element;
};
  
const appendChild = (parent, child) => {
    if( isNull(child) ) { return; }
    if (Array.isArray(child))
        child.forEach(nestedChild => appendChild(parent, nestedChild));
    else
        parent.appendChild(child.nodeType ? child : document.createTextNode(child));
};
  
export const createFragment = (props, ...children) => {
    return children;
};

export const isInt = (str) => {
    return !isNaN(str) && Number.isInteger(parseFloat(str));
}

export const isIntFromRange = (value, minValue, maxValue) => {
    const minVal = minValue ? parseInt(minValue) : "";
    const maxVal = maxValue ? parseInt(maxValue) : "";
    let valid = false;

    if( isInt(value) ) {
        valid = true;
        value = parseInt(value);

        if( minVal != "" && maxVal != "" ) {
            if( value >= minVal && value <= maxVal ) valid = true; else valid = false;
        } else if( minVal != "" && maxVal == "" ) {
            if( value >= minVal ) valid = true; else valid = false;
        } else if( minVal == "" && maxVal != "" ) {
            if( value <= maxVal ) valid = true; else valid = false;
        }
    } else {
        valid = false;
    }

    return valid;
}
