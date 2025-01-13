<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Model;

use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;
use Superiortile\RequiredProduct\Model\Product\Link;
use Superiortile\RequiredProduct\Ui\DataProvider\Product\Form\Modifier\RequiredProduct;

/**
 * Class Superiortile\RequiredProduct\Model\Product
 */
class Product extends \Magento\Catalog\Model\Product
{
    /**
     * Retrieve array of Required products
     *
     * @param string $linkType
     * @return array
     */
    public function getRequiredLinkProducts($linkType)
    {
        if (!$this->hasData($linkType)) {
            $products = [];
            $collection = $this->getRequiredLinkProductCollection($linkType);
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setData($linkType, $products);
        }
        return $this->getData($linkType);
    }

    /**
     * Retrieve Required products identifiers
     *
     * @param string $linkType
     * @return array
     */
    public function getRequiredLinkProductIds($linkType)
    {
        if (!$this->hasData($linkType . '_ids')) {
            $ids = [];
            foreach ($this->getRequiredLinkProducts($linkType) as $product) {
                $ids[] = $product->getId();
            }
            $this->setData($linkType . '_ids', $ids);
        }
        return [$this->getData($linkType . '_ids')];
    }

    /**
     * Retrieve collection Required products
     *
     * @param  string $linkType
     * @return Collection
     */
    public function getRequiredLinkProductCollection($linkType)
    {
        return $this->getLinkInstance()
            ->setLinkTypeId($this->getRequiredProductLinkTypeId($linkType))
            ->getProductCollection()
            ->setIsStrongMode()
            ->setProduct($this);
    }

    /**
     * Get Required Product Link Type Id By Code
     *
     * @param  string $linkType
     * @return mixed
     */
    public function getRequiredProductLinkTypeId($linkType)
    {
        $typeIds = [
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_1 => Link::LINK_TYPE_REQUIRE_PRODUCT_1,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_2 => Link::LINK_TYPE_REQUIRE_PRODUCT_2,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_3 => Link::LINK_TYPE_REQUIRE_PRODUCT_3,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_4 => Link::LINK_TYPE_REQUIRE_PRODUCT_4,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_5 => Link::LINK_TYPE_REQUIRE_PRODUCT_5,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_6 => Link::LINK_TYPE_REQUIRE_PRODUCT_6,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_7 => Link::LINK_TYPE_REQUIRE_PRODUCT_7,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_8 => Link::LINK_TYPE_REQUIRE_PRODUCT_8

        ];

        return $typeIds[$linkType];
    }
}
