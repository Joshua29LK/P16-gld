<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bss\RequiredProduct\Block\Product\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Checkout\Model\ResourceModel\Cart as CartResourceModel;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Helper\Data as TaxHelper;
use Magento\Framework\Module\Manager;

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
     * @var TaxHelper
     */
    protected $taxHelper;

    /**
     * @param Context $context
     * @param CartResourceModel $checkoutCart
     * @param ProductVisibility $catalogProductVisibility
     * @param CheckoutSession $checkoutSession
     * @param Manager $moduleManager
     * @param TaxHelper $taxHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CartResourceModel $checkoutCart,
        ProductVisibility $catalogProductVisibility,
        CheckoutSession $checkoutSession,
        Manager $moduleManager,
        TaxHelper $taxHelper,
        array $data = []
    ) {
        $this->taxHelper = $taxHelper;
        parent::__construct(
            $context,
            $checkoutCart,
            $catalogProductVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
    }

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

    /**
     * @param $_product
     * @return float
     */
    public function getProductPriceInclTax($_product)
    {
        if ($_product->getTypeId() == 'simple') {
            $price = $this->taxHelper->getTaxPrice($_product, $_product->getFinalPrice(), true);
            return round($price, 2);
        }
        return null;
    }
}
