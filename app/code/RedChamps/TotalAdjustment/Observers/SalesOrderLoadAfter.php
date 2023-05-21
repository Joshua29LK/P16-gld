<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderExtension;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class SalesOrderLoadAfter implements ObserverInterface
{
    protected $orderExtension;

    protected $adjustmentManager;

    public function __construct(
        OrderExtension $orderExtension,
        AdjustmentManager $adjustmentManager
    ) {
        $this->orderExtension = $orderExtension;
        $this->adjustmentManager = $adjustmentManager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $attr = $order->getData('adjustments');
        if ($attr && $adjustments = $this->adjustmentManager->decodeAdjustments($attr)) {
            $extensionAttributes = $order->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->orderExtension;
            }
            $extensionAttributes->setAdjustments($adjustments);
            $order->setExtensionAttributes($extensionAttributes);
        }
    }
}
