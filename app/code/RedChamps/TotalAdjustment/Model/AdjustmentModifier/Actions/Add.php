<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\AdjustmentModifier\Actions;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Add extends Base
{
    /**
     * @param CartInterface|OrderInterface $entity
     * @param $adjustments
     * @return false|\Magento\Framework\Phrase|string
     * @throws LocalizedException
     */
    public function execute($entity, $adjustments)
    {
        $newAdjustments = [];
        $newGrandTotal = $entity->getGrandTotal();
        $newBaseGrandTotal = $entity->getBaseGrandTotal();
        $error = false;
        $adjustmentTitles = [];
        $message = "";
        $totalAdjustmentAmount = 0;
        $totalBaseAdjustmentAmount = 0;
        foreach ($adjustments as $adjustment) {
            if (!isset($adjustment['title']) || !isset($adjustment['amount'])) {
                throw new LocalizedException(
                    __("Invalid input format")
                );
            }
            $adjustmentTitles[] = $adjustment['title'];
            if (!$this->validateTitle($entity, $adjustment['title'])) {
                throw new LocalizedException(
                    __("An adjustment with this title '{$adjustment['title']}' already exists . Please try again with different title.")
                );
            }
            $amount = $adjustment['amount'];
            $baseAmount = $this->calculateBaseAmount($amount, $entity->getStoreId());
            $percentage = 0;
            $type = isset($adjustment['type']) ? $adjustment['type'] : "flat";
            if ($type == "percentage") {
                $percentage  = $amount;
                $total =  $newGrandTotal;
                $baseTotal = $newBaseGrandTotal;
                if ($this->configReader->areAdjustmentsBeforeTax()) {
                    $total = $total - $entity->getTaxAmount();
                    $baseTotal = $baseTotal - $entity->getBaseTaxAmount();
                }
                $amount = ($total*$percentage)/100;
                $baseAmount = ($baseTotal*$percentage)/100;
            }
            $newAdjustments[] = [
                "title" => $adjustment['title'],
                "amount" => $amount,
                "base_amount" => $baseAmount,
                "type" => $type,
                "percentage" => $percentage
            ];
            $totalAdjustmentAmount+= $amount;
            $totalBaseAdjustmentAmount+= $baseAmount;
            $newGrandTotal = $newGrandTotal + $amount;
            $newBaseGrandTotal = $newBaseGrandTotal + $baseAmount;
        }
        if (count($adjustmentTitles) != count(array_unique($adjustmentTitles))) {
            throw new LocalizedException(
                __("Validation Failed: Multiple adjustments exist with same title. Please modify the titles and retry.")
            );
        }
        if (!$error) {
            $newTax = $this->calculateTax($entity, $totalAdjustmentAmount);
            $newBaseTax = $this->calculateTax($entity, $totalBaseAdjustmentAmount);
            if (!$this->configReader->areAdjustmentsTaxInclusive()) {
                $newGrandTotal+=$newTax;
                $newBaseGrandTotal+=$newBaseTax;
            }

            if ($newGrandTotal >= 0 && $newBaseGrandTotal >= 0) {
                $entity->setTaxAmount($entity->getTaxAmount() + $newTax);
                $entity->setBaseTaxAmount($entity->getBaseTaxAmount() + $newBaseTax);

                $entity->setAdjustmentsTax($entity->getAdjustmentsTax()+$newTax);
                $entity->setBaseAdjustmentsTax($entity->getBaseAdjustmentsTax()+$newBaseTax);

                $entity->setGrandTotal($newGrandTotal);
                $entity->setBaseGrandTotal($newBaseGrandTotal);

                $oldAdjustments = $entity->getAdjustments() ?
                    $this->adjustmentManager->decodeAdjustments($entity->getAdjustments()) :
                    [];
                $adjustments =  array_merge($oldAdjustments, $newAdjustments);
                $entity->setAdjustments($this->adjustmentManager->encodeAdjustments($adjustments));
                $message = __('Total has been adjusted successfully.');
            } else {
                throw new LocalizedException(
                    __("After this adjustment amount grand total will have negative value which is not allowed . Please try again.")
                );
            }
        }
        return $message;
    }
}
