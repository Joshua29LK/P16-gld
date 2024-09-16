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

use Bss\Faqs\Helper\Data as HelperData;
use Magento\Framework\Event\ObserverInterface;

class CategoryLoadAfter implements ObserverInterface
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
        $category = $observer->getDataObject();
        if ($frontendLabel = $category->getFrontendLabel()) {
            $storeLabels = $this->helperData->returnJson()->unserialize($frontendLabel);
            $storeId = $this->storeManager->getStore()->getId();
            if (isset($storeLabels[$storeId])) {
                if ($storeLabels[$storeId] != "") {
                    $category->setTitle($storeLabels[$storeId]);
                }

            }
        }
        return $this;
    }
}
