/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mageworx.optionAdvancedPricing', {
        options: {
            optionConfig: {}
        },

        firstRun: function firstRun(optionConfig, productConfig, base, self) {
            base.setOptionValueTitle();
        },

        update: function update(option, optionConfig, productConfig, base) {
            var $option = $(option),
                values = $option.val(),
                self = this;

            $('option', $option).each(function (i, e) {
                var tierPrice = $('#value_' + e.value + '_tier_price');
                if (tierPrice.length > 0) {
                    tierPrice.hide();
                }
            });

            var optionId = base.getOptionId($option);
            if (!values
                || $.inArray(self.options.optionTypes[optionId], ['drop_down', 'multiple', 'checkbox', 'radio']) === -1
            ) {
                return;
            }

            if (!Array.isArray(values)) {
                values = [values];
            }

            $(values).each(function (i, e) {
                var tierPrice = $('#value_' + e + '_tier_price');
                if (tierPrice.length > 0) {
                    if ($option.is(':checked') || $('option:selected', $option).val()) {
                        tierPrice.show();
                    } else {
                        tierPrice.hide();
                    }
                }
            });

        }
    });

    return $.mageworx.optionAdvancedPricing;

});