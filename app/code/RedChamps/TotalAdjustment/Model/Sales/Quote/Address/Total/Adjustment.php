<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\Sales\Quote\Address\Total;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;
use RedChamps\TotalAdjustment\Model\ConfigReader;

class Adjustment extends AbstractTotal
{
    const CODE = 'adjustment';

    const TAXABLE_CODE = 'adjustments_tax';

    protected $configReader;

    protected $adjustmentManager;

    public static $collected = false;

    public function __construct(
        ConfigReader $configReader,
        AdjustmentManager $adjustmentManager
    ) {
        $this->configReader = $configReader;
        $this->adjustmentManager = $adjustmentManager;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();

        $this->_setAmount(0);
        $this->_setBaseAmount(0);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this; //this makes only address type shipping to come through
        }

        if ($this->configReader->canApply()) {
            $adjustments = $quote->getAdjustments();
            if ($adjustments) {
                $taxClass = $this->configReader->getTaxSetting('tax_class');
                $decodedAdjustments = is_array($adjustments) ?
                    $adjustments :
                    $this->adjustmentManager->decodeAdjustments($adjustments);
                $totalAmounts = $total->getAllTotalAmounts();
                if ($this->configReader->areAdjustmentsBeforeTax($taxClass)) {
                    unset($totalAmounts['tax']);
                }
                $taxRate = 0;
                if ($this->configReader->areAdjustmentsTaxInclusive()) {
                    $taxRate = $this->configReader->getTaxRate($quote);
                }
                $grandTotal = array_sum($totalAmounts);
                $totalAdjustments = 0;
                $baseTotalAdjustments = 0;
                foreach ($decodedAdjustments as $adjustmentNumber => $adjustment) {
                    if (isset($adjustment['type']) && $adjustment['type'] == "percentage" && !isset($adjustment["percentage"])) {
                        $decodedAdjustments[$adjustmentNumber]["percentage"] = $adjustment['amount'];
                        $amount = number_format(($grandTotal*$adjustment['amount'])/100, 2);
                        $decodedAdjustments[$adjustmentNumber]["amount"] = $amount;
                        $adjustment['amount'] = $amount;
                    }
                    $baseAmount = $this->configReader->convertToBaseCurrency($adjustment['amount'], $quote->getStoreId());
                    $decodedAdjustments[$adjustmentNumber]["base_amount"] = $baseAmount;
                    $grandTotal = $grandTotal + $adjustment['amount'];
                    $totalAdjustments = $totalAdjustments + $adjustment['amount'];
                    $baseTotalAdjustments = $baseTotalAdjustments + $baseAmount;
                    $adjustmentAmount = $adjustment['amount'];
                    //handle tax inclusive adjustments
                    if ($taxRate) {
                        $totalAdjustmentTax = $this->configReader->calculateTax($adjustment['amount'], $taxRate);
                        $totalBaseAdjustmentTax = $this->configReader->calculateTax($baseAmount, $taxRate);
                        $adjustmentAmount = $adjustmentAmount - $totalAdjustmentTax;
                        $baseAmount = $baseAmount - $totalBaseAdjustmentTax;
                    }
                    $total->setTotalAmount('adjustment_amount_' . $adjustmentNumber, $adjustmentAmount);
                    $total->setBaseTotalAmount('adjustment_amount_' . $adjustmentNumber, $baseAmount);
                }

                $adjustments = $this->adjustmentManager->encodeAdjustments($decodedAdjustments);

                if ($this->configReader->getTaxSetting('tax_class')) {
                    $associatedTaxables = $address->getAssociatedTaxables();
                    $associatedTaxables[] = [
                        CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE => self::TAXABLE_CODE,
                        CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE => self::TAXABLE_CODE,
                        'associated_item_code' => CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE,
                        CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $totalAdjustments,
                        CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $baseTotalAdjustments,
                        CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
                        CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $taxClass,
                        'price_includes_tax' => $this->configReader->areAdjustmentsTaxInclusive()
                    ];

                    $address->setAssociatedTaxables($associatedTaxables);
                }
            }
            $address->setAdjustments($adjustments);
            $quote->setAdjustments($adjustments);
        }
    }

    public function fetch(
        Quote $quote,
        Total $total
    ) {
        $result = null;
        if ($this->configReader->canApply()) {
            $amt = $total->getAdjustmentAmount();
            $result = [
                'code'=> self::CODE,
                'title'=> $total->getAdjustmentTitle(),
                'value'=> $amt
            ];
        }

        return $result;
    }

    public function processConfigArray($config, $store)
    {
        $config['before'][] = 'tax';
        if (!$this->configReader->areAdjustmentsBeforeTax()) {
            $config['before'][] = 'grand_total';
            $config['sort_order'] = '549';
        }
        return $config;
    }
}
