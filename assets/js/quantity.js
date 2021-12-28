class Quantity {
    constructor( element, options) {
        const defaults = {
            moreSelector: '.js-quantity-more',
            lessSelector: '.js-quantity-less',
            inputSelector: '.js-quantity-input'
        }

        this.settings = { ...defaults, ...options };

        this.el = {
            parent: element,
            more: element.querySelector(this.settings.moreSelector),
            less: element.querySelector(this.settings.lessSelector),
            input: element.querySelector(this.settings.inputSelector)
        }

        this.state = {
            quantity: this.stripInputLetters(this.el.input.value)
        }

        this.el.input.addEventListener("change", event => {
            this.updateState({
                quantity: this.stripInputLetters(event.target.value)
            });
        });

        this.el.more.addEventListener("click", event => {
            this.updateState({
                quantity: this.state.quantity+1
            });
        });

        this.el.less.addEventListener("click", event => {
            this.updateState({
                quantity: this.state.quantity-1 || 1
            });
        });        

        this.render(); 
    }

    updateState( newState ) {
        this.state = { ...this.state, ...newState };
        this.render();
    }

    render() {
        this.el.input.value = this.state.quantity;
    }

    stripInputLetters(string) {
       const result = parseInt(string.replace(/\D/g,''));
       return result || 1;
    }
}

export const initQuantity = async (el) => {
    new Quantity(el);
} 