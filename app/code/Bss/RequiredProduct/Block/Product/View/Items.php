<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bss\RequiredProduct\Block\Product\View;

use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Bss\RequiredProduct\Block\Product\View\Items
 */
class Items extends Related
{
    /**
     * @var string
     */
    protected $_template = 'Bss_RequiredProduct::product/list/items.phtml';

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

    /**
     * @param $_product
     * @return bool
     */
    public function canImmediatelyAddToCart($_product)
    {
        if ($_product->getTypeId() == 'simple') {
            $hasRequiredOptions = false;
            $customOptions = $_product->getOptions();
            if (!empty($customOptions)) {
                foreach ($customOptions as $option) {
                    if ($option->getIsRequire()) {
                        $hasRequiredOptions = true;
                        break;
                    }
                }
            }
            if (!$hasRequiredOptions) {
                return true;
            }
        }
        return false;
    }
}
