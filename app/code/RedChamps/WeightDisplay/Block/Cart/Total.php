<?php
namespace RedChamps\WeightDisplay\Block\Cart;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Quote\Model\Quote;
use RedChamps\WeightDisplay\Model\ConfigReader;
use RedChamps\WeightDisplay\Model\TotalWeightCalculator;

class Total extends Template
{
    protected $_quote;

    protected $checkoutSession;

    protected $totalWeightCalculator;

    protected $configReader;

    public function __construct(
       TotalWeightCalculator $totalWeightCalculator,
       ConfigReader $configReader,
       Session $checkoutSession,
       Template\Context $context,
       array $data = []
   ) {
        $this->configReader = $configReader;
        $this->checkoutSession = $checkoutSession;
        $this->totalWeightCalculator = $totalWeightCalculator;
        parent::__construct($context, $data);
    }

    public function getTotalWeight()
    {
        if ($this->configReader->isAllowed("shopping_cart/enabled")) {
            return $this->totalWeightCalculator->execute($this->getQuote());
        }
    }

    public function getTotalLabel()
    {
        return $this->configReader->getConfig("shopping_cart/label");
    }

    /**
     * Get active quote
     *
     * @return Quote
     */
    protected function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->checkoutSession->getQuote();
        }
        return $this->_quote;
    }
}
