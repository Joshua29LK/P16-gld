<?php
namespace Bss\CustomBiztechDeliverydate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveEAToOrderObserver implements ObserverInterface
{
    public function execute(EventObserver $observer)
    {
        try {
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $order->setIsFastestDate($quote->getIsFastestDate());
            return $this;
        } catch (\Exception $e) {
            return $this;
        }
    }
}
