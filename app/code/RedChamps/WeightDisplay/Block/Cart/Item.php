<?php
namespace RedChamps\WeightDisplay\Block\Cart;

use Magento\Framework\View\Element\Template;
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
        if (
            $this->configReader->isAllowed("shopping_cart/enabled") &&
            $this->getItem() &&
            $weight = $this->getItem()->getWeight()
        ) {
            return $this->configReader->getFormattedWeight($weight);
        }
    }
}
