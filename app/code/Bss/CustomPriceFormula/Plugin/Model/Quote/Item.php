<?php

namespace Bss\CustomPriceFormula\Plugin\Model\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Item
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Item constructor.
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Plugin method to modify the calculation of row total.
     *
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundGetRowTotal(
        \Magento\Quote\Model\Quote\Item $subject,
        \Closure $proceed
    ) {
        $finalPrice = round($subject->getFormulaPrice(), 2);
        $finalPrice = ($finalPrice == 0) ? null : $finalPrice;
        if (!is_null($finalPrice)) {
            if ($subject->isCatalogPriceInclTax() && $subject->getTaxPercent()) {
                $finalPrice = 100 * $finalPrice / (100 + $subject->getTaxPercent());
            }

            $rowTotal = $finalPrice * $subject->getQty();
            $rowTotal = $this->priceCurrency->convert($rowTotal);
            return $this->priceCurrency->convert($rowTotal);
        }

        return $proceed();
    }
}
