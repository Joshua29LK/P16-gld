define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
    'use strict';

    return select.extend({
        initialize: function () {
            this._super();

            if (this.hasOwnProperty('initialValue') && typeof this.initialValue !== 'undefined') {
                this.processFieldsVisibility(this.initialValue);
            }
        },

        onUpdate: function (value) {
            this.processFieldsVisibility(value);

            return this._super();
        },

        processFieldsVisibility: function (value) {
            this.processFieldVisibility(value, 'category_id');
            this.processFieldVisibility(value, 'parent_id');
            this.processFieldVisibility(value, 'second_parent_id');
        },

        processFieldVisibility: function (value, fieldName) {
            var field = uiRegistry.get('index = ' + fieldName);

            if (field.visibleValue === value) {
                field.show();

                return;
            }

            field.hide();
        }
    });
});