<?php

namespace Geissweb\Euvat\Plugin\Pricing;

use Geissweb\Euvat\Helper\Configuration;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Framework\Pricing\SaleableInterface;

/**
 * Adds a plugin to change the "As low as" price calculator
 */
class MinimalTierPriceCalculator
{
    /**
     * @var \Geissweb\Euvat\Helper\Configuration
     */
    private $configHelper;

    /**
     * @param \Geissweb\Euvat\Helper\Configuration $configHelper
     */
    public function __construct(
        Configuration $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * Changes the way the lowest tier price is estimated by using the website price which is always incl. tax.
     * Otherwise it can happen that the price excl. tax is being used and the tax amount is being subtracted
     * again in further processing, which will lead to a wrong display of the "As low as" price in catalog pages.
     *
     * @param \Magento\Catalog\Pricing\Price\MinimalTierPriceCalculator $subject
     * @param callable $proceed
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     *
     * @return mixed|null
     */
    public function aroundGetValue(
        \Magento\Catalog\Pricing\Price\MinimalTierPriceCalculator $subject,
        callable $proceed,
        SaleableInterface $saleableItem
    ) {
        if ($this->configHelper->isAsLowAsPriceFixEnabled()) {
            /** @var TierPrice $price */
            $price = $saleableItem->getPriceInfo()->getPrice(TierPrice::PRICE_CODE);
            $tierPriceList = $price->getTierPriceList();

            $tierPrices = [];
            foreach ($tierPriceList as $tierPrice) {
                $tierPrices[] = $tierPrice['website_price'];
            }

            return $tierPrices ? min($tierPrices) : $proceed($saleableItem);
        }

        return $proceed($saleableItem);
    }
}
