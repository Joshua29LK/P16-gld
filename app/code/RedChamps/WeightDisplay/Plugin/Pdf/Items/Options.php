<?php
namespace RedChamps\WeightDisplay\Plugin\Pdf\Items;

use Magento\Sales\Model\Order\Pdf\Items\AbstractItems;
use RedChamps\WeightDisplay\Model\ConfigReader;

class Options
{
    protected $configReader;

    public function __construct(
        ConfigReader $configReader
    ) {
        $this->configReader = $configReader;
    }

    public function afterGetItemOptions(AbstractItems $subject, $result)
    {
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
