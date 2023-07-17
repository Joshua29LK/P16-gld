<?php
namespace RedChamps\WeightDisplay\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigReader
{
    /**
     * Path to config value that contains weight unit
     */
    const XML_PATH_WEIGHT_UNIT = 'general/locale/weight_unit';

    const XML_BASE_CONFIG_PATH = "weight_display/";

    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isAllowed($path)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_BASE_CONFIG_PATH . $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getWeightUnit()
    {
            return __($this->scopeConfig->getValue(
                self::XML_PATH_WEIGHT_UNIT,
                ScopeInterface::SCOPE_STORE
            ));
    }

    public function getFormattedWeight($weight)
    {
        return ($weight+0) . " " . $this->getWeightUnit();
    }

    public function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            self::XML_BASE_CONFIG_PATH . $path,
            ScopeInterface::SCOPE_STORE
        );
    }
}
