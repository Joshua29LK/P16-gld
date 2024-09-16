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

class RelatedAssignTab extends AbstractAssignTab implements TabInterface
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

        $relatedFieldset = $form->addFieldset(
            'related_fieldset',
            [
                'legend' => __('Related')
            ]
        );

        $faqGrid = $this->getLayout()->createBlock(
            \Bss\Faqs\Block\Adminhtml\Grid\FaqGrid::class,
            'faq.related.grid',
            [
                'data' =>
                [
                    'type' => 'related',
                    'source_id' => 'related_faq_id_old',
                    'field_id' => 'related_faq_id',
                    'faq_id' => $formValues['faq_id']
                ]
            ]
        );

        $relatedFieldset->addField(
            'related_faq_id_old',
            'hidden',
            [
                'name' => 'related_faq_id_old',
                'value' => $formValues['related_faq_id'],
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
        return __('Related Assign');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Related Assign');
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
