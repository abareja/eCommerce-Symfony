export const init = () => {
    const prodGallery = document.querySelector('.js-product-gallery');

    if( prodGallery ) {
        const mainImage = prodGallery.querySelector('.js-product-gallery-featured img');
        const thumbnails = prodGallery.querySelectorAll('.js-product-gallery-image');
        const fancybox = prodGallery.querySelector('.js-product-gallery-featured a[data-fancybox]');

        let mainImageSrc = mainImage.src;

        if( mainImage && thumbnails.length > 0 ) {
            thumbnails.forEach(el => {
                const thumbnailImg = el.querySelector('img');
                let thumbnailSrc = thumbnailImg.src;

                el.addEventListener('mouseenter', () => {
                    mainImageSrc = mainImage.src;
                    mainImage.src = thumbnailSrc;

                    thumbnails.forEach(elem => {
                        elem.classList.remove('is-active');
                    });

                    el.classList.add('is-active');

                    if( fancybox ) {
                        fancybox.href = thumbnailSrc;
                    }
                });
                // el.addEventListener('mouseout', () => {
                //     mainImage.src = mainImageSrc;
                // });
            });
        }
    }
}