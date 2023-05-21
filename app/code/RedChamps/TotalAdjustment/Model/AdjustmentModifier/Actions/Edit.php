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

class Edit extends Base
{
    /**
     * @param CartInterface|OrderInterface $entity
     * @param $adjustments
     * @return false|\Magento\Framework\Phrase|string
     * @throws LocalizedException
     */
    public function execute($entity, $adjustments)
    {
        $existingAdjustments = $this->adjustmentManager->decodeAdjustments($entity->getAdjustments());
        if (isset($adjustments['adjustment_number'])) {
            if (isset($existingAdjustments[$adjustments['adjustment_number']])) {
                $result = $this->processAdjustmentEdit(
                    $existingAdjustments,
                    $adjustments['adjustment_number'],
                    $entity,
                    $adjustments['new_value']
                );
                $existingAdjustments = $result['existing_adjustments'];
                $entity = $result['entity'];
            } else {
                throw new LocalizedException(
                    __("Adjustment amount can't be found. Please try again.")
                );
            }
        } else {
            $existingAdjustmentTitles = [];
            foreach ($existingAdjustments as $adjustmentNumber => $adjustment) {
                $existingAdjustmentTitles[strtolower($adjustment['title'])] = $adjustmentNumber;
            }
            foreach ($adjustments as $adjustment) {
                if (!isset($adjustment['title']) || !isset($adjustment['amount'])) {
                    throw new LocalizedException(
                        __("Invalid input format")
                    );
                }
                $adjustmentTitle = strtolower($adjustment['title']);
                if (!isset($existingAdjustmentTitles[$adjustmentTitle])) {
                    throw new LocalizedException(
                        __("Adjustment amount can't be found with specified adjustment title '$adjustmentTitle'. Please try again.")
                    );
                }
                $adjustmentNumber = $existingAdjustmentTitles[$adjustmentTitle];
                $result = $this->processAdjustmentEdit(
                    $existingAdjustments,
                    $adjustmentNumber,
                    $entity,
                    $adjustment['amount']
                );
                $existingAdjustments = $result['existing_adjustments'];
                $entity = $result['entity'];
            }
        }
        $entity->setAdjustments($this->adjustmentManager->encodeAdjustments($existingAdjustments));
        return __('Adjustment amount has been updated successfully.');
    }

    /**
     * @param $existingAdjustments
     * @param $adjustmentNumber
     * @param $entity
     * @param $newAmount
     * @return mixed
     * @throws LocalizedException
     */
    protected function processAdjustmentEdit($existingAdjustments, $adjustmentNumber, $entity, $newAmount)
    {
        $oldValue = $existingAdjustments[$adjustmentNumber]["amount"];
        $oldBaseValue = isset($existingAdjustments[$adjustmentNumber]["base_amount"]) ? $existingAdjustments[$adjustmentNumber]["base_amount"] : $existingAdjustments[$adjustmentNumber]["amount"];
        //remove currency symbol
        $newValue = $newAmount;
        $newBaseValue = $this->calculateBaseAmount($newValue, $entity->getStoreId());
        $adjustmentTitle = $existingAdjustments[$adjustmentNumber]["title"];
        if ($invoicedAmount = $this->validateInvoiceAmount($entity, $adjustmentTitle, $newValue)) {
            throw new LocalizedException(
                __("Amount '$invoicedAmount' had been already invoiced for '$adjustmentTitle'. Please try value greater than '$invoicedAmount'")
            );
        } else {
            $valueDiff = $newValue - $oldValue;
            $baseValueDiff = $newBaseValue - $oldBaseValue;

            $taxUpdate = 0;
            $baseTaxUpdate = 0;

            if ((float)$entity->getAdjustmentsTax() || ($newValue > $oldValue && !(float)$entity->getAdjustmentsTax())) {
                $taxUpdate = $this->calculateTax($entity, $valueDiff);
                $baseTaxUpdate = $this->calculateTax($entity, $baseValueDiff);
            }

            $newGrandTotal = (($entity->getGrandTotal() - $oldValue) + $newValue);
            $newBaseGrandTotal = (($entity->getBaseGrandTotal() - $oldBaseValue) + $newBaseValue);

            if (!$this->configReader->areAdjustmentsTaxInclusive()) {
                $newGrandTotal+=$taxUpdate;
                $newBaseGrandTotal+=$baseTaxUpdate;
            }

            if ($newGrandTotal >= 0 && $newBaseGrandTotal >= 0) {
                $entity->setTaxAmount($entity->getTaxAmount() + $taxUpdate);
                $entity->setBaseTaxAmount($entity->getBaseTaxAmount() + $baseTaxUpdate);

                $entity->setAdjustmentsTax($entity->getAdjustmentsTax() + $taxUpdate);
                $entity->setBaseAdjustmentsTax($entity->getBaseAdjustmentsTax() + $baseTaxUpdate);

                $entity->setGrandTotal($newGrandTotal);
                $entity->setBaseGrandTotal($newBaseGrandTotal);

                if ($newValue == 0) {
                    unset($existingAdjustments[$adjustmentNumber]);
                } else {
                    $existingAdjustments[$adjustmentNumber]["amount"] = $newValue;
                    $existingAdjustments[$adjustmentNumber]["base_amount"] = $newBaseValue;
                }
                $response['message'] = __("'$adjustmentTitle' Adjustment amount has been updated.");
                $response['existing_adjustments'] = $existingAdjustments;
                $response['entity'] = $entity;
                return $response;
            } else {
                throw new LocalizedException(
                    __("After this adjustment($adjustmentTitle) amount grand total will have negative value which is not allowed . Please try again.")
                );
            }
        }
    }

    /*
     * Function contains the logic to disallow
     * */
    /**
     * @param $entity
     * @param $title
     * @param $amount
     * @return bool
     */
    protected function validateInvoiceAmount($entity, $title, $amount)
    {
        if ($entity->getAdjustmentsInvoiced()) {
            $adjustments = $this->adjustmentManager->decodeAdjustments($entity->getAdjustmentsInvoiced());
            foreach ($adjustments as $adjustment) {
                if ($adjustment["title"] == $title && $amount < $adjustment["amount"]) {
                    return $adjustment["amount"];
                }
            }
        }
        return false;
    }
}
