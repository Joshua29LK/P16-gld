<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Superiortile\RequiredProduct\Model\Cart
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Cart extends \Magento\Checkout\Model\Cart
{
    public const REQUIRED_PRODUCT_FIELD_NAME = 'required-item';

    /**
     * Add Required Products To Cart
     *
     * @param  RequestInterface $request
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addRequiredProductsToCart($request)
    {
        $data = $this->getSelectedRequiredProductData($request);
        $this->addProducts($data);
    }

    /**
     * Get Selected Required Product Data
     *
     * @param RequestInterface $request
     * @return array
     */
    public function getSelectedRequiredProductData($request)
    {
        $result = [];

        foreach ($request->getParam(self::REQUIRED_PRODUCT_FIELD_NAME, []) as $collection) {
            foreach ($collection as $productId => $qty) {
                if (!empty($result[$productId])) {
                    $result[$productId] += (int) $qty;
                } else {
                    $result[$productId] = (int) $qty;
                }
            }
        }

        return $result;
    }

    /**
     * Add Products
     *
     * @param  array $data
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addProducts($data)
    {
        $allAvailable = true;
        $allAdded = true;
        foreach ($data as $productId => $qty) {
            $productId = (int)$productId;
            $qty = (int)$qty;
            $product = $this->_getProduct($productId);
            $product->setIsRequiredProduct(true);
            if ($product->getId() && $product->isVisibleInCatalog()) {
                $product->setSkipEventAddRequiredProduct(true);
                try {
                    $this->addProduct($product, $qty);
                    $this->messageManager->addComplexSuccessMessage(
                        'addCartSuccessMessage',
                        [
                            'product_name' => $product->getName(),
                            'cart_url' => $this->getCartUrl(),
                        ]
                    );
                } catch (\Exception $e) {
                    $allAdded = false;
                }
            } else {
                $allAvailable = false;
            }
        }

        if (!$allAvailable) {
            $this->messageManager->addErrorMessage(__("We don't have some of the products you want."));
        }
        if (!$allAdded) {
            $this->messageManager->addErrorMessage(__("We don't have as many of some products as you want."));
        }
    }

    /**
     * Returns cart url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    private function getCartUrl()
    {
        return $this->getQuote()->getStore()->getUrl('checkout/cart', ['_secure' => true]);
    }
}
