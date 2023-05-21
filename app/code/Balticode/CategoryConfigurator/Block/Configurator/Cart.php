<?php

namespace Balticode\CategoryConfigurator\Block\Configurator;

use Balticode\CategoryConfigurator\Model\CurrencySymbolProvider;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Cart extends Template
{
    const ADD_TO_CART_ACTION_PATH = 'category/configurator/cart';

    /**
     * @var CurrencySymbolProvider
     */
    protected $currencySymbolProvider;

    /**
     * @param Context $context
     * @param CurrencySymbolProvider $currencySymbolProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        CurrencySymbolProvider $currencySymbolProvider,
        array $data = []
    ) {
        $this->currencySymbolProvider = $currencySymbolProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getAddToCartActionUrl()
    {
        return $this->getUrl(self::ADD_TO_CART_ACTION_PATH);
    }

    /**
     * @return null|string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbolProvider->getCurrencySymbol();
    }
}
