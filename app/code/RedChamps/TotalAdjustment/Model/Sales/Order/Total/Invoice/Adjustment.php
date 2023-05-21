<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\Sales\Order\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use RedChamps\TotalAdjustment\Model\ConfigReader;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class Adjustment extends AbstractTotal
{
    protected $adjustmentManager;

    protected $configReader;

    public function __construct(
        AdjustmentManager $adjustmentManager,
        ConfigReader $configReader
    ) {
        $this->adjustmentManager = $adjustmentManager;
        $this->configReader = $configReader;
        parent::__construct([]);
    }

    public function collect(Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $adjustments = $this->adjustmentManager->getPendingAdjustments($order);
        if ($adjustments) {
            $areAdjustmentsTaxInclusive = $this->configReader->areAdjustmentsTaxInclusive();
            foreach ($adjustments as $adjustmentNumber => $adjustment) {
                $amount = $adjustment['amount'];
                $baseAmount = (isset($adjustment['base_amount']) ? $adjustment['base_amount'] : $adjustment['amount']);
                if($areAdjustmentsTaxInclusive) {
                    $taxAdjustment = $order->getAdjustmentsTax() - $order->getAdjustmentsTaxInvoiced();
                    $baseTaxAdjustment = $order->getBaseAdjustmentsTax() - $order->getBaseAdjustmentsTaxInvoiced();
                    $amount = $amount-$taxAdjustment;
                    $baseAmount = $baseAmount-$baseTaxAdjustment;
                }
                $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseAmount);
            }
            $invoice->setAdjustments($this->adjustmentManager->encodeAdjustments($adjustments));
        }
        return $this;
    }
}
