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
    'Bss_Faqs/js/social/jssocials'
], function ($, _) {
    'use strict';

    $.widget('bss.faq_social_share', {
        _create: function () {
            this.element.jsSocials({shares: ["twitter", "facebook"]});
        }
    });
    return $.bss.faq_social_share;
});
