<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Plugin;

use Superiortile\RequiredProduct\Model\ProductFactory;
use Superiortile\RequiredProduct\Model\ProductLink\CollectionProvider\RequiredLinkProducts;

/**
 * Class Superiortile\RequiredProduct\Plugin\UpdateToCustomLinkModel
 */
class UpdateToRequiredLinkModel
{
    /**
     * @var ProductFactory
     */
    private $catalogModel;

    /**
     * @param ProductFactory $catalogModel
     */
    public function __construct(
        ProductFactory $catalogModel
    ) {
        $this->catalogModel = $catalogModel;
    }

    /**
     * Before plugin to update model class
     *
     * @param RequiredLinkProducts $subject
     * @param Object $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetLinkedProducts(RequiredLinkProducts $subject, $product)
    {
        $currentProduct = $this->catalogModel->create()
            ->load($product->getId());

        return [$currentProduct];
    }
}
