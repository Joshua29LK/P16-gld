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
    'mage/translate',
    "mage/cookies"
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('bss.faq_question', {
        _create: function () {
            var $widget = this;
            $widget._updateFaqContent($widget);
            $widget.element.on('click', '.category_title_block', function () {
                if (typeof $(this).attr('url_key') !== typeof undefined && $(this).attr('url_key') != false) {
                    window.location.href =
                        $widget.options.baseUrl + 'faqs/category/view/url/' + $(this).attr('url_key');
                } else {
                    window.location.href =
                        $widget.options.baseUrl + 'faqs/category/view/id/' + $(this).attr('category_id');
                }
            });
            $widget.element.on('click', '.faq_question .title', function () {
                return $widget._expandQuestion($(this).parents('.faq_question'));
            });
            $('#faq_sortby').change(function () {
                return $widget._updateSortOrder($(this));
            });
        },
        _updateFaqContent: function ($widget) {
            $.ajax({
                type: 'post',
                url: $widget.options.baseUrl + $widget.options.controller,
                data: {
                    'type': $widget.options.paramtype,
                    'value': $widget.options.paramvalue,
                    'form_key': $.mage.cookies.get('form_key')
                },
                dataType: 'json'
            }).done(function ($data) {
                if ($widget.options.controller === 'faqs/json/index') {
                    $widget._updateMainContent($data['main_content']);
                } else {
                    $widget._updateCateContent($data['main_content']);
                }
                $widget._updateSidebar($data['sidebar']);
            });
        },
        _updateSortOrder: function ($field) {
            var $widget = this;
            $.ajax({
                type: 'post',
                url: $widget.options.baseUrl + $widget.options.controller,
                data: {
                    'type': $widget.options.paramtype,
                    'value': $widget.options.paramvalue,
                    'sort_order': $field.val()
                },
                dataType: 'json'
            }).done(function ($data) {
                $widget._updateCateContent($data['main_content']);
            });
        },
        _expandQuestion: function ($faqElement) {
            var $categoryColor = this.element.find(
                '.category_title_block[category_id="' + $faqElement.attr('category_id') + '"]'
            ).css('color');
            if ($faqElement.hasClass('expanding')) {
                $faqElement.parents('li').removeClass('expanding');
                this.element.find('.faq_question_expand[faq_id="' + $faqElement.attr('faq_id') + '"][category_id="'
                    + $faqElement.attr('category_id') + '"]').removeClass('expanding');
                $faqElement.removeClass('expanding').css('color', '#505050');
            } else {
                if (this.options.questionDisplay == 'collapse') {
                    var $parentCategory = $faqElement.parents('.faqs_mainpage_category_block');
                    $parentCategory.find('.faq_question').css('color', '#505050');
                    $faqElement.parents('li').addClass('expanding');
                    this.element.find('.faq_question_expand[faq_id="' + $faqElement.attr('faq_id') + '"][category_id="'
                        + $faqElement.attr('category_id') + '"]').addClass('expanding');
                    $faqElement.addClass('expanding').css('color', $categoryColor);
                } else {
                    if ($faqElement.attr('url_key') !== "") {
                        window.location.href =
                            this.options.baseUrl + 'faqs/question/view/category/'
                            + $faqElement.attr('category_id') + '/url/' + $faqElement.attr('url_key');
                    } else {
                        window.location.href =
                            this.options.baseUrl + 'faqs/question/view/category/'
                            + $faqElement.attr('category_id') + '/id/' + $faqElement.attr('faq_id');
                    }
                }
            }
        },
        _updateMainContent: function ($data) {
            var $widget = this;
            $($widget.options.block).html('');
            $.each($data, function (index1, $category) {
                var content =
                    '<div class="faqs_mainpage_category_block" category_id="<%- data.faq_category_id %>"'
                    + ' category-color="<%- data.color %>">'
                    + '<div class="faqs_mainpage_category_image" category_id="<%- data.faq_category_id %>"'
                    + ' style="background-color: <%- data.color %>; box-shadow: 0px 5px 24px <%- data.color %>;">'
                    + '<img alt="" src="<%- data.image %>" />'
                    + '</div>'
                    + '<div class="category_block_info">'
                    + '<div class="category_title_block" url_key="<%- data.url_key %>"'
                    + ' category_id="<%- data.faq_category_id %>" style="color: <%- data.title_color %>">'
                    + '<span><%- data.title %></span>'
                    + '</div>'
                    + '</div>'
                    + '<div class="faqs_mainpage_question_list" category-color="<%- data.color %>">'
                    + '<ul></ul>'
                    + '</div>'
                    + '</div>';
                $category['title'] = $category['title'].toUpperCase();
                $($widget.options.block).append(mageTemplate(content, {data: $category}));

                var $categoryElement = $('.faqs_mainpage_category_block[category_id="' + $category['faq_category_id'] + '"]');
                $.each($category['faq'], function (index2, $question) {
                    var questionContent =
                        '<li>'
                        + '<div class="faq_question" faq_id="<%- question.faq_id %>"'
                        + ' url_key="<%- question.url_key %>" category_id="<%- category.faq_category_id %>">'
                        + '<div class="title"><%- question.title %></div>'
                        + '<div class="faq_question_expand" faq_id="<%- question.faq_id %>"'
                        + ' category_id="<%- category.faq_category_id %>">'
                        + '<div><a class="faq-url" href="<%- questionUrl %>">'
                        + '<%- seeMoreText %>'
                        + '</a></div>'
                        + '<p class="created-info">'
                        + '<%- createByText %><%- question.customer %><%- onText %><%- question.time %>'
                        + '</p>'
                        + '</div>'
                        + '</div>'
                        + '</li>';
                    $categoryElement.find('ul').append(
                        mageTemplate(
                            questionContent,
                            {
                                question: $question,
                                category: $category,
                                questionUrl: $widget._getQuestionUrl(
                                    $question['url_key'],
                                    $question['faq_id'],
                                    $category['faq_category_id']
                                ),
                                createByText: $.mage.__('Created by '),
                                onText: $.mage.__(' on: '),
                                seeMoreText: $.mage.__('See more')
                            }
                        )
                    );
                    $categoryElement.find('.faq_question_expand[faq_id="' + $question['faq_id'] + '"]')
                        .prepend($question['short_answer']);
                });
            });
            if ($widget.options.questionDisplay == 'expand') {
                $('.faq_question_expand').addClass('expanding');
            }
            if ($widget.options.controller === 'faqs/json/category') {
                $('.breadcrumbs').find('.faq_category').find('strong').html($data[0]['title']);
            }
        },
        _getQuestionUrl: function ($url, $id, $cateId) {
            if ($cateId !== null) {
                if ($url) {
                    return (this.options.baseUrl + 'faqs/question/view/category/' + $cateId + '/url/' + $url);
                } else {
                    return (this.options.baseUrl + 'faqs/question/view/category/' + $cateId + '/id/' + $id);
                }
            } else {
                if ($url) {
                    return (this.options.baseUrl + 'faqs/question/view/url/' + $url);
                } else {
                    return (this.options.baseUrl + 'faqs/question/view/id/' + $id);
                }
            }
        },
        _updateCateContent: function ($data) {
            var $widget = this,
                $category = $data[0],
                categoryContent =
                    '<div class="faqs_mainpage_category_block" category_id="<%- data.faq_category_id %>"'
                    + ' category-color="<%- data.color %>">'
                    + '<% if (data.image) { %>'
                    + '<div class="faqs_catepage_category_image" category_id="<%- data.faq_category_id %>"'
                    + ' style="background-color: <%- data.color %>; box-shadow: 0px 5px 24px <%- data.color %>;">'
                    + '<img alt="" src="<%- data.image %>" />'
                    + '</div>'
                    + '<% } %>'
                    + '<div class="catepage_title_block" url_key="<%- data.url_key %>"'
                    + ' category_id="<%- data.faq_category_id %>" style="color: <%- data.title_color %>">'
                    + '<span><%- data.title %></span>'
                    + '</div>'
                    + '<div class="catepage_block_info">'
                    + '<div class="faqs_catepage_question_list" category-color="<%- data.color %>">'
                    + '<ul></ul>'
                    + '</div>'
                    + '</div>'
                    + '</div>';
            $category['title'] = $category['title'].toUpperCase()
            $($widget.options.block).html('');
            $($widget.options.block).append(
                mageTemplate(categoryContent, {
                    data: $category
                })
            );
            var $categoryElement = $(
                '.faqs_mainpage_category_block[category_id=' + $category['faq_category_id'] + ']'
                ),
                questionContent =
                    '<li>'
                    + '<div class="faq_question" faq_id="<%- question.faq_id %>"'
                    + ' url_key="<%- question.url_key %>" category_id="<%- category.faq_category_id %>">'
                    + '<div class="title"><%- question.title %></div>'
                    + '<div class="faq_question_expand" faq_id="<%- question.faq_id %>"'
                    + ' category_id="<%- category.faq_category_id %>">'
                    + '<div><a class="faq-url" href="<%- questionUrl %>">'
                    + '<%- seeMoreText %>'
                    + '</a></div>'
                    + '<p class="created-info">'
                    + '<%- createByText %><%- question.customer %><%- onText %><%- question.time %>'
                    + '</p>'
                    + '</div>'
                    + '</div>'
                    + '</li>';
            $.each($category['faq'], function (index2, $question) {
                $categoryElement.find('ul').append(
                    mageTemplate(
                        questionContent,
                        {
                            question: $question,
                            category: $category,
                            questionUrl: $widget._getQuestionUrl(
                                $question['url_key'],
                                $question['faq_id'],
                                $category['faq_category_id']
                            ),
                            createByText: $.mage.__('Created by '),
                            onText: $.mage.__(' on: '),
                            seeMoreText: $.mage.__('See more')
                        }
                    )
                );
                $categoryElement.find('.faq_question_expand[faq_id="' + $question['faq_id'] + '"]')
                    .prepend($question['short_answer']);
            });
            if ($widget.options.questionDisplay == 'expand') {
                $('.faq_question_expand').addClass('expanding');
            }
            if ($widget.options.controller === 'faqs/json/category') {
                $('.breadcrumbs').find('.faq_category').find('strong').html($data[0]['title']);
            }
        },
        _updateSidebar: function ($sidebarData) {
            var $widget = this;
            $('.faqs_sidebar_tag_list').html('');
            $('.faqs_sidebar_data.most_faq ul').html('');
            $('.faqs_sidebar_data.categories ul').html('');
            if ($sidebarData['tag'].length > 0) {
                $.each($sidebarData['tag'], function (index, $tag) {
                    $('.faqs_sidebar_tag_list').append(
                        mageTemplate('<div class="faqs_sidebar_tag" tag="<%- tag1 %>"><span><%- tag2 %></span></div>' ,{
                            tag1: $tag,
                            tag2: $tag.toUpperCase()
                        })
                    );
                });
            } else {
                $('.faqs_sidebar_tag_list').parent('.faqs_sidebar_menu').css('display', 'none');
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
                $('.faqs_sidebar_data.categories').parent('.faqs_sidebar_menu').css('display', 'none');
            }

            if ($sidebarData['most_faq'].length > 0) {
                var mostFaqContent =
                    '<li>'
                    + '<div class="faqs_sidebar_item sidebar_most_faq" faq_id="<%- question.faq_id %>"'
                    + ' url_key="<%- question.url_key %>">'
                    + '<%- question.title %>'
                    + '</div>'
                    + '<div class="faqs_sidebar_item_expand short_answer" faq_id="<%- question.faq_id %>">'
                    + '<div class="question-short-answer"></div>'
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
                            questionUrl: $widget._getQuestionUrl(
                                mostFaq['url_key'],
                                mostFaq['faq_id'],
                                null
                            ),
                            createByText: $.mage.__('Created by '),
                            onText: $.mage.__(' on: '),
                            seeMoreText: $.mage.__('See more')
                        })
                    );
                    $('.short_answer[faq_id="' + mostFaq.faq_id + '"] .question-short-answer').html(mostFaq.short_answer);
                });
            } else {
                $('.faqs_sidebar_data.most_faq').parent('.faqs_sidebar_menu').css('display', 'none');
            }
            $('.faq-spinner').remove();
        }
    });
    return $.bss.faq_question;
});
