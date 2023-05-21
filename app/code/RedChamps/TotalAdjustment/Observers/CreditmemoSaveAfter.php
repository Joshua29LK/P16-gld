<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class CreditmemoSaveAfter implements ObserverInterface
{
    protected $adjustmentManager;

    /**
     * CreditmemoSaveAfter constructor.
     * @param AdjustmentManager $adjustmentManager
     */
    public function __construct(
        AdjustmentManager $adjustmentManager
    )
    {
        $this->adjustmentManager = $adjustmentManager;
    }

    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo->getAdjustments()) {
            $order = $creditmemo->getOrder();
            $order->setAdjustmentsRefunded(
                $this->adjustmentManager->mergeAdjustments(
                    $creditmemo->getAdjustments(),
                    $order->getAdjustmentsRefunded()
                )
            );
            $order->setAdjustmentsTaxRefunded(
                $order->getAdjustmentsTaxRefunded() + $creditmemo->getAdjustmentsTax()
            );
            $order->setBaseAdjustmentsTaxRefunded(
                $order->getBaseAdjustmentsTaxRefunded() + $creditmemo->getBaseAdjustmentsTax()
            );
            $order->save();
        }
        return $this;
    }
}
