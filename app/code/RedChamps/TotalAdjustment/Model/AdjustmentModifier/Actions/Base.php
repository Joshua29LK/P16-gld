<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\AdjustmentModifier\Actions;

use RedChamps\TotalAdjustment\Model\AdjustmentManager;
use RedChamps\TotalAdjustment\Model\ConfigReader;

abstract class Base
{
    protected $configReader;

    protected $adjustmentManager;

    public function __construct(
        ConfigReader $configReader,
        AdjustmentManager $adjustmentManager
    ) {
        $this->configReader = $configReader;
        $this->adjustmentManager = $adjustmentManager;
    }

    protected function validateTitle($order, $title)
    {
        if ($order->getAdjustments()) {
            $adjustments = $this->adjustmentManager->decodeAdjustments($order->getAdjustments());
            foreach ($adjustments as $adjustment) {
                if ($title == $adjustment["title"]) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function calculateTax($order, $amount)
    {
        $taxPercent = $this->getTaxRate($order);
        $taxAmount = 0;
        if ($taxPercent) {
            $taxAmount = $this->configReader->calculateTax($amount, $taxPercent);
        }
        return $taxAmount;
    }

    protected function getTaxRate($order)
    {
        $taxPercent = (float)$order->getAdjustmentsTaxPercentage();
        $taxAmount = (float)$order->getTaxAmount();
        if (!$taxPercent && $taxAmount) {
            $taxPercent = $this->configReader->getTaxRate($order);
            $order->setAdjustmentsTaxPercentage($taxPercent);
        }
        return $taxPercent;
    }

    protected function calculateBaseAmount($amount, $store)
    {
        return $this->configReader->convertToBaseCurrency($amount, $store);
    }
}
