define([
    'ko',
    'underscore',
    'Amasty_Oaction/js/grid/massactions'
], function (ko, _, Massactions) {
    'use strict';

    return Massactions.extend({
        defaults: {
            template: 'Amasty_Oaction/grid/tree-massactions',
            submenuTemplate: 'Amasty_Oaction/grid/native-submenu',
            amastysubmenuTemplate: 'Amasty_Oaction/grid/submenu',
            selectProvider: '',
            modules: {
                selections: '${ $.selectProvider }'
            },
            listens: {
                opened: 'hideSubmenus'
            },
            selectors: {
                menuClass: 'action-menu'
            }
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Massactions} Chainable.
         */
        initObservable: function () {
            this._super()
                .recursiveObserveActions(this.actions());

            return this;
        },

        /**
         * Recursive initializes observable actions.
         *
         * @param {Array} actions - Action objects.
         * @returns {Massactions} Chainable.
         */
        recursiveObserveActions: function (actions) {
            _.each(actions, function (action) {
                if (action.actions) {
                    action.visible = ko.observable(false);
                    action.top = ko.observable(0);
                    action.parent = actions;
                    this.recursiveObserveActions(action.actions);
                }
            }, this);

            return this;
        },

        /**
         * Applies specified action.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {Object} action
         * @param {Event} event
         * @returns {Massactions} Chainable.
         */
        applyAction: function (actionIndex, action, event) {
            var visibility,
                submenuTop;

            if (!action) {
                action = this.getAction(actionIndex);
            }

            if (action.visible) {
                visibility = action.visible();
                this.hideSubmenus(action.parent);

                submenuTop = this.calculateSubmenuTop(event.currentTarget.parentElement);
                action.top(submenuTop);
                action.visible(!visibility);

                return this;
            }

            return this._super(actionIndex);
        },

        /**
         * Retrieves action object associated with a specified index.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {Array} actions - Action objects.
         * @returns {Object} Action object.
         */
        getAction: function (actionIndex, actions) {
            var currentActions = actions || this.actions(),
                result = false;

            _.find(currentActions, function (action) {
                if (action.type === actionIndex) {
                    result = action;

                    return true;
                }

                if (action.actions) {
                    result = this.getAction(actionIndex, action.actions);

                    return result;
                }
            }, this);

            return result;
        },

        /**
         * Recursive hide all sub folders in given array.
         *
         * @param {Array} actions - Action objects.
         * @returns {Massactions} Chainable.
         */
        hideSubmenus: function (actions) {
            var currentActions = actions || this.actions();

            _.each(currentActions, function (action) {
                if (action.visible && action.visible()) {
                    action.visible(false);
                }

                if (action.actions) {
                    this.hideSubmenus(action.actions);
                }
            }, this);

            return this;
        },

        applyMassaction: function (action) {
            return this._super(this, action);
        },

        /**
         * @param {Element} activeMenuItem
         * @returns {number}
         */
        calculateSubmenuTop: function (activeMenuItem) {
            var top = 0,
                menuElement = activeMenuItem.parentElement;
            if (menuElement.classList.contains(this.selectors.menuClass)) {
                top = activeMenuItem.offsetTop - menuElement.scrollTop;
            }

            return top;
        }
    });
});
