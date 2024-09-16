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

class ProductAssignTab extends AbstractAssignTab implements TabInterface
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

        $productFieldset = $form->addFieldset(
            'product_fieldset',
            [
                'legend' => __('Product')
            ]
        );

        $productFieldset->addField(
            'is_check_all_product',
            'select',
            [
                'label' => __('Assign All Products'),
                'title' => __('Assign All Products'),
                'name' => 'is_check_all_product',
                'options' => $this->getOptionYesnoProvider(),
                'value' => $formValues['is_check_all_product']
            ]
        );

        $faqGrid = $this->getLayout()->createBlock(
            \Bss\Faqs\Block\Adminhtml\Grid\ProductGrid::class,
            'faq.product.grid',
            [
                'data' =>
                [
                    'dependence' => 'is_check_all_product',
                    'field_id' => 'product_id',
                    'source_id' => 'product_id_old',
                    'faq_id' => $formValues['faq_id']
                ]
            ]
        );

        $productFieldset->addField(
            'product_id_old',
            'hidden',
            [
                'name' => 'product_id',
                'value' => $formValues['product_id']
            ]
        )->setRenderer($faqGrid);

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
        return __('Product Assign');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Product Assign');
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
