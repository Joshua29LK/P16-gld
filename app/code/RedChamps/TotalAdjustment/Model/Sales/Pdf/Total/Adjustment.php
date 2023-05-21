<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\Sales\Pdf\Total;

use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class Adjustment extends DefaultTotal
{
    /**
     * @var AdjustmentManager
     */
    private $adjustmentManager;

    public function __construct(
        AdjustmentManager $adjustmentManager,
        Data $taxHelper,
        Calculation $taxCalculation,
        CollectionFactory $ordersFactory,
        array $data = []
    ) {
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
        $this->adjustmentManager = $adjustmentManager;
    }

    public function getTotalsForDisplay()
    {
        $adjustments = $this->getSource()->getAdjustments();
        $totals = [];
        if ($adjustments) {
            $adjustments = $this->adjustmentManager->decodeAdjustments($adjustments);
            foreach ($adjustments as $adjustment) {
                $amount = $this->getOrder()->formatPriceTxt($adjustment["amount"]);
                if ($this->getAmountPrefix()) {
                    $amount = $this->getAmountPrefix() . $amount;
                }
                $title = __($adjustment["title"]);
                if ($this->getTitleSourceField()) {
                    $label = $title . ' (' . $this->getTitleDescription() . '):';
                } else {
                    $label = $title . ':';
                }

                $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
                $totals[] = [
                    'amount'    => $amount,
                    'label'     => $label,
                    'font_size' => $fontSize
                ];
            }
        }
        return $totals;
    }
}
