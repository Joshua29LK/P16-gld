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
    'mage/template'
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('bss.faq_search_result', {
        _create: function () {
            var $widget = this;
            $widget._updateData($widget);
            $widget.element.on('click', '.faq_question', function () {
                if (typeof $(this).attr('url_key') !== typeof undefined && $(this).attr('url_key') != false) {
                    window.location.href =
                    $widget.options.baseUrl + 'faqs/question/view/url/' + $(this).attr('url_key');
                } else {
                    window.location.href =
                    $widget.options.baseUrl + 'faqs/question/view/id/' + $(this).attr('faq_id');
                }
            });
            $('#faq_sortby').change(function () {
                return $widget._UpdateSortOrder($(this));
            });
        },
        _updateData: function ($widget) {
            $.ajax({
                type: 'post',
                url: $widget.options.baseUrl + $widget.options.controller,
                data: {
                    'category': $widget.options.category,
                    'keyword': $widget.options.keyword
                },
                dataType: 'json'
            }).done(function ($data) {
                $('.faq_message_block p').html($data['message']);
                if ($widget.options.controller === "faqs/json/tag") {
                    $widget._updateTagLabel($widget);
                }
                $widget._updateContent($data['result']);
                $widget._updateSidebar($data['sidebar']);
            });
        },
        _UpdateSortOrder: function ($field) {
            var $widget = this;
            $.ajax({
                type: 'post',
                url: $widget.options.baseUrl + $widget.options.controller,
                data: {
                    'category': $widget.options.category,
                    'keyword': $widget.options.keyword,
                    'sort_order': $field.val()
                },
                dataType: 'json'
            }).done(function ($data) {
                $widget._updateContent($data['result']);
            });
        },
        _updateTagLabel: function ($widget) {
            var tagList = $('.faq_message_block').find('span').html().split('~'),
                tagHtml =
                '<div class="closeable_tag">'
                    + '<%- data %>'
                    + '<div class="close" tag="<%- data %>">'
                        + '<i class="fa fa-times" aria-hidden="true"></i>'
                    + '</div>'
                + '</div>';
            if (tagList.length < 2) {
                return false;
            }
            $('.faq_message_block').find('span').html('');

            $.each(tagList, function ($index, $value) {
                $('.faq_message_block').find('span').append(
                    mageTemplate(tagHtml, {data: $value})
                );
            });
            $('.close').click(function () {
                var newTagList = new Array(),
                removeTag = $(this).attr('tag');
                $(this).parent('.closeable_tag').remove();
                $.each($widget.options.keyword.split('~'), function (index, tag) {
                    if (tag !== removeTag) {
                        newTagList.push(tag);
                    }
                });
                $widget.options.keyword = newTagList.join('~');
                window.history.replaceState('SDCP', 'SCDP', $widget.options.keyword);

                $($widget.options.block).html(
                    $('<div />', {class: 'main_block_loading'})
                    .append('<i class="fa fa-spinner fa-pulse" aria-hidden="true"></i>')
                );
                $.ajax({
                    type: 'post',
                    url: $widget.options.baseUrl + $widget.options.controller,
                    data: {
                        'category': $widget.options.category,
                        'keyword': $widget.options.keyword
                    },
                    dataType: 'json'
                }).done(function ($data) {
                    $('.faq_message_block p').html($data['message']);
                    if ($widget.options.controller === "faqs/json/tag") {
                        $widget._updateTagLabel($widget);
                    }
                    $widget._updateContent($data['result']);
                    $widget._updateSidebar($data['sidebar']);
                });
            });
        },
        _updateContent: function ($data) {
            var $widget = this;
            $($widget.options.block).html('<ul></ul>');
            var liHtml =
            '<li>'
                + '<div class="faq_question" faq_id="<%- data.faq_id %>" url_key="<%- data.url_key %>">'
                    + '<div><%- data.title %></div>'
                + '</div>'
            + '</li>';
            $.each($data, function (index, $question) {
                $($widget.options.block).find('ul').append(
                    mageTemplate(liHtml, {data: $question})
                );
            });
        },
        _updateSidebar: function ($sidebarData) {
            var $widget = this,
                $oldTag = "",
                $tagCode;
            $('.faqs_sidebar_tag_list').html('');
            $('.faqs_sidebar_data.most_faq ul').html('');
            $('.faqs_sidebar_data.categories ul').html('');
            if ($widget.options.controller === "faqs/json/tag") {
                $oldTag = "~" + $widget.options.keyword;
            }
            if ($sidebarData['tag'].length > 0) {
                $.each($sidebarData['tag'], function (index, $tag) {
                    if ($.inArray($tag, $widget.options.keyword.split(', ')) >= 0) {
                        $tagCode = $tag;
                    } else {
                        $tagCode = $tag + $oldTag
                    }
                    $('.faqs_sidebar_tag_list').append(
                        mageTemplate('<div class="faqs_sidebar_tag" tag="<%- tag1 %>"><span><%- tag2 %></span></div>' ,{
                            tag1: $tagCode,
                            tag2: $tag.toUpperCase()
                        })
                    );
                });
            } else {
                $('.faqs_sidebar_tag_list').parent('.faqs_sidebar_menu').hide();
            }

            if ($sidebarData['category'].length > 0) {
                var liHtml =
                '<li class="category_li">'
                    + '<div class="faqs_sidebar_item sidebar_category" cate_id="<%- cate.faq_category_id %>"'
                        + ' url_key="<%- cate.url_key %>">'
                        + '<div>'
                                + '<%- cate.title %>'
                        + '</div>'
                    + '</div>'
                + '</li>';
                $.each($sidebarData['category'], function (index, category) {
                    $('.faqs_sidebar_data.categories ul').append(
                        mageTemplate(liHtml, {cate: category})
                    );
                });
            } else {
                $('.faqs_sidebar_data.categories').parent('.faqs_sidebar_menu').hide();
            }

            if ($sidebarData['most_faq'].length > 0) {
                var mostFaqContent =
                '<li>'
                    + '<div class="faqs_sidebar_item sidebar_most_faq" faq_id="<%- question.faq_id %>"'
                        + ' url_key="<%- question.url_key %>">'
                                + '<%- question.title %>'
                        + '</div>'
                        + '<div class="faqs_sidebar_item_expand" faq_id="<%- question.faq_id %>">'
                            + '<%- question.short_answer %>'
                            + '<div><a class="faq-url" href="<%- questionUrl %>">'
                                + '<%- seeMoreText %>'
                            + '</a></div>'
                            + '<p class="created-info">'
                                + '<%- createByText %><%- question.customer %><%- onText %><%- question.time %>'
                            + '</p>'
                        + '</div>'
                    + '</div>'
                + '</li>';
                $.each($sidebarData['most_faq'], function (index, mostFaq) {
                    $('.faqs_sidebar_data.most_faq ul').append(
                        mageTemplate(mostFaqContent, {
                            question: mostFaq,
                            questionUrl: $widget._getFaqUrl(
                                mostFaq['url_key'],
                                mostFaq['faq_id']
                            ),
                            createByText: $.mage.__('Created by '),
                            onText: $.mage.__(' on: '),
                            seeMoreText: $.mage.__('See more')
                        })
                    );
                });
            } else {
                $('.faqs_sidebar_data.most_faq').parent('.faqs_sidebar_menu').hide();
            }
            $('.faq-spinner').remove();
        },
        _getFaqUrl: function ($url, $id) {
            if ($url == "") {
                return (this.options.baseUrl + 'faqs/question/view/id/' + $id);
            } else {
                return (this.options.baseUrl + 'faqs/question/view/url/' + $url);
            }
        }
    });
    return $.bss.faq_search_result;
});
