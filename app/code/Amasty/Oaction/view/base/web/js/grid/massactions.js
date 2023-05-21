
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/massactions',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/lib/collapsible',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, _, Massactions, registry, utils, Collapsible, confirm, alert, $t) {
    'use strict';

    return Massactions.extend({
        defaults: {
            actionSpace: {
                status: 'addStatus',
                comment: 'addComment',
                status_comment: 'addStatusAndComment'
            }
        },

        applyAmastyAction: function (action, event) {
            var data = this.getSelections(),
                callback;

            if (action.skipOnChange && event.type === 'change') {
                return this;
            }

            if (!data.total) {
                alert({
                    content: this.noItemsMsg
                });

                return this;
            }

            callback = this.actionCallback.bind(this, action, data);

            action.confirm ?
                this._confirmAction(action, callback) :
                callback();

            return this;
        },

        actionCallback: function (action, data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                actionName = action.actionName,
                selections = {};

            selections[itemsType] = data[itemsType];

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            this[this.actionSpace[actionName]](selections);

            _.extend(selections, data.params || {});

            utils.submit({
                url: action.url,
                data: selections
            });
        },

        /**
         * Default action callback. Sends selections data
         * via POST request.
         *
         * @param {Object} action - Action data.
         * @param {Object} data - Selections data.
         */
        defaultCallback: function (action, data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {},
                ajaxSaveOptions = {ajaxSaveType: 'default'};

            selections[itemsType] = data[itemsType];

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            _.extend(selections, data.params || {});

            if (_.isUndefined(action.download) || !action.download) {
                utils.submit({
                    url: action.url,
                    data: selections
                });

                return;
            }

            selections.download = true;
            utils.ajaxSubmit({
                url: action.url,
                data: selections
            }, ajaxSaveOptions).done(this.downloadFileAndRedirect.bind(this));
        },

        downloadFileAndRedirect: function (response) {
            var responseData = _.extend(
                {},
                {download: false, filename: '', content: '', redirectUrl: ''},
                response || {}
            );

            if (responseData.download && responseData.filename && responseData.content) {
                this._downloadFile(responseData.filename, responseData.content);
            }

            if (responseData.redirectUrl) {
                window.location.href = responseData.redirectUrl;
            }
        },

        _downloadFile: function (filename, content) {
            var a = document.createElement('a');

            document.body.appendChild(a);
            a.style = 'display: none';
            a.download = filename;
            a.href = content;
            a.click();
            window.URL.revokeObjectURL(content);
        },

        applyAction: function (actionIndex) {
            var data = this.getSelections(),
                action,
                callback;

            if (!data.total) {
                alert({
                    content: this.noItemsMsg
                });

                return this;
            }

            action = this.getAction(actionIndex);

            if (action.type && action.url.indexOf('oaction') && action.url.indexOf('ship') > 0) {
                var selected = data['selected'];
                var oaction = {};
                var emptyTracks = [];
                var shippings = $('table.data-grid .aoaction-cell');

                if (!shippings.length) {
                    alert({
                        content: $.mage.__('Please make the Shipping column visible.')
                    });

                    return;
                }

                shippings.each(function (index, value) {
                    var tr = $(value).parents('tr');

                    if (tr.length) {
                        var input = tr.find('input.admin__control-checkbox[id*="check"]')
                        var id = input.val();

                        if ($.inArray(id, selected) >= 0) {
                            oaction[id] = {};

                            $(value).find('[name^="amasty"]').each(function (key, element) {
                                element = $(element);
                                oaction[id][element.attr('name')] = element.val();

                                if ('amasty-tracking' === element.attr('name')) {
                                    element.closest('.admin__field').removeClass('_error');

                                    if (oaction[id]['amasty-carrier'] && !oaction[id][element.attr('name')]) {
                                        emptyTracks.push(id);
                                        element.closest('.admin__field').addClass('_error');
                                    }
                                }
                            });
                        }
                    }
                });

                if (0 < emptyTracks.length) {
                    alert({
                        content: $.mage.__('Please fill the Tracking Number where you selected Carrier.')
                    });

                    return;
                }

                data['params']['oaction'] = oaction;
            }

            callback = this._getCallback(action, data);

            action.confirm ?
                this._confirm(action, callback) :
                callback();

            return this;
        },

        /**
         * Shows actions' confirmation window.
         *
         * @param {Object} action - Actions' data.
         * @param {Function} callback - Callback that will be
         *      invoked if action is confirmed.
         */
        _confirmAction: function (action, callback) {
            var confirmData = action.confirm,
                data = this.getSelections(),
                total = data.total ? data.total : 0,
                confirmMessage = confirmData.message + ' (' + total + ' record' + (total > 1 ? 's' : '') + ')';

            confirm({
                title: confirmData.title,
                content: confirmMessage,
                actions: {
                    confirm: callback,
                    cancel: function () {
                        var oactionElement = $('[data-amoaction-js="action"]');

                        if (oactionElement.length) {
                            oactionElement.val("");
                        }
                    }
                }
            });
        },

        addComment: function (selections) {
            selections['comment_text'] = this.comment_text;
        },

        addStatus: function (selections) {
            selections['status'] = this.selectedStatus;
        },

        addStatusAndComment: function (selections) {
            this.addComment(selections);
            this.addStatus(selections);
        }
    });
});
