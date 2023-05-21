<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Block\Sales\Order\Total;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;
use RedChamps\TotalAdjustment\Model\ConfigReader;

class Adjustment extends Template
{
    protected $configReader;

    protected $adjustmentManager;

    public function __construct(
        ConfigReader $configReader,
        AdjustmentManager $adjustmentManager,
        Template\Context $context,
        array $data = []
    ) {
        $this->configReader = $configReader;
        $this->adjustmentManager = $adjustmentManager;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function initTotals()
    {
        if ($adjustments = $this->getOrder()->getAdjustments()) {
            foreach ($this->adjustmentManager->decodeAdjustments($adjustments) as $adjustmentNumber => $adjustment) {
                $total = new DataObject(
                    [
                        'code' => $this->getNameInLayout() . '_' . $adjustmentNumber,
                        'area' => $this->getArea(),
                        'label' => $adjustment['title'],
                        'value' => $adjustment['amount']
                    ]
                );
                $this->getParentBlock()->addTotalBefore(
                    $total,
                    [
                        $this->configReader->areAdjustmentsBeforeTax() ? 'tax' : 'grand_total'
                    ]
                );
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return mixed
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
