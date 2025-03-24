<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bss\RequiredProduct\Model\ProductLink\CollectionProvider;

use Bss\RequiredProduct\Model\Product;

/**
 * Class Bss\RequiredProduct\Model\ProductLink\CollectionProvider\RequiredLinkProducts
 */
abstract class RequiredLinkProducts
{
    /**
     * @var string
     */
    protected $linkType;

    /**
     * @param string $linkType
     */
    public function __construct(
        string $linkType
    ) {
        $this->linkType = $linkType;
    }

    /**
     * Get Linked Products
     *
     * @param Product $product
     * @return mixed
     */
    public function getLinkedProducts($product)
    {
        return $product->getRequiredLinkProducts($this->linkType);
    }
}
