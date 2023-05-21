var config = {
  config: {
      mixins: {
          "Magento_Catalog/js/catalog-add-to-cart": {
              "RedChamps_MiniCartScroll/js/add-to-cart-mixin": true
          },
          'Magento_Checkout/js/view/minicart': {
              'RedChamps_MiniCartScroll/js/minicart-mixin': true
          }
      }
  }
};