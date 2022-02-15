define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Customer/js/model/customer',
        'mage/translate',
    ],
    function(
        ko,
        Component,
        _,
        stepNavigator,
        customer,
        $t
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Boostsales_PartialShipment/cart-page'
            },

            //add here your logic to display step,
            isVisible: ko.observable(true),
            isLogedIn: customer.isLoggedIn(),
            //step code will be used as step content id in the component template
            stepCode: 'Cart',
            //step title value
            stepTitle: 'Shopping Cart',

            /**
             *
             * @returns {*}
             */
            initialize: function() {
                this._super();
                // register your step
                stepNavigator.registerStep(
                    this.stepCode,
                    //step alias
                    null,
                    $t('Shopping Cart'),
                    //observable property with logic when display step or hide step
                    this.isVisible,

                    _.bind(this.navigate, this),

                    9
                );

                return this;
            },

            /**
             * The navigate() method is responsible for navigation between checkout step
             * during checkout. You can add custom logic, for example some conditions
             * for switching to your custom step
             */
            navigate: function() {

            },


            /**
             * @returns void
             */
            navigateToNextStep: function() {
                stepNavigator.next();
            }
        });
    }
);