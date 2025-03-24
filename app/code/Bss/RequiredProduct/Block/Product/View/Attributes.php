<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bss\RequiredProduct\Block\Product\View;

use Magento\Catalog\Model\Product;

/**
 * Class Bss\RequiredProduct\Block\Product\View\Attributes
 */
class Attributes extends \Magento\Catalog\Block\Product\View\Attributes
{
    /**
     * @var string
     */
    protected $_template = 'Bss_RequiredProduct::product/list/attributes.phtml';

    /**
     * @var $product
     */
    protected $product;

    /**
     * SetProduct
     *
     * @param  Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * GetProduct
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
