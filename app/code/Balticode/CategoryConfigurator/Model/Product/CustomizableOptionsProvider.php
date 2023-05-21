<?php

namespace Balticode\CategoryConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;

class CustomizableOptionsProvider
{
    const OPTION_TYPE_DROP_DOWN = 'drop_down';

    /**
     * @var Option
     */
    protected $productOptions;

    /**
     * @param Option $productOptions
     */
    public function __construct(Option $productOptions)
    {
        $this->productOptions = $productOptions;
    }

    /**
     * @param ProductInterface $product
     * @return array|null
     */
    public function prepareCustomizableOptions($product)
    {
        if (!$product instanceof ProductInterface) {
            return null;
        }

        $customizableOptions = $this->productOptions->getProductOptionCollection($product)->getItems();

        if (!count($customizableOptions)) {
            return null;
        }

        $productOptions = [];

        foreach ($customizableOptions as $customizableOption) {
            if ($customizableOptionData = $this->prepareCustomizableOption($customizableOption)) {
                $productOptions[] = $customizableOptionData;
            }
        }

        if (!count($productOptions)) {
            return null;
        }

        return $productOptions;
    }

    /**
     * @param ProductCustomOptionInterface $customizableOption
     * @return null
     */
    protected function prepareCustomizableOption($customizableOption)
    {
        if (!$customizableOption instanceof ProductCustomOptionInterface ||
            $customizableOption->getType() != ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN
        ) {
            return null;
        }

        $customizableOptionValues = [];

        foreach ($customizableOption->getValues() as $value) {
            if ($valueData = $this->prepareCustomizableOptionValue($value)) {
                $customizableOptionValues[] = $valueData;
            }
        }

        if (!count($customizableOptionValues)) {
            return null;
        }

        return [
            'option_id' => $customizableOption->getOptionId(),
            'title' => $customizableOption->getTitle(),
            'values' => $customizableOptionValues
        ];
    }

    /**
     * @param ProductCustomOptionValuesInterface $value
     * @return array|null
     */
    protected function prepareCustomizableOptionValue($value)
    {
        if (!$value instanceof ProductCustomOptionValuesInterface) {
            return null;
        }

        return [
            'option_type_id' => $value->getOptionTypeId(),
            'title' => $value->getTitle(),
            'sku' => $value->getSku(),
            'price' => $value->getPrice()
        ];
    }
}