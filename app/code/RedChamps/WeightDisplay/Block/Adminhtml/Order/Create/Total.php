<?php
namespace RedChamps\WeightDisplay\Block\Adminhtml\Order\Create;

use Magento\Backend\Block\Template;
use Magento\Backend\Model\Session\Quote;
use RedChamps\WeightDisplay\Model\ConfigReader;
use RedChamps\WeightDisplay\Model\TotalWeightCalculator;

class Total extends Template
{
    protected $sessionQuote;

    protected $totalWeightCalculator;

    public function __construct(
        Quote $sessionQuote,
        TotalWeightCalculator $totalWeightCalculator,
        Template\Context $context,
        array $data = []
    ) {
        $this->sessionQuote = $sessionQuote;
        $this->totalWeightCalculator = $totalWeightCalculator;
        parent::__construct($context, $data);
    }

    public function getTotalWeight()
    {
        return $this->totalWeightCalculator->execute($this->sessionQuote->getQuote());
    }
}
