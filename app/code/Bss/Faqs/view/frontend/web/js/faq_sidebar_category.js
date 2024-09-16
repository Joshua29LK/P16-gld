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

    $.widget('bss.faq_sidebar_category', {
        _create: function () {
            var $widget = this;
            $widget.element.on('click', '.faqs_sidebar_item.sidebar_category', function () {
                if (typeof $(this).attr('url_key') !== typeof undefined && $(this).attr('url_key') != false) {
                    window.location.href = $widget.options.baseUrl + 'faqs/category/view/url/' + $(this).attr('url_key');
                } else {
                    window.location.href = $widget.options.baseUrl + 'faqs/category/view/id/' + $(this).attr('cate_id');
                }
            });
        }
    });
    return $.bss.faq_sidebar_category;
});
