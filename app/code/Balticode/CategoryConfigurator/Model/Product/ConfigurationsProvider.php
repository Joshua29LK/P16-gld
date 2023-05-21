<?php

namespace Balticode\CategoryConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ConfigurationsProvider
{
    /**
     * @var BaseFieldsProvider
     */
    protected $baseFieldsProvider;

    /**
     * @param BaseFieldsProvider $baseFieldsProvider
     */
    public function __construct(BaseFieldsProvider $baseFieldsProvider)
    {
        $this->baseFieldsProvider = $baseFieldsProvider;
    }

    /**
     * @param ProductInterface $product
     * @return array|null
     */
    public function getProductConfigurationsData($product)
    {
        if (!$product instanceof ProductInterface || $product->getTypeId() != Configurable::TYPE_CODE) {
            return null;
        }

        $childProducts = $product->getTypeInstance()->getUsedProducts($product);

        if (!count($childProducts)) {
            return null;
        }

        $productConfigurationsData = [];

        foreach ($childProducts as $childProduct) {
            if ($childProductConfigurations = $this->baseFieldsProvider->getBaseProductFields($childProduct)) {
                $productConfigurationsData[] = $childProductConfigurations;
            }
        }

        if (!count($productConfigurationsData)) {
            return null;
        }

        return $productConfigurationsData;
    }
}