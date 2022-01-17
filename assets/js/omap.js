import forEach from 'lodash/forEach';
import "leaflet";

class oMap {
    constructor( el ) {
        this.el = el;
        this.markers = [];
        this.createMap();
        this.createMarkers();
        this.centerMap();
        this.masks = this.el.querySelectorAll('.js-map-mask');
    }

    createMap() {
        this.map = L.map(this.el, {
            dragging: !L.Browser.mobile,
            tap: !L.Browser.mobile
        }).setView([0,0],13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
        }).addTo(this.map);
        this.map.scrollWheelZoom.disable();
        this.el.addEventListener("mousewheel", event => {
            event.stopPropagation();
            if (event.ctrlKey == true) {
                event.preventDefault();
                this.map.scrollWheelZoom.enable();

                this.masks.forEach(mask => {
                    mask.classList.remove('is-visible');
                });

                setTimeout(() => {
                    this.map.scrollWheelZoom.disable();
                }, 1000);
            } else {
                this.map.scrollWheelZoom.disable();

                this.masks.forEach(mask => {
                    mask.classList.add('is-visible');
                });
            }
        });
        window.addEventListener("mousewheel", event => {
            this.masks.forEach(mask => {
                mask.classList.remove('is-visible');
            });
        });
    }

    createMarkers() {
        const markers = this.el.querySelectorAll('.marker');
        forEach(markers, (marker) => {
            let icon = false;
            if( marker.dataset.icon ) {
                icon = L.icon({
                    iconUrl: marker.dataset.icon,
                    iconSize: [marker.dataset.iconWidth, marker.dataset.iconHeight],
                    iconAnchor: [ parseInt(marker.dataset.iconWidth/2), marker.dataset.iconHeight]
                });
            }
            this.addMarker(marker.dataset.lat, marker.dataset.lng, marker.dataset.address, icon)
        });
    }

    addMarker(lat, lng, address, icon) {
        const markerOptions = {};
        if( icon ) {
            markerOptions.icon = icon;
        }

        const marker = new L.marker([lat,lng], {...markerOptions});
        marker.addTo(this.map);
        marker.addEventListener('click', e => {
            const LatLng = marker.getLatLng();
            var win = window.open(`http://maps.google.com/maps?q=${address}`, '_blank');
            win.focus();
        });
        this.markers.push(marker);
    }

    centerMap() {
        const points = this.markers.map(marker => {
            return marker.getLatLng();
        });

        const bounds = new L.LatLngBounds(points);
        this.map.fitBounds(bounds, {maxZoom: 17, padding: L.point(100, 100)});
    }
}

export default oMap;