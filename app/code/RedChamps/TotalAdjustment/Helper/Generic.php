<?php
namespace RedChamps\TotalAdjustment\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Generic extends AbstractHelper
{
    const BASE_CONFIG_PATH = "total_adjustment/";

    protected $appState;

    protected $currency;

    protected $authorization;

    public function __construct(
        Context $context,
        State $appState,
        AuthorizationInterface $authorization,
        PriceCurrencyInterface $currency
    ) {
        $this->appState = $appState;
        $this->currency = $currency;
        $this->authorization = $authorization;
        parent::__construct($context);
    }

    public function canApply()
    {
        if ($this->appState->getAreaCode() == Area::AREA_ADMINHTML && $this->isAllowed()) {
            return true;
        }
        return false;
    }

    public function isAllowed()
    {
        return $this->authorization->isAllowed(
            'RedChamps_TotalAdjustment::allowed'
        );
    }

    public function getCurrencySymbol($currencyCode)
    {
        return $currencyCode ? $this->currency->getCurrencySymbol(null, $currencyCode) : "";
    }

    public function convertToBaseCurrency($amount, $store)
    {
        $rate = $this->currency->convert($amount, $store) / $amount;
        $amount = $amount / $rate;

        return $this->currency->round($amount);
    }

    public function calculateTax($amount, $percentage)
    {
        $tax = 0;
        if ($this->getTaxSetting('tax_class')) {
            $tax =  ($amount*$percentage)/100;
        }
        return $tax;
    }

    public function getTaxSetting($code)
    {
        return $this->scopeConfig->getValue(self::BASE_CONFIG_PATH . 'tax/' . $code);
    }

    public function areAdjustmentsBeforeTax($taxClass = null)
    {
        if (is_null($taxClass)) {
            $taxClass = $this->getTaxSetting('tax_class');
        }
        if ($taxClass || (!$taxClass && $this->getTaxSetting('before_tax'))) {
            return true;
        }
        return false;
    }
}
