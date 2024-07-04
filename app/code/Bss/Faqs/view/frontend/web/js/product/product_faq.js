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
    'underscore',
    'mage/template',
    'mage/translate'
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('bss.product_faq', {
        _create: function () {
            var $widget = this;
            $widget._updateData($widget);
            $widget.element.on('click', '.faq_ask_button', function () {
                return $widget._showFieldSet($widget, $(this));
            });
            $widget.element.on('click', '.product_faq_title', function () {
                return $widget._showQbox($widget, $(this));
            });
            $widget.element.on('click', '.faq_product_submit', function () {
                return $widget._submit($widget);
            });
        },
        _updateData: function ($widget) {
            $.ajax({
                type: 'post',
                url: $widget.options.baseUrl + 'faqs/json/productfaq',
                data: {
                    'product_id': $widget.options.productId
                },
                dataType: 'json'
            }).done(function ($data) {
                $('.product_faq_list').html('');
                $('.faq_product_textfield input[name="username"]').val($data['user']);
                var faqTitleHtml =
                    '<div class="product_faq_title" faq_id="<%- data.faq_id %>" url_key="<%- data.url_key %>">'
                        + '<div class="product_faq_li" faq_id="<%- data.faq_id %>" >'
                            + '<i class="fa fa-caret-right" aria-hidden="true"></i>'
                        + '</div>'
                        + '<div class="product_faq_label">'
                            + '<div><%- data.title %></div>'
                        + '</div>'
                    + '</div>',
                    faqContentHtml =
                    '<div class="product_faq_content" faq_id="<%- data.faq_id %>">'
                        + '<div>'
                            + '<span>'
                                + '<a href="<%- url %>"><%- seeMoreText %></a>'
                            + '</span>'
                        + '</div>'
                    + '</div>';
                $.each($data['faq'], function (index, $question) {
                    $('.product_faq_list').append(
                        mageTemplate(faqTitleHtml, {data: $question})
                    );
                    if ($widget.options.showFullAnswer == '1') {
                        $('.product_faq_list').append(
                            mageTemplate(faqContentHtml, {
                                data: $question,
                                url: $widget._getFaqUrl($question['url_key'], $question['faq_id']),
                                seeMoreText: $.mage.__('See more')
                            })
                        );
                        $('.product_faq_list').find('.product_faq_content[faq_id="' + $question['faq_id'] + '"]')
                        .prepend($question['answer']);
                    }

                });
            });
        },
        _getFaqUrl: function ($url, $id) {
            if ($url == "") {
                return (this.options.baseUrl + 'faqs/question/view/id/' + $id);
            } else {
                return (this.options.baseUrl + 'faqs/question/view/url/' + $url);
            }
        },
        _showFieldSet: function ($widget, $button) {
            if ($button.hasClass('active')) {
                $button.removeClass('active');
                $('.faq_ask_field').fadeOut();
                $('.faq_ask_field_notify').css('display', 'none');
            } else {
                $button.addClass('active');
                $('.faq_ask_field').fadeIn();
            }
        },
        _showQbox: function ($widget, $title) {
            if ($widget.options.showFullAnswer == '1') {
                if ($title.hasClass('active')) {
                    $title.removeClass('active');
                    $('.product_faq_li[faq_id="' + $title.attr('faq_id') + '"]')
                    .html('<i class="fa fa-caret-right" aria-hidden="true"></i>');
                    $('.product_faq_content[faq_id="' + $title.attr('faq_id') + '"]').fadeOut();
                } else {
                    $title.addClass('active');
                    $('.product_faq_li[faq_id="' + $title.attr('faq_id') + '"]')
                    .html('<i class="fa fa-caret-down" aria-hidden="true"></i>');
                    $('.product_faq_content[faq_id="' + $title.attr('faq_id') + '"]').fadeIn();
                }
            } else {
                if (typeof $title.attr('url_key') !== typeof undefined && $title.attr('url_key') != false) {
                    window.location.href =
                    $widget.options.baseUrl + 'faqs/question/view/url/' + $title.attr('url_key');
                } else {
                    window.location.href =
                    $widget.options.baseUrl + 'faqs/question/view/id/' + $title.attr('faq_id');
                }
            }
        },
        _submit: function ($widget) {
            var canSubmit = true;
            $('.faq_ask_field_notify div').html('');
            if ($('#faq_ask_user').val().replace(/ /g, '') == '') {
                $('.faq_ask_field_notify div').append('<p>' + $widget.options.usernameValidation + '</p>');
                canSubmit = false;
            }
            if ($('#faq_ask_question').val().replace(/ /g, '').length < 10
                || $('#faq_ask_question').val().replace(/ /g, '').length > 255) {
                $('.faq_ask_field_notify div').append('<p>' + $widget.options.keywordValidation + '</p>');
                canSubmit = false;
            }
            if (canSubmit) {
                $.ajax({
                    type: 'post',
                    url: $widget.options.baseUrl + 'faqs/json/productfaqsubmit',
                    data: {
                        'question': $('#faq_ask_question').val(),
                        'username': $('#faq_ask_user').val(),
                        'product_id': $widget.options.productId,
                        'form_key': $.cookie('form_key')
                    },
                    dataType: 'json'
                }).done(function ($return) {
                    $('.faq_ask_field_notify div').html($return);
                    $('.faq_ask_field_notify').fadeIn();
                });
            } else {
                $('.faq_ask_field_notify').fadeIn();
            }
        }
    });
    return $.bss.product_faq;
});
