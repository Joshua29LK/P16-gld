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

class InvoiceSaveAfter implements ObserverInterface
{
    protected $adjustmentManager;

    public function __construct(
        AdjustmentManager $adjustmentManager
    ) {
        $this->adjustmentManager = $adjustmentManager;
    }

    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getAdjustments()) {
            $order = $invoice->getOrder();
            $order->setAdjustmentsInvoiced(
                $this->adjustmentManager->mergeAdjustments(
                    $invoice->getAdjustments(),
                    $order->getAdjustmentsInvoiced()
                )
            );
            $order->setAdjustmentsTaxInvoiced(
                $order->getAdjustmentsTaxInvoiced() + $invoice->getAdjustmentsTax()
            );
            $order->setBaseAdjustmentsTaxInvoiced(
                $order->getBaseAdjustmentsTaxInvoiced() + $invoice->getBaseAdjustmentsTax()
            );
        }
        return $this;
    }
}
