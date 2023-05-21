<?php

namespace MageArray\Customprice\Observer;

use Magento\Framework\Event\ObserverInterface;

class Custompriceproduct implements ObserverInterface
{
    /**
     * [execute description]
     *
     * @param  \Magento\Framework\Event\Observer $observer [description]
     * @return [type]                                      [description]
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $csvPrice = $item->getProduct()->getCsvPrice();
        if($this->hasTierPrice($item->getProduct()) == false && !empty($csvPrice)){
            $item->setCustomPrice($item->getProduct()->getFinalprice());
            $item->setOriginalCustomPrice($item->getProduct()->getFinalprice());
            $item->getProduct()->setIsSuperMode(true);
        }
        
    }

    /**
     * check if product has Tier prices added
     *
     * @return bool
     */
    private function hasTierPrice($product) : bool{
        return count($product->getTierPrices());
    }
}

