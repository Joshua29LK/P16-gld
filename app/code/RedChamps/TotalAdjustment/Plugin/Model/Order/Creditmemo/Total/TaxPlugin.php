<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Plugin\Model\Order\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\Tax;

class TaxPlugin
{
    public function aroundCollect(Tax $subject, callable $proceed, Creditmemo $creditmemo)
    {
        $result = $proceed($creditmemo);

        $order = $creditmemo->getOrder();

        $taxAdjustment = $order->getAdjustmentsTaxInvoiced() - $order->getAdjustmentsTaxRefunded();
        $baseTaxAdjustment = $order->getBaseAdjustmentsTaxInvoiced() - $order->getBaseAdjustmentsTaxRefunded();

        if($order->getAdjustmentsTaxInvoiced() > 0 && $order->getBaseAdjustmentsTaxInvoiced() > 0) {
            $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $taxAdjustment);
            $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTaxAdjustment);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $taxAdjustment);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTaxAdjustment);
        }

        $creditmemo->setAdjustmentsTax($taxAdjustment);
        $creditmemo->setBaseAdjustmentsTax($baseTaxAdjustment);

        return $result;
    }
}
