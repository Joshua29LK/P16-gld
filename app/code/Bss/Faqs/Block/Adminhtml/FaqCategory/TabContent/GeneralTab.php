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
namespace Bss\Faqs\Block\Adminhtml\FaqCategory\TabContent;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class GeneralTab extends AbstractAssignTab implements TabInterface
{
    /**
     * Add Image preview block
     * {@inheritdoc}
     */
    public function toHtml()
    {
        return parent::toHtml() . $this->getPreviewBlock();
    }

    /**
     * PrepareForm
     *
     * @return AbstractAssignTab
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $formValues = $this->getModel();

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('FAQ Category Details'),
                'collapsible'  => true
            ]
        );

        $fieldset->addField(
            'faq_category_id',
            'hidden',
            [
                'name' => 'faq_category_id',
                'value' => $formValues['faq_category_id']
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'label' => __('Category Title'),
                'title' => __('Category Title'),
                'name' => 'title',
                'required' => true,
                'value' => $formValues['title']
            ]
        );

        $fieldset->addField(
            'show_in_mainpage',
            'select',
            [
                'label' => __('Show in Main Page'),
                'title' => __('Show in Main Page'),
                'name' => 'show_in_mainpage',
                'options' => $this->getOptionYesnoProvider(),
                'value' => $formValues['show_in_mainpage']
            ]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'name' => 'url_key',
                'value' => $formValues['url_key']
            ]
        );

        $fieldset->addField(
            'faq_image',
            'file',
            [
                'label' => __('Category Image'),
                'title' => __('Category Image'),
                'name' => 'cate_uploader',
                'note' => __(
                    "Recommend uploading square image with width and height over 250px<br/>"
                    . "The size limit for uploading an image is 1200px x 1200px"
                )
            ]
        );

        $fieldset->addField(
            'oldImage',
            'hidden',
            [
                'value' => $formValues['image'],
                'name' => 'old_image'
            ]
        );

        $fieldset->addField(
            'delimage',
            'hidden',
            [
                'name' => 'del_image',
                'value' => '0'
            ]
        );

        $fieldset->addField(
            'title_color',
            'text',
            [
                'label' => __('Title Color'),
                'title' => __('Title Color'),
                'name' => 'title_color',
                'required' => true,
                'class'  => 'jscolor {hash:true,refine:false}',
                'value' => ($formValues['title_color']) ? $formValues['title_color'] : '#000000',
                'note' => __('Using color format: "#000000" or "red"')
            ]
        );

        $fieldset->addField(
            'color',
            'text',
            [
                'label' => __('Background Color'),
                'title' => __('Background Color'),
                'name' => 'color',
                'required' => true,
                'class'  => 'jscolor {hash:true,refine:false}',
                'value' => ($formValues['color']) ? $formValues['color'] : '#cccccc',
                'note' => __('Using color format: "#cccccc" or "red"')
            ]
        );
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
