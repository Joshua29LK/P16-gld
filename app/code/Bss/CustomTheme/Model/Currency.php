<?php
namespace Bss\CustomTheme\Model;

class Currency extends \Magento\Directory\Model\Currency
{
    /**
     * @param $price
     * @param $precision
     * @param $options
     * @param $includeContainer
     * @param $addBrackets
     * @return string
     */
    public function formatPrecision(
        $price,
        $precision,
        $options = [],
        $includeContainer = true,
        $addBrackets = false
    ) {
        $priceTxt = $this->formatTxt(
            $price,
            $options
        );
        $symbol = '.';
        $priceParts = [];
        if (strpos($priceTxt, '.') !== false) {
            $priceParts = explode('.', $priceTxt);
        } elseif (strpos($priceTxt, ',') !== false) {
            $symbol = ',';
            $priceParts = explode(',', $priceTxt);
        }
        $intPrice = $priceParts[0] ?? '';
        $decimalPrice = $priceParts[1] ?? '';
        if (!isset($options['precision'])) {
            $options['precision'] = $precision;
        }
        if ($includeContainer) {
            return '<span class="price">' . ($addBrackets ? '[' : '') . $intPrice . $symbol .
                '<span class="custom_sub_price">' . $decimalPrice . '</span>'
                . ($addBrackets ? ']' : '') . '</span>';
        }
        return $this->formatTxt($price, $options);
    }
}
