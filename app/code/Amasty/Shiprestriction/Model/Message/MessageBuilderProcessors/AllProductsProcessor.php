<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Message\MessageBuilderProcessors;

use Amasty\Shiprestriction\Model\ProductRegistry;

class AllProductsProcessor implements MessageBuilderProcessorInterface
{
    /**
     * @var ProductRegistry
     */
    private $productRegistry;

    public function __construct(
        ProductRegistry $productRegistry
    ) {
        $this->productRegistry = $productRegistry;
    }

    public function process(string $message): string
    {
        $allProducts = implode(', ', $this->productRegistry->getProducts());

        return str_replace('{all-products}', $allProducts, $message);
    }
}
