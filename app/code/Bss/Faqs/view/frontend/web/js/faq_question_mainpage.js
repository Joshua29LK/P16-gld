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
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co+ ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'mage/translate'
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('bss.faq_question_mainpage', {
        _create: function () {
            var $widget = this;
            $widget._updateFaqContent($widget);

            $widget.element.on('click', '.faqs_mainpage_question_image', function () {
                if ($(this).attr('category-id') != "") {
                    window.location.href =
                    $widget.options.baseUrl + 'faqs/category/view/id/' + $(this).attr('category-id');
                }
            });
        },
        _updateFaqContent: function ($widget) {
            $.ajax({
                type: 'post',
                url: $widget.options.baseUrl + 'faqs/json/question',
                data: {
                    'type': $widget.options.paramtype,
                    'value': $widget.options.paramvalue,
                    'category_id': $widget.options.paramcategory
                },
                dataType: 'json'
            }).done(function ($data) {
                $widget._updateMainContent($data);
                $widget._updateSidebar($data['related_faq_id']);
            });
        },
        _updateMainContent: function ($question) {
            if ($question['cate_info']['image'] === "") {
                $question['cate_info']['image'] = this.options.nopicURL;
                $question['cate_info']['color'] = '#1E9E9E';
                $question['cate_info']['title_color'] = '#1E9E9E';
            }
            var content =
            '<div class="faqs_mainpage_question_image"'
                + 'category-id="<%- data.cate_info.id %>"'
                + 'style="background-color: <%- data.cate_info.color %>; box-shadow: 0px 5px 24px <%- data.cate_info.color %>;">'
                + '<img alt="" src="<%- data.cate_info.image %>" />'
            + '</div>'
            + '<div class="question_block_info">'
                + '<div class="question_title_block" style="color: <%- data.cate_info.title_color %>">'
                    + '<span><%- data.title %></span>'
                + '</div>'
                + '<div class="question_questioner_block">'
                    + '<%- createByText %><span><%- data.customer %></span>'
                + '</div>'
                + '<div class="question_time_block">'
                    + '<span><%- onText %><%- data.time %></span>'
                + '</div>'
                + '<div class="question_answer_block">'
                    + '<span></span>'
                + '</div>'
            + '</div>';
            $(this.options.block).html(
                mageTemplate(content, {
                    data: $question,
                    createByText: $.mage.__('Created by '),
                    onText: $.mage.__(' On: '),
                })
            );
            $('.question_answer_block span').prepend($question['answer']);
            $('.faq_tag_block i').remove();

            if ($question['tag'] !== null) {
                $.each($question['tag'].split(','), function (index, $tag) {
                    if ($tag != "") {
                        $('.faq_tag_block').append(
                            mageTemplate(
                                '<div class="faqs_tag" tag_word="<%- tag1 %>"><div><%- tag2 %></div></div>',
                                {tag1: $tag, tag2: $tag.toUpperCase()}
                            )
                        );
                    }
                });
            }
            $('.breadcrumbs').find('.faq_question_detail').find('strong').html($question['title']);
            if ($question['cate_info']['title'] !== "") {
                $('.breadcrumbs').find('.faq_category').find('a').html($question['cate_info']['title']);
            }
        },
        _updateSidebar: function ($data) {
            var $widget = this;
            if ($($data).size() > 0) {
                $('.faqs_sidebar_data').append('<ul></ul>');
                if ($data != null) {
                    var liHtml =
                    '<li>'
                        + '<div class="faqs_sidebar_item sidebar_most_faq" faq_id="<%- data.faq_id %>" url="<%- data.url_key %>">'
                            + '<%- data.title %>'
                        + '</div>'
                        + '<div class="faqs_sidebar_item_expand short_answer" faq_id="<%- data.faq_id %>">'
                            + '<div class="question-short-answer"></div>'
                            + '<div>'
                                + '<a class="faq-url" href="<%- url %>"><%- seeMoreText %></a>'
                            + '</div>'
                            + '<p class="created-info">'
                                + '<%- createByText %><%- data.customer %><%- onText %><%- data.time %>'
                            + '</p>'
                        + '</div>'
                    + '</li>';
                    $.each($data, function (index, $faq) {
                        $('.faqs_sidebar_data ul').append(
                            mageTemplate(liHtml, {
                                data: $faq,
                                url: $widget._getFaqUrl($faq['url_key'], $faq['faq_id']),
                                createByText: $.mage.__('Created by '),
                                onText: $.mage.__(' on: '),
                                seeMoreText: $.mage.__('See more')
                            })
                        );
                        $('.short_answer[faq_id="' + $faq.faq_id + '"] .question-short-answer').html($faq.short_answer);
                    });
                }
            } else {
                $('.faqs_sidebar_menu').css('display', 'none');
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
    return $.bss.faq_question_mainpage;
});
