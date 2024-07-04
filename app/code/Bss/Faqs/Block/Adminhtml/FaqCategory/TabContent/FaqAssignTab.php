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
use Magento\Framework\Exception\LocalizedException;

class FaqAssignTab extends AbstractAssignTab implements TabInterface
{
    /**
     * PrepareForm
     *
     * @return FaqAssignTab
     * @throws LocalizedException
     */
    public function _prepareForm()
    {
        $formValues = $this->getModel();

        $form = $this->_formFactory->create();

        $relatedFieldset = $form->addFieldset(
            'faq_assign_fieldset',
            [
                'legend' => __('FAQs')
            ]
        );

        $faqGrid = $this->getLayout()->createBlock(
            \Bss\Faqs\Block\Adminhtml\Grid\FaqGrid::class,
            'category.faq.grid',
            [
                'data' =>
                [
                    'type' => 'category',
                    'source_id' => 'faq_id_oldvalue',
                    'field_id' => 'faq_id',
                    'faq_category_id' => $formValues['faq_category_id']
                ]
            ]
        );

        $relatedFieldset->addField(
            'faq_id_oldvalue',
            'hidden',
            [
                'name' => 'faq_id_oldvalue',
                'value' => $formValues['faq_id'],
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
        return __('FAQ Assign');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('FAQ Assign');
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
