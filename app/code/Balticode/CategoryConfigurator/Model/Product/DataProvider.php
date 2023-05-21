<?php

namespace Balticode\CategoryConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;

class DataProvider
{
    /**
     * @var BaseFieldsProvider
     */
    protected $baseFieldsProvider;

    /**
     * @var CustomizableOptionsProvider
     */
    protected $customizableOptionsProvider;

    /**
     * @var ConfigurationsProvider
     */
    protected $productConfigurationsProvider;

    /**
     * @param BaseFieldsProvider $baseFieldsProvider
     * @param CustomizableOptionsProvider $customizableOptionsProvider
     * @param ConfigurationsProvider $productConfigurationsProvider
     */
    public function __construct(
        BaseFieldsProvider $baseFieldsProvider,
        CustomizableOptionsProvider $customizableOptionsProvider,
        ConfigurationsProvider $productConfigurationsProvider
    ) {
        $this->baseFieldsProvider = $baseFieldsProvider;
        $this->customizableOptionsProvider = $customizableOptionsProvider;
        $this->productConfigurationsProvider = $productConfigurationsProvider;
    }

    /**
     * @param ProductInterface[] $products
     * @return array
     */
    public function prepareProductsData($products)
    {
        $productsData = [];

        if (!is_array($products)) {
            return $productsData;
        }

        foreach ($products as $product) {
            if ($productData = $this->prepareProductData($product)) {
                $productsData[] = $productData;
            }
        }

        return $productsData;
    }

    /**
     * @param ProductInterface $product
     * @return array|null
     */
    protected function prepareProductData($product)
    {
        $productData = $this->baseFieldsProvider->getBaseProductFields($product);

        if (!$productData) {
            return null;
        }

        if ($productOptions = $this->customizableOptionsProvider->prepareCustomizableOptions($product)) {
            $productData['options'] = $productOptions;
        }

        if ($productConfigurations = $this->productConfigurationsProvider->getProductConfigurationsData($product)) {
            $productData['product_configurations'] = $productConfigurations;
        }

        return $productData;
    }
}