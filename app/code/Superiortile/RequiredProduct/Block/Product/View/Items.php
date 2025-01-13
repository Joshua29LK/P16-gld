<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Block\Product\View;

use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Superiortile\RequiredProduct\Block\Product\View\Items
 */
class Items extends Related
{
    /**
     * @var string
     */
    protected $_template = 'Superiortile_RequiredProduct::product/list/items.phtml';

    /**
     * GetProductAttributesHtml
     *
     * @param  Product $_product
     * @return void
     * @throws LocalizedException
     */
    public function getProductAttributesHtml($_product)
    {
        return $this->getLayout()
            ->createBlock(Attributes::class)
            ->setProduct($_product)
            ->toHtml();
    }
}
