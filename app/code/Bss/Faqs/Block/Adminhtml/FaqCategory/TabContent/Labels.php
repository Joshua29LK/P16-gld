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

use Bss\Faqs\Model\FaqCategory;
use Magento\Backend\Block\Template;
use Bss\Faqs\Helper\Data as HelperData;

class Labels extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var HelperData $helperData
     */
    protected $helperData;

    /**
     * @var FaqCategory
     */
    protected $faqCategory;

    /**
     * @var string
     */
    protected $_template = 'Magento_Catalog::catalog/product/attribute/labels.phtml';

    /**
     * Labels constructor.
     * @param Template\Context $context
     * @param FaqCategory $faqCategory
     * @param \Magento\Framework\Registry $registry
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Bss\Faqs\Model\FaqCategory $faqCategory,
        \Magento\Framework\Registry $registry,
        HelperData $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->faqCategory = $faqCategory;
        $this->registry = $registry;
        $this->helperData = $helperData;
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return \Magento\Store\Model\ResourceModel\Store\Collection
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $this->setData('stores', $this->_storeManager->getStores());
        }
        return $this->_getData('stores');
    }

    /**
     * Retrieve frontend labels of attribute for each store
     *
     * @return array
     */
    public function getLabelValues()
    {
        $values = [];
        if ($frontendLabel = $this->getAttributeObject()->getFrontendLabel()) {
            $storeLabels = $this->helperData->returnJson()->unserialize($frontendLabel);
            foreach ($this->getStores() as $store) {
                if ($store->getId() != 0) {
                    $label = isset($storeLabels[$store->getId()]) ? $storeLabels[$store->getId()] : '';
                    $values[$store->getId()] = $label;
                }
            }
        } else {
            foreach ($this->getStores() as $store) {
                if ($store->getId() != 0) {
                    $values[$store->getId()] = '';
                }
            }
        }
        return $values;
    }

    /**
     * Get attribute object
     *
     * @return FaqCategory
     */
    private function getAttributeObject()
    {
        return $this->faqCategory->load($this->registry->registry('faq_category_id'));
    }
}
