/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            CatalogAddToConfigurable: 'Superiortile_RequiredProduct/js/product/view/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Superiortile_RequiredProduct/js/swatch-renderer-mixin': true
            }
        }
    },
    paths: {
        'owl.carousel': 'Superiortile_RequiredProduct/owl.carousel/owl.carousel.min',
        'OXowlCarousel': 'Superiortile_RequiredProduct/owl.carousel'
    },
    shim: {
        'owl.carousel': {deps: ['jquery', 'jquery-ui-modules/widget']}
    }
};