<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * List of attributes that should be added to an order.
     *
     * @var array
     */
    private $attributes = [
        'adjustments',
        'adjustments_tax',
        'base_adjustments_tax',
        'adjustments_tax_percentage'
    ];

    /**

     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var \Magento\Quote\Model\Quote $quote */
        $quoteShippingAddress = $observer->getEvent()->getData('quote')->getShippingAddress();

        foreach ($this->attributes as $attribute) {
            if ($quoteShippingAddress->hasData($attribute)) {
                $order->setData($attribute, $quoteShippingAddress->getData($attribute));
            }
        }

        return $this;
    }
}
