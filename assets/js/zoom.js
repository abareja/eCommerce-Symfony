import { triggerEvent } from "./helpers";

export const initZoom = async el => {
    el.addEventListener("mousemove", e => {
        var zoomer = e.currentTarget;
        var offsetX = e.offsetX ? e.offsetX : e.touches[0].pageX;
        var offsetY = e.offsetY ? e.offsetY : e.touches[0].pageY;
        var x = offsetX/zoomer.offsetWidth*100
        var y = offsetY/zoomer.offsetHeight*100
        zoomer.style.backgroundPosition = x + '% ' + y + '%';
    });

    const img = el.querySelector('img');
    if( img ) {
        img.addEventListener('load', () => {
            el.style.backgroundImage = `url(${img.src})`;
        });
        triggerEvent(img, 'load');
    }    
}