<?php

namespace Balticode\CategoryConfigurator\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;

class BaseFieldsProvider
{
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @param Image $imageHelper
     */
    public function __construct(Image $imageHelper)
    {
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param ProductInterface $product
     * @return array|null
     */
    public function getBaseProductFields($product)
    {
        if (!$product instanceof ProductInterface) {
            return null;
        }

        return [
            'product_id' => $product->getId(),
            'name' => $product->getName(),
            'image' => $this->getProductImageUrl($product),
            'price' => $product->getPrice(),
            'description' => $product->getDescription()
        ];
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    protected function getProductImageUrl($product)
    {
        return $this->imageHelper->init($product, 'product_page_image_medium')->getUrl();
    }
}