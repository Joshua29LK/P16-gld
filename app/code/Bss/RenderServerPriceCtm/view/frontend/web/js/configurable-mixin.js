define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.configurable', widget, {
            /**
             * Override default options values settings with either URL query parameters or
             * initialized inputs values.
             * @private
             */
            _overrideDefaults: function () {
                var hashIndex = window.location.href.indexOf('~');

                if (hashIndex !== -1) {
                    const rawParams = window.location.href.substr(hashIndex + 1);
                    const cleanedParams = rawParams.replace(/opt_(\d+)=/g, '$1=');
                    this._parseQueryParams(cleanedParams);
                }

                if (this.options.spConfig.inputsInitialized) {
                    this._setValuesByAttribute();
                }

                this._setInitialOptionsLabels();
            }
        });
        return $.mage.configurable;
    };
});
