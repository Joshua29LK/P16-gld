<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Model\Calculation;

class ConfigReader
{
    const BASE_CONFIG_PATH = "total_adjustment/";

    protected $appState;

    protected $currency;

    protected $authorization;

    protected $taxCalculator;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        State $appState,
        AuthorizationInterface $authorization,
        PriceCurrencyInterface $currency,
        Calculation $taxCalculator
    ) {
        $this->appState = $appState;
        $this->currency = $currency;
        $this->authorization = $authorization;
        $this->taxCalculator = $taxCalculator;
        $this->scopeConfig = $scopeConfig;
    }

    public function canApply()
    {
        $areaCode = $this->appState->getAreaCode();
        if ($areaCode == Area::AREA_WEBAPI_REST ||
            ($areaCode == Area::AREA_ADMINHTML && $this->isAllowed())
        ) {
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
            if ($this->areAdjustmentsTaxInclusive()) {
                $tax  = ($amount * $percentage) / (100 + $percentage);
            } else {
                $tax =  ($amount*$percentage)/100;
            }
        }
        return $tax;
    }

    public function getTaxRate($object)
    {
        $request = $this->taxCalculator->getRateRequest(
            $object->getShippingAddress(),
            $object->getBillingAddress(),
            null,
            $object->getStore(),
            $object->getCustomerId()
        );
        return $this->taxCalculator->getRate(
            $request->setProductClassId($this->getTaxSetting('tax_class'))
        );
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

    public function areAdjustmentsTaxInclusive()
    {
        return $this->getTaxSetting('inclusive');
    }
}
