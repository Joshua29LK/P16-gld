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
 * @copyright  Copyright (c) 2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery'
], function (Component, customerData, $) {
    'use strict';

    loadAjax();

    /**
     * Display number of like, unlike and state
     */
    function loadAjax() {
        let faqId = $('.hidden-faq-vote-faq_id').html().toString();
        let url = $('.hidden-faq-vote-reload-url').html().toString();
        $.ajax({
            type: 'get',
            url: url,
            data: {
                'faqId': faqId,
                'form_key': $.cookie('form_key')
            },
            dataType: 'json',
            success : function (result) {
                successAjax(result);
            },
            failed : function () {
                failedAjax();
            }
        });
    }

    /**
     * After call ajax failed, set like unlike is 0, state not choose like or unlike
     */
    function failedAjax() {
        $('#faq-like').html("0");
        $('#faq-unlike').html("0");
        let state = document.getElementById('bss_faq_vote');
        state.className =  "";
    }

    /**
     * After call ajax success, set like unlike is 0, state choose like or unlike
     *
     * @param {Object} result
     */
    function successAjax(result) {
        if (result) {
            $('#faq-like').html(result.like);
            $('#faq-unlike').html(result.unlike);
            let state = document.getElementById('bss_faq_vote');
            state.className =  result.state;
        }
    }

    return Component.extend({
		getFaqVoteMessageType: function () {
            var data = customerData.get('bss_faq_vote_section');
            return data().messageType;
		},
		getFaqVoteMessage: function () {
            var data = customerData.get('bss_faq_vote_section');
            return data().message;
		},
        faqLikeSubmit: function () {
            var $this = this;
            $.ajax({
                type: 'post',
                url: $this.ajaxSubmit,
                data: {
                    'type': '1',
                    'faqId': $this.faqId,
                    'form_key': $.cookie('form_key')
                },
                dataType: 'json',
                success : function (result) {
                   successAjax(result)
                }
            });
        },
        faqUnlikeSubmit: function () {
            var $this = this;
            $.ajax({
                type: 'post',
                url: $this.ajaxSubmit,
                data: {
                    'type': '-1',
                    'faqId': $this.faqId,
                    'form_key': $.cookie('form_key')
                },
                dataType: 'json',
                success : function (result) {
                    successAjax(result)
                }
            });
        }
    });
});
