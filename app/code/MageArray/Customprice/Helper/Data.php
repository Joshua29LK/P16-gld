<?php

namespace MageArray\Customprice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var $_scopeConfig
     */
    protected $_scopeConfig;

    /**
     * [__construct description]
     *
     * @param \Magento\Framework\App\Helper\Context      $context         [description]
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager    [description]
     * @param \Magento\Directory\Model\CurrencyFactory   $currencyFactory [description]
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        parent::__construct($context);
    }

    /**
     * [getStoreConfig description]
     *
     * @param  [type] $storePath [description]
     * @return [type]            [description]
     */
    public function getStoreConfig($storePath)
    {
        $storeConfig = $this->_scopeConfig
            ->getValue(
                $storePath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        return $storeConfig;
    }

    /**
     * [convertPriceFromBaseCurrencyToCurrentCurrency description]
     *
     * @param  [type] $price [description]
     * @return [type]        [description]
     */
    public function convertPriceFromBaseCurrencyToCurrentCurrency($price)
    {
        $curentCurrencyCode =  $this->storeManager->getStore()
            ->getCurrentCurrency()
            ->getCode();
        $baseCurrencyCode =  $this->storeManager->getStore()
            ->getBaseCurrency()
            ->getCode();

        if ($price != '' && $baseCurrencyCode != $curentCurrencyCode) {
            $rate = $this->currencyFactory->create()
                ->load($baseCurrencyCode)
                ->getAnyRate($curentCurrencyCode);

            $price = $price * $rate;
        }
        return (float) $price;
    }
}
