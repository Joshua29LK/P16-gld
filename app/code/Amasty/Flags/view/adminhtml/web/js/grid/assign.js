define([
    'jquery',
    'ko',
    'Magento_Ui/js/modal/modal-component',
    'mage/translate',
    'uiRegistry',
    'mage/storage',
    'Magento_Ui/js/modal/alert'
], function ($, ko, Component, $t, registry, storage, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_Flags/assign/modal',
            modalClass: 'amasty-flags-assign'
        },

        popupTitle: ko.observable(''),
        applicableFlags: ko.observable([]),

        currentOrderId: null,
        currentColumnId: null,
        currentFlagElement: null,

        showPopup: function (orderId, columnId, elementId) {
            var self = this;

            registry.get('sales_order_grid.sales_order_grid_data_source_storage', function (storage) {
                var element = document.getElementById(elementId);
                self.currentOrderId = orderId;
                self.currentColumnId = columnId;
                self.currentFlagElement = element;

                var orderTitle = storage.data[orderId].increment_id;
                var columnTitle = self.columns[columnId].title;

                var title = $t('Flag For Order #%1 (Column: %2)')
                    .replace('%1', orderTitle)
                    .replace('%2', columnTitle);

                self.popupTitle(title);

                var flags = [];

                self.columns[columnId].flagIds.forEach(function (id) {
                    flags.push(self.flags[id]);
                });

                self.applicableFlags(flags);

                self.openModal();
            });
        },
        setFlag: function (flag) {
            var self = this;

            this.closeModal();

            var payload = {
                orderId: this.currentOrderId,
                columnId: this.currentColumnId,
                flag: flag
            };

            $('body').trigger('processStart');

            // do not use "success" method because some error responses might be with 200 code
            return $.ajax({
                url: this.actionUrl,
                type: 'POST',
                data: payload
            }).done(function (response) {
                $('body').trigger('processStop');
                if (response && response.hasOwnProperty('error')) {
                    alert({content: response.message || $t('Unknown error')});
                } else {
                    var src = flag ? flag.image_src : self.imagePlaceholder;
                    var title = self.columns[self.currentColumnId].comment || $t('No flag');

                    if (flag) {
                        title = flag.note || flag.defaultNote || flag.title;
                    }

                    self.currentFlagElement.setAttribute('src', src);
                    self.currentFlagElement.setAttribute('alt', title);
                    self.currentFlagElement.setAttribute('title', title);
                }
            }).fail(function (response) {
                $('body').trigger('processStop');
                alert({content: $t('Unknown error')});
            });
        }
    });
});
