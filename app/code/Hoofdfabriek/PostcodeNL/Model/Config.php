<?php
namespace Hoofdfabriek\PostcodeNL\Model;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $resourceConfig;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->resourceConfig = $resourceConfig;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Returns module's enabled status
     *
     * @return bool
     */
    public function isPostcodeNLEnabled()
    {
        return (bool)$this->scopeConfig->getValue('postcodenl/general/enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Postcodenl API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue('postcodenl/general/api_key', ScopeInterface::SCOPE_STORES));
    }

    /**
     * Postcodenl API secret
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue('postcodenl/general/api_secret', ScopeInterface::SCOPE_STORES));
    }


    /**
     * Check if street second line is used for house number
     *
     * @return bool
     */
    public function isSecondLineForHouseNumber()
    {
        return $this->scopeConfig->isSetFlag(
            'postcodenl/advanced_config/use_street2_as_housenumber',
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Check if steet third line is used for house addition
     *
     * @return bool
     */
    public function isThirdLineForHouseAddition()
    {
        return $this->scopeConfig->isSetFlag(
            'postcodenl/advanced_config/use_street3_as_housenumber_addition',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Sets config value
     *
     * @param string $pathId
     * @param mixed $value
     * @param string $scope
     * @param int $scopeId
     * @return void
     */
    public function setConfigValue($pathId, $value, $scope = 'default', $scopeId = 0)
    {
        $this->resourceConfig->saveConfig($pathId, $value, $scope, $scopeId);
        $this->cacheTypeList->cleanType('config');
    }

    /**
     * Disable module's functionality for case when postcodenl is not available
     *
     * @return void
     */
    public function disableModule()
    {
        $this->setConfigValue('postcodenl/general/enabled', 0);
    }
}
