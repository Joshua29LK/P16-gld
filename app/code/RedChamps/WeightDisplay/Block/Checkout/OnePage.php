<?php
namespace RedChamps\WeightDisplay\Block\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use RedChamps\WeightDisplay\Model\ConfigReader;
use RedChamps\WeightDisplay\Model\TotalWeightCalculator;

class OnePage extends Template
{
    protected $checkoutSession;

    protected $totalWeightCalculator;

    protected $configReader;

    public function __construct(
        Session $checkoutSession,
        TotalWeightCalculator $totalWeightCalculator,
        ConfigReader $configReader,
        Template\Context $context,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->configReader = $configReader;
        $this->totalWeightCalculator = $totalWeightCalculator;
        parent::__construct($context, $data);
    }

    public function isEnabled()
    {
        return $this->configReader->getConfig('checkout/enabled');
    }

    public function getTotalWeight()
    {
        return $this->totalWeightCalculator->execute($this->checkoutSession->getQuote());
    }

    public function getTotalLabel()
    {
        return $this->configReader->getConfig('checkout/label');
    }

    public function getPosition()
    {
        return $this->configReader->getConfig('checkout/position');
    }
}
