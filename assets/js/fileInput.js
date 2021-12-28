import { createElement, appendChild } from "./helpers";

class FileInput {
    constructor( element, options ) {
        const defaults = {
            itemClass: 'o-file__item',
        }

        this.settings = { ...defaults, ...options };

        this.state = {value: []};
        this.id = Date.now();

        this.el = {
            parent: element,
            input: element.querySelector('input[type="file"]'),
        };

        this.el.input.addEventListener('change', event => {
            this.changeEvent(event);
        });
    }

    readImage = (file) => {
        return new Promise((resolve, reject) => {
            let reader = new FileReader();

            reader.onloadend = () => {
                resolve(reader.result);
            };
            reader.onerror = reject;

            reader.readAsDataURL(file);
        });
    }

    changeEvent = async (event) => {
        this.state = {value: []};
        let files = event.target.files;

        for(let i = 0; i < files.length; i++) {
            let url = await this.readImage(files[i]);
            this.state.value.push(url);
        }

        this.renderAllItems();
    }

    renderAllItems = () => {
        const images = this.el.parent.querySelectorAll('.o-file--image');

        if( images.length > 0 ) {
            images.forEach(image => {
                image.remove();
            });
        } 

        for(let i = 0; i < this.state.value.length; i++) {
            this.el.input.after( this.renderItem(this.state.value[i]) );
        }
    }

    renderItem = (src) => {
        return (
            <div class={` ${this.settings.itemClass} o-file--image`} style={`background-image: url(${src})`}>
                <a href={src} data-fancybox={`file-input-${this.id}`}></a>
            </div>
        );
    }
}

export const initFileInput = async (el) => {
    new FileInput(el);
} 