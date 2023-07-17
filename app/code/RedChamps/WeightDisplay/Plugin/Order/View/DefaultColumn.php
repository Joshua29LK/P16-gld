<?php
namespace RedChamps\WeightDisplay\Plugin\Order\View;

use RedChamps\WeightDisplay\Model\ConfigReader;

class DefaultColumn
{
    protected $configReader;

    public function __construct(
        ConfigReader $configReader
    ) {
        $this->configReader = $configReader;
    }

    public function afterGetOrderOptions(
        \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $subject,
        $result
    ) {
        $weight = $subject->getItem()->getWeight();
        if ($weight) {
            $result[] = [
                'label' => __("Weight"),
                'value' => $this->configReader->getFormattedWeight($weight)
            ];
        }

        return $result;
    }
}
