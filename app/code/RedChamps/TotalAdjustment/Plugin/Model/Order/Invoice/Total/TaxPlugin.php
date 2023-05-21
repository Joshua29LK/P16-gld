<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Plugin\Model\Order\Invoice\Total;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\Tax;

class TaxPlugin
{
    public function aroundCollect(Tax $subject, callable $proceed, Invoice $invoice)
    {
        $result = $proceed($invoice);

        $order = $invoice->getOrder();

        $taxAdjustment = $order->getAdjustmentsTax() - $order->getAdjustmentsTaxInvoiced();
        $baseTaxAdjustment = $order->getBaseAdjustmentsTax() - $order->getBaseAdjustmentsTaxInvoiced();

        if ($order->getAdjustmentsTax() > 0 & $order->getBaseAdjustmentsTax() > 0) {
            $invoice->setTaxAmount($invoice->getTaxAmount()+$taxAdjustment);
            $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount()+$baseTaxAdjustment);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $taxAdjustment);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTaxAdjustment);
        }

        $invoice->setAdjustmentsTax($taxAdjustment);
        $invoice->setBaseAdjustmentsTax($baseTaxAdjustment);

        return $result;
    }
}
