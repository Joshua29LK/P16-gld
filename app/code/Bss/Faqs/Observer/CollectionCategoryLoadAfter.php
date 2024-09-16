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
namespace Bss\Faqs\Observer;

use Magento\Framework\Event\ObserverInterface;
use Bss\Faqs\Helper\Data as HelperData;

class CollectionCategoryLoadAfter implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CollectionCategoryLoadAfter constructor.
     * @param HelperData $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        HelperData $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();

        if ($collection instanceof \Bss\Faqs\Model\ResourceModel\FaqCategory\Collection) {
            foreach ($collection as $category) {
                if ($frontendLabel = $category->getFrontendLabel()) {
                    $storeLabels = $this->helperData->returnJson()->unserialize($frontendLabel);
                    $storeId = $this->storeManager->getStore()->getId();
                    $this->setTitle($storeLabels, $storeId, $category);
                }
            }
        }
        if ($collection instanceof \Bss\Faqs\Model\ResourceModel\Faqs\Collection) {
            foreach ($collection as $faqs) {
                if ($frontendLabel = $faqs->getFrontendLabel()) {
                    $storeLabels = $this->helperData->returnJson()->unserialize($frontendLabel);
                    $storeId = $this->storeManager->getStore()->getId();
                    $this->setTitle($storeLabels, $storeId, $faqs);
                }
            }
        }
        return $this;
    }

    /**
     * Set title
     *
     * @param array $storeLabels
     * @param int $storeId
     * @param mixed $item
     */
    protected function setTitle($storeLabels, $storeId, $item)
    {
        if (isset($storeLabels[$storeId])) {
            if ($storeLabels[$storeId] != "") {
                $item->setTitle($storeLabels[$storeId]);
            }
        }
    }
}
