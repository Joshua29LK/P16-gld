<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_PRODUCT_PRICE_FORMULA
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\ProductPriceFormula\Plugin;

class CustomerWishlistPrice {
    public function afterToHtml($subject, $result) {
        if ($subject->getNameInLayout() != 'customer.wishlist.item.price') return $result;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Itoris\ProductPriceFormula\Helper\Price');
        $price = $helper->getProductFinalPrice($subject->getItem());
        if ($price) {
            $productId = $subject->getProductItem()->getId();
            $finalPriceFormatted = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency(number_format($price, 2), true, false);
            $priceHtml = '<div class="price-box price-configured_price" data-role="priceBox" data-product-id="'.$productId.'"><p class="price-as-configured">
                <span class="price-container price-configured_price">
                        <span id="product-price-'.$productId.'" data-price-amount="'.$price.'" data-price-type="finalPrice" class="price-wrapper ">
                        <span class="price">'.$finalPriceFormatted.'</span></span>
                </span></p></div>';
            if (count($subject->getItem()->getOptions())) {
                $optionsBlock = $objectManager->create('Magento\Wishlist\Block\Customer\Wishlist\Item\Options');
                $priceHtml .= $optionsBlock->setItem($subject->getItem())->toHtml();
            }
            return $priceHtml;
        }
        return $result;
    }
}