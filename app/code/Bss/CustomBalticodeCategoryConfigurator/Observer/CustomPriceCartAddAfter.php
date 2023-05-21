<?php
namespace Bss\CustomBalticodeCategoryConfigurator\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CustomPriceCartAddAfter implements ObserverInterface
{
    /**
     * CustomPriceCartAddAfter constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Balticode\CategoryConfigurator\Model\GlassShapeService $glassShapeService
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Balticode\CategoryConfigurator\Model\GlassShapeService $glassShapeService
    ) {
        $this->request = $request;
        $this->glassShapeService = $glassShapeService;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {

        $item = $observer->getEvent()->getData('quote_item');

        $stepsData = (array) $this->request->getPost();
        foreach ($stepsData as $selectedProducts) {
            if (is_array($selectedProducts) && !empty($selectedProducts)) {
                foreach ($selectedProducts as $stepProduct) {
                    if(isset($stepProduct['width']) && isset($stepProduct['height']) && $item->getProductId() == $stepProduct['product_id']) {
                        $stepProduct['custom_bss'] = true;
                        $qty = $this->glassShapeService->calculateQty($stepProduct);
                        $custom_price = $item->getProduct()->getFinalPrice() * $qty;
                        $item->setCustomPrice($custom_price);
                        $item->setOriginalCustomPrice($custom_price);
                        $item->getProduct()->setIsSuperMode(true);
                    }
                }
            }
        }
        return $this;
    }
}
