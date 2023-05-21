<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Plugin\Model\Sales\Total\Quote;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use Magento\Tax\Model\Sales\Total\Quote\Tax;
use RedChamps\TotalAdjustment\Model\Sales\Quote\Address\Total\Adjustment;

class TaxPlugin
{
    public function aroundCollect(
        $subject,
        callable $proceed,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $result = $proceed($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        $address->setAdjustmentsTax(0);
        $address->setBaseAdjustmentsTax(0);

        $this->processExtraTaxable($address, $total);

        return $result;
    }

    protected function processExtraTaxable($address, $total)
    {
        $extraTaxable = $total->getExtraTaxableDetails();
        if (isset($extraTaxable[Adjustment::TAXABLE_CODE][CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE])) {
            $extraAdjustmentTaxes = $extraTaxable[Adjustment::TAXABLE_CODE][CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE];
            $totalAdjustmentTax = 0;
            $totalBaseAdjustmentTax = 0;
            foreach ($extraAdjustmentTaxes as $extraAdjustmentTax) {
                $tax = $extraAdjustmentTax[Tax::KEY_TAX_DETAILS_PRICE_INCL_TAX] - $extraAdjustmentTax[Tax::KEY_TAX_DETAILS_PRICE_EXCL_TAX];
                $baseTax = $extraAdjustmentTax[Tax::KEY_TAX_DETAILS_BASE_PRICE_INCL_TAX] - $extraAdjustmentTax[Tax::KEY_TAX_DETAILS_BASE_PRICE_EXCL_TAX];
                if ($tax < 0 && !$extraAdjustmentTax[Tax::KEY_TAX_DETAILS_ROW_TAX]) {
                    $totalAdjustmentTax+=$tax;
                    $totalBaseAdjustmentTax+=$baseTax;
                    $total->addTotalAmount('tax', $tax);
                    $total->addBaseTotalAmount('tax', $baseTax);
                } else {
                    $totalAdjustmentTax+=$extraAdjustmentTax[Tax::KEY_TAX_DETAILS_ROW_TAX];
                    $totalBaseAdjustmentTax+=$extraAdjustmentTax[Tax::KEY_TAX_DETAILS_BASE_ROW_TAX];
                }
                $address->setAdjustmentsTaxPercentage($extraAdjustmentTax[Tax::KEY_TAX_DETAILS_TAX_PERCENT]);
            }
            $address->setAdjustmentsTax($totalAdjustmentTax);
            $address->setBaseAdjustmentsTax($totalBaseAdjustmentTax);
        }
    }
}
