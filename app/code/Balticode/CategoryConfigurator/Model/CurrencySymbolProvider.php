<?php

namespace Balticode\CategoryConfigurator\Model;

use Exception;
use Magento\Directory\Model\Currency;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CurrencySymbolProvider
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory
    ) {
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @return null|string
     */
    public function getCurrencySymbol()
    {
        try {
            $currentStore = $this->storeManager->getStore();
        } catch (NoSuchEntityException $e) {
            return null;
        }

        $currencyCode = $currentStore->getCurrentCurrencyCode();

        if (!$currencyCode) {
            return null;
        }

        try {
            $currency = $this->currencyFactory->create()->load($currencyCode);
        } catch (Exception $e) {
            return null;
        }

        if ($currency instanceof Currency) {
            return $currency->getCurrencySymbol();
        }

        return null;
    }
}