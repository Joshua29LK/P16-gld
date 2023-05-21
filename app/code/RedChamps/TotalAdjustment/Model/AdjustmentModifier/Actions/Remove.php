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

class Remove extends Base
{
    /**
     * @param CartInterface|OrderInterface $entity
     * @param $adjustmentTitles
     * @return false|\Magento\Framework\Phrase|string
     * @throws LocalizedException
     */
    public function execute($entity, $adjustmentTitles)
    {
        $existingAdjustments = $entity->getAdjustments() ?
            $this->adjustmentManager->decodeAdjustments($entity->getAdjustments()) :
            [];
        if (is_numeric($adjustmentTitles)) {
            if (isset($existingAdjustments[$adjustmentTitles])) {
                $result = $this->processAdjustmentRemoval($existingAdjustments, $adjustmentTitles, $entity);
                $existingAdjustments = $result[0];
                $entity = $result[1];
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
            foreach ($adjustmentTitles as $adjustmentTitle) {
                $adjustmentTitle = strtolower($adjustmentTitle);
                if (!isset($existingAdjustmentTitles[$adjustmentTitle])) {
                    throw new LocalizedException(
                        __("Adjustment amount can't be found with specified adjustment title. Please try again.")
                    );
                }
                $adjustmentNumber = $existingAdjustmentTitles[$adjustmentTitle];
                $result = $this->processAdjustmentRemoval($existingAdjustments, $adjustmentNumber, $entity);
                $existingAdjustments = $result[0];
                $entity = $result[1];
            }
        }
        $entity->setAdjustments($this->adjustmentManager->encodeAdjustments($existingAdjustments));
        return __('Adjustment has been removed successfully.');
    }

    protected function processAdjustmentRemoval($existingAdjustments, $adjustmentNumber, $entity)
    {
        $adjustment = $existingAdjustments[$adjustmentNumber];

        $taxUpdate = 0;
        $baseTaxUpdate = 0;

        if ((float)$entity->getAdjustmentsTax()) {
            $taxUpdate = $this->calculateTax($entity, $adjustment['amount']);
            $baseTaxUpdate = $this->calculateTax(
                $entity, (isset($adjustment['base_amount']) ? $adjustment['base_amount'] : $adjustment['amount'])
            );
        }

        $entity->setTaxAmount($entity->getTaxAmount()-$taxUpdate);
        $entity->setBaseTaxAmount($entity->getBaseTaxAmount()-$baseTaxUpdate);

        $entity->setAdjustmentsTax($entity->getAdjustmentsTax()-$taxUpdate);
        $entity->setBaseAdjustmentsTax($entity->getBaseAdjustmentsTax()-$baseTaxUpdate);

        $newGrandTotal = $entity->getGrandTotal()-$adjustment['amount'];
        $newBaseGrandTotal = $entity->getBaseGrandTotal()-((isset($adjustment['base_amount']) ? $adjustment['base_amount'] : $adjustment['amount']));
        if (!$this->configReader->areAdjustmentsTaxInclusive()) {
            $newGrandTotal-=$taxUpdate;
            $newBaseGrandTotal-=$baseTaxUpdate;
        }
        $entity->setGrandTotal($newGrandTotal);
        $entity->setBaseGrandTotal($newBaseGrandTotal);

        unset($existingAdjustments[$adjustmentNumber]);
        return [$existingAdjustments, $entity];
    }
}
