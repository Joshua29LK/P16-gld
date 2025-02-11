<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model;

/**
 * ConfigProvider.
 */
class Config
{
    public const CONFIG_PATH_GENERAL_TAX_INCLUDE = '/general/tax';
    public const CONFIG_PATH_GENERAL_USE_SUBTOTAL = '/general/discount';
    public const CONFIG_PATH_GENERAL_ERROR_MESSAGE = '/general/error_message';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $sectionName
     * @return boolean
     */
    public function getTaxIncludeConfig($sectionName)
    {
        return $this->getConfigValueByPath($sectionName . self::CONFIG_PATH_GENERAL_TAX_INCLUDE);
    }

    /**
     * @param string $sectionName
     * @return boolean
     */
    public function getUseSubtotalConfig($sectionName)
    {
        return $this->getConfigValueByPath($sectionName . self::CONFIG_PATH_GENERAL_USE_SUBTOTAL);
    }

    /**
     * @param string $sectionName
     * @return boolean
     */
    public function getErrorMessageConfig($sectionName)
    {
        return $this->getConfigValueByPath($sectionName . self::CONFIG_PATH_GENERAL_ERROR_MESSAGE);
    }

    /**
     * @param $path
     * @param null $storeId
     * @param string $scope
     * @return mixed
     */
    public function getConfigValueByPath(
        $path,
        $storeId = null,
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE
    ) {
        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * @return array
     */
    public function getCarriersConfig()
    {
        return $this->scopeConfig->getValue('carriers');
    }
}
