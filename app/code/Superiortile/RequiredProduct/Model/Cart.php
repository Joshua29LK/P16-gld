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

class Cart extends \Magento\Checkout\Model\Cart
{
    public const REQUIRED_PRODUCT_FIELD_NAME = 'required-item';

    /**
     * Add Required Products To Cart
     *
     * @param RequestInterface $request
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addRequiredProductsToCart($request)
    {
        $data = $this->getSelectedRequiredProductData($request);
        $optionsData = $this->getSelectedRequiredProductOptions($request);
        $this->addProducts($data, $optionsData);
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
                    $result[$productId] += (int)$qty;
                } else {
                    $result[$productId] = (int)$qty;
                }
            }
        }
        return $result;
    }

    /**
     * Get Selected Required Product Options
     *
     * @param RequestInterface $request
     * @return array
     */
    public function getSelectedRequiredProductOptions($request)
    {
        $result = [];
        foreach ($request->getParam('options-required-item', []) as $collectionId => $products) {
            foreach ($products as $productId => $optionsJson) {
                $options = json_decode($optionsJson, true);
                if (is_array($options)) {
                    $result[$productId] = $options;
                }
            }
        }
        return $result;
    }

    /**
     * Add Products with Options
     *
     * @param array $data
     * @param array $optionsData
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addProducts($data, $optionsData)
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
                    $options = $optionsData[$productId] ?? [];
                    $this->addProduct($product, [
                        'qty' => $qty,
                        'options' => $options
                    ]);

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
