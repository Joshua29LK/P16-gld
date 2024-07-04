<?php
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
namespace Bss\Faqs\Block\Adminhtml\Faqs\TabContent;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class GeneralTab extends AbstractAssignTab implements TabInterface
{
    /**
     * Prepare form
     *
     * @return AbstractAssignTab
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $formValues = $this->getModel();

        $form = $this->_formFactory->create();

        $generalFieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General')
            ]
        );
        $authorFieldset = $form->addFieldset(
            'author_fieldset',
            [
                'legend' => __('Author')
            ]
        );
        $seoFieldset = $form->addFieldset(
            'seo_fieldset',
            [
                'legend' => __('SEO')
            ]
        );
        $generalFieldset->addField(
            'faq_id',
            'hidden',
            [
                'name' => 'faq_id',
                'value' => $formValues['faq_id']
            ]
        );
        $generalFieldset->addField(
            'title',
            'text',
            [
                'label' => __('Title'),
                'title' => __('Title'),
                'name' => 'title',
                'required' => true,
                'value' => $formValues['title']
            ]
        );

        $generalFieldset->addField(
            'is_most_frequently',
            'select',
            [
                'label' => __('Most FAQ'),
                'title' => __('Most FAQ'),
                'name' => 'is_most_frequently',
                'options' => $this->getOptionYesnoProvider(),
                'value' => $formValues['is_most_frequently']
            ]
        );

        $generalFieldset->addField(
            'answer',
            'editor',
            [
                'label' => __('Answer'),
                'title' => __('Answer'),
                'name' => 'answer',
                'value' => $formValues['answer'],
                'rows' => '5',
                'cols' => '30',
                'wysiwyg' => true,
                'config' => $this->getWysiwygConfig(),
                'required' => true
            ]
        );

        $generalFieldset->addField(
            'is_show_full_answer',
            'select',
            [
                'label' => __('Show Full Answer'),
                'title' => __('Show Full Answer'),
                'name' => 'is_show_full_answer',
                'options' => $this->getOptionYesnoProvider(),
                'value' => $formValues['is_show_full_answer']
            ]
        );
        $generalFieldset->addField(
            'short_answer',
            'textarea',
            [
                'label' => __('Short Answer'),
                'title' => __('Short Answer'),
                'name' => 'short_answer',
                'value' => $formValues['short_answer'],
                'rows' => '5',
                'cols' => '30'
            ]
        );

        $generalFieldset->addField(
            'tag',
            'text',
            [
                'label' => __('Tag'),
                'title' => __('Tag'),
                'name' => 'tag',
                'value' => $formValues['tag'],
                'note' => __('Separate tag by ",": tag1,tag2,tag3,...')
            ]
        );

        $seoFieldset->addField(
            'url_key',
            'text',
            [
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'name' => 'url_key',
                'value' => $formValues['url_key']
            ]
        );

        $generalFieldset->addField(
            'use_real_vote_data',
            'select',
            [
                'label' => __('Using Real Voting Data'),
                'title' => __('Using Real Voting Data'),
                'name' => 'use_real_vote_data',
                'options' => $this->getOptionYesnoProvider(),
                'value' => $formValues['use_real_vote_data']
            ]
        );

        $generalFieldset->addField(
            'helpful_vote',
            'text',
            [
                'label' => __('A Number of Helpful Votes'),
                'title' => __('A Number of Helpful Votes'),
                'name' => 'helpful_vote',
                'class' => 'validate-not-negative-number',
                'value' => $formValues['helpful_vote']
            ]
        );
        $generalFieldset->addField(
            'unhelpful_vote',
            'text',
            [
                'label' => __('A Number of Unelpful Votes'),
                'title' => __('A Number of Unelpful Votes'),
                'name' => 'unhelpful_vote',
                'class' => 'validate-not-negative-number',
                'value' => $formValues['unhelpful_vote']
            ]
        );

        $authorFieldset->addField(
            'customer',
            'text',
            [
                'label' => __('Customer'),
                'title' => __('Customer'),
                'name' => 'customer',
                'required' => true,
                'value' => $formValues['customer']
            ]
        );

        $authorFieldset->addField(
            'time',
            'date',
            [
                'label' => __('Time Created'),
                'title' => __('Time Created'),
                'name' => 'time',
                'date_format' => 'Y-MM-dd',
                'class' => 'validate-date-iso',
                'value' => $formValues['time']
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Element\Dependence::class
            )
            ->addFieldMap(
                'use_real_vote_data',
                'use_real_vote_data'
            )
            ->addFieldMap(
                'helpful_vote',
                'helpful_vote'
            )
            ->addFieldMap(
                'unhelpful_vote',
                'unhelpful_vote'
            )
            ->addFieldDependence(
                'helpful_vote',
                'use_real_vote_data',
                '0'
            )
            ->addFieldDependence(
                'unhelpful_vote',
                'use_real_vote_data',
                '0'
            )
        );
        $this->_prepareStoreElement($generalFieldset);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Faq Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Faq Info');
    }

    /**
     * Can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is hidden
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }
}
