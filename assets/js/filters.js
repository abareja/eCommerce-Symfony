
class Filters {
    constructor(el, spinner) {
        this.form = el;
        
        if( !this.form.querySelector('.js-filters-json') ) return;

        this.json = JSON.parse(this.form.querySelector('.js-filters-json').value);
        this.productsContainer = document.querySelector('.js-filters-products');
        this.products = this.productsContainer.querySelectorAll('.js-filters-product');
        this.spinner = spinner;

        this.initFormSubmission();
    }

    getFormData = () => {
        let formData = new FormData(this.form);

        var object = {};
        formData.forEach((value, key) => {
            if( key == 'json' ) return;
            if(!Reflect.has(object, key)){
                object[key] = value;
                return;
            }
            if(!Array.isArray(object[key])){
                object[key] = [object[key]];    
            }
            object[key].push(value);
        });

        return JSON.parse(JSON.stringify(object));
    }

    getAllProducts = () => {
        let products = [];
        this.json.forEach((product) => {
            products.push(product.id);
        });

        return products;
    }

    filterByPrice = (formData) => {
        let priceFrom = formData["price-from"] ? parseFloat(formData["price-from"]) : null;
        let priceTo = formData["price-to"] ? parseFloat(formData["price-to"]) : null;

        if( !this.json ) return;
        let products = [];

        this.json.forEach((product) => {
            let productPrice = product.discountPrice ? product.discountPrice : product.price;
            if( priceFrom != null && priceTo != null ) {
                if( productPrice >= priceFrom && productPrice <= priceTo ) products.push(product.id);
            } else if( priceFrom != null && priceTo == null ) {
                if( productPrice >= priceFrom ) products.push(product.id);
            } else if( priceFrom == null && priceTo != null ) {
                if( productPrice <= priceTo ) products.push(product.id)
            } else {
                products.push(product.id);
            }
        });

        return products;
    }

    filterBySupplier = (formData) => {
        let suppliers = formData['supplier'] ? [...formData['supplier']] : [];
        if( !this.json ) return;
        let products = [];

        if( suppliers.length === 0 ) {
            return this.getAllProducts();
        }

        suppliers = suppliers.map(supplier => parseInt(supplier));

        this.json.forEach((product) => {
            if( suppliers.includes(product.supplier.id) ) products.push(product.id);
        });

        return products;
    }

    checkProductAttributes = (product, attribute, attributeValues) => {
        attribute = parseInt(attribute);
        attributeValues = typeof attributeValues == 'string' ? [attributeValues] : attributeValues;
        attributeValues = attributeValues.map(value => Number(value));
        let productAttributes = product.productAttributes ? [...product.productAttributes] : [];

        if( productAttributes.length === 0 ) return true;

        for( let i in productAttributes ) {
            if( productAttributes[i].attribute.id == attribute && attributeValues.includes(productAttributes[i].attributeValue.id) ) {
                return true;
            }
        }

        return false;
    }

    filterByAttributes = (formData) => {
        delete formData['supplier'];
        delete formData['price-from'];
        delete formData['price-to'];
        let products = this.getAllProducts();

        for ( let attribute in formData ) {
            let productsForAttribute = [];

            this.json.forEach(product => {
                if( this.checkProductAttributes(product, attribute, formData[attribute]) ) {
                    productsForAttribute.push(product.id);
                }
            });
            
            products = this.arrayIntersection(products, productsForAttribute);
        }

        return products;
    }

    arrayIntersection = (array1, array2) => {
        const filteredArray = array1.filter(value => array2.includes(value));

        return filteredArray;
    }

    initFormSubmission = () => {
        const $root = $('html, body');

        this.form.addEventListener('submit', e => {
            e.preventDefault(); 
            this.spinner.style.opacity = "1";

            let formData = this.getFormData();
            let productsByPrice = this.filterByPrice(formData);
            let productsBySupplier = this.filterBySupplier(formData);
            let productsByAttributes = this.filterByAttributes(formData);

            let products = this.arrayIntersection(productsByPrice, productsBySupplier);
            products = this.arrayIntersection(products, productsByAttributes);
            
            this.products.forEach(product => {
                const id = parseInt(product.dataset.product);

                if( products.includes(id) ) {
                    product.classList.remove('u-hidden');
                } else {
                    product.classList.add('u-hidden');
                }
            });

            $root.animate({
                scrollTop: 0
            }, 500, 'swing');

            setTimeout(() => {
                this.spinner.style.opacity = "0";
            }, 900);
        });
    }
}

export const init = (el, spinner) => {
    new Filters(el, spinner);
}