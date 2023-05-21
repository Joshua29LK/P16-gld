<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\InvoiceExtension;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class SalesInvoiceLoadAfter implements ObserverInterface
{
    protected $invoiceExtension;

    protected $adjustmentManager;

    public function __construct(
         InvoiceExtension $invoiceExtension,
        AdjustmentManager $adjustmentManager
    ) {
        $this->invoiceExtension = $invoiceExtension;
        $this->adjustmentManager = $adjustmentManager;
    }

    public function execute(Observer $observer)
    {
        $invoice = $observer->getInvoice();
        $extensionAttributes = $invoice->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->invoiceExtension;
        }
        $adjustments = $invoice->getData('adjustments');
        $adjustmentsInvoiced = $invoice->getData('adjustments_invoiced');
        $extensionAttributes->setAdjustments(
            $this->adjustmentManager->decodeAdjustments($adjustments)
        );
        $extensionAttributes->setAdjustmentsInvoiced(
            $this->adjustmentManager->decodeAdjustments($adjustmentsInvoiced)
        );
        $invoice->setExtensionAttributes($extensionAttributes);
    }
}
