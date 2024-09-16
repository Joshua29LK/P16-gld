/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    $.widget('bss.faq_side_bar_most_faq', {
        _create: function () {
            var $widget = this;
            $widget.element.on('click', '.faqs_sidebar_item.sidebar_most_faq', function () {
                return $widget._expandQuestion($(this));
            });
        },
        _expandQuestion: function ($faqElement) {
            $faqElement.focus();
            if ($faqElement.hasClass('expanding') && this.options.questionDisplay != 'expand') {
                this.element.find('.faqs_sidebar_item_expand[faq_id="' + $faqElement.attr('faq_id') + '"]')
                .removeClass('expanding');
                $faqElement.parents('li').removeClass('expanding');
                $faqElement.removeClass('expanding');
            } else {
                if (this.options.questionDisplay == 'collapse') {
                    var $parentCategory = $faqElement.parents('.faqs_sidebar_data');
                    $parentCategory.find('li').removeClass('expanding');
                    $parentCategory.find('.faqs_sidebar_item_expand').removeClass('expanding');
                    $parentCategory.find('.faqs_sidebar_item').removeClass('expanding');
                    $faqElement.parents('li').addClass('expanding');
                    this.element.find('.faqs_sidebar_item_expand[faq_id="' + $faqElement.attr('faq_id') + '"]')
                    .addClass('expanding');
                    $faqElement.addClass('expanding');
                } else {
                    window.location.href =
                    this.element.find('.faqs_sidebar_item_expand[faq_id="' + $faqElement.attr('faq_id') + '"] a')
                    .attr('href');
                }
            }
        }

    });
    return $.bss.faq_side_bar_most_faq;
});
