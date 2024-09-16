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

class CategoryAssignTab extends AbstractAssignTab implements TabInterface
{
    /**
     * Prepare form
     *
     * @return CategoryAssignTab
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareForm()
    {
        $formValues = $this->getModel();

        $form = $this->_formFactory->create();

        $categoryAssignFieldset = $form->addFieldset(
            'category_assign_fieldset',
            [
                'legend' => __('Category Assign')
            ]
        );

        $categoryGrid = $this->getLayout()->createBlock(
            \Bss\Faqs\Block\Adminhtml\Grid\CategoryGrid::class,
            'category.grid',
            [
                'data' =>
                [
                    'field_id' => 'category_id',
                    'source_id' => 'category_id_oldvalue',
                    'faq_id' => $formValues['faq_id']
                ]
            ]
        );

        $categoryAssignFieldset->addField(
            'category_id_oldvalue',
            'hidden',
            [
                'name' => 'category_id_oldvalue',
                'value' => $formValues['category_id']
            ]
        )->setRenderer($categoryGrid);

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
        return __('Category Assign');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Category Assign');
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
