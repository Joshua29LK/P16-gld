/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            CatalogAddToConfigurable: 'Bss_RequiredProduct/js/product/view/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Bss_RequiredProduct/js/swatch-renderer-mixin': true
            }
        }
    },
    paths: {
        'owl.carousel': 'Bss_RequiredProduct/owl.carousel/owl.carousel.min',
        'OXowlCarousel': 'Bss_RequiredProduct/owl.carousel'
    },
    shim: {
        'owl.carousel': {deps: ['jquery', 'jquery-ui-modules/widget']}
    }
};