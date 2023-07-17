<?php
namespace RedChamps\WeightDisplay\Block\Adminhtml\Order\Create;

use Magento\Backend\Block\Template;
use RedChamps\WeightDisplay\Model\ConfigReader;

class Item extends Template
{
    protected $configReader;

    public function __construct(
        ConfigReader $configReader,
        Template\Context $context,
        array $data = []
    ) {
        $this->configReader = $configReader;
        parent::__construct($context, $data);
    }

    public function getWeight()
    {
        if ($this->getItem() && $weight = $this->getItem()->getWeight()) {
            return $this->configReader->getFormattedWeight($weight);
        }
    }
}
