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
namespace Bss\Faqs\Block\Adminhtml\FaqCategory;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
use Magento\Framework\Exception\LocalizedException;

class Tab extends WidgetTabs
{
    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('category_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('FAQ Category Infomation'));
    }

    /**
     * Prepare layout
     *
     * @return Tab|void
     * @throws LocalizedException
     */
    public function _prepareLayout()
    {
        $this->addTab(
            'base_category',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    \Bss\Faqs\Block\Adminhtml\FaqCategory\TabContent\GeneralTab::class
                )->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'faq_assign',
            [
                'label' => __('FAQs Assign'),
                'title' => __('FAQs Assign'),
                'content' => $this->getLayout()->createBlock(
                    \Bss\Faqs\Block\Adminhtml\FaqCategory\TabContent\FaqAssignTab::class
                )->toHtml(),
                'active' => false
            ]
        );
        $this->addTab(
            'faq_labels',
            [
                'label' => __('Manage Labels'),
                'title' => __('Manage Labels'),
                'content' => $this->getLayout()->createBlock(
                    \Bss\Faqs\Block\Adminhtml\FaqCategory\TabContent\Labels::class
                )->toHtml(),
                'active' => false
            ]
        );
    }
}
