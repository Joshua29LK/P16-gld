define([
    'Magento_Ui/js/grid/columns/thumbnail',
    'uiRegistry'
], function (Column, registry) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Amasty_Flags/grid/cells/flag',
            isAssignAllowed: false
        },
        openModal: function (row) {
            if (!this.isAssignAllowed) {
                return;
            }

            var self = this;

            registry.get('sales_order_grid.sales_order_grid.amflags_flag_assign', function (component) {

                component.showPopup(
                    row.entity_id,
                    self.columnId,
                    self.getElementId(row)
                );
            });
        },
        getElementId: function (row) {
            return 'amasty-flag-' + row.entity_id + '-' + this.columnId;
        }
    });
});
