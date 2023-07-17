<?php
namespace RedChamps\WeightDisplay\Block\Adminhtml\Order\View;

use Magento\Sales\Block\Adminhtml\Order\Totals\Item;
use RedChamps\WeightDisplay\Model\TotalWeightCalculator;

class Total extends Item
{
    protected $_beforeCondition = null;

    protected $totalWeightCalculator;

    public function __construct(
        TotalWeightCalculator $totalWeightCalculator,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->totalWeightCalculator = $totalWeightCalculator;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    public function getTotalWeight()
    {
        return $this->totalWeightCalculator->execute($this->getOrder(), "order");
    }

    public function getBeforeCondition()
    {
        if ($this->_beforeCondition === null) {
            $this->_beforeCondition = ['subtotal'];
        }
        return $this->_beforeCondition;
    }
}
