<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Observer\Frontend\Checkout;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Superiortile\RequiredProduct\Model\Cart;
use Superiortile\RequiredProduct\Model\RequiredConfiguration;

/**
 * Class Superiortile\RequiredProduct\Observer\Frontend\Checkout\CartProductAddAfter
 */
class CartProductAddAfter implements ObserverInterface
{
    /**
     * @var Cart
     */
    protected $requiredProduct;
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RequiredConfiguration
     */
    protected $configuration;

    /**
     * @param RequiredConfiguration $configuration
     * @param RequestInterface $request
     * @param Cart $requiredProduct
     */
    public function __construct(
        RequiredConfiguration $configuration,
        RequestInterface $request,
        Cart $requiredProduct
    ) {
        $this->configuration = $configuration;
        $this->request = $request;
        $this->requiredProduct = $requiredProduct;
    }

    /**
     * Execute observer
     *
     * @param  Observer $observer
     * @return void
     * @throws NoSuchEntityException|LocalizedException
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getSkipEventAddRequiredProduct()) {
            return;
        }
        $this->requiredProduct->addRequiredProductsToCart($this->request);

        $selectedRequiredProductsData = $this->request->getParam(Cart::REQUIRED_PRODUCT_FIELD_NAME, []);
        foreach (array_keys($selectedRequiredProductsData) as $collectionTypeId) {
            $data = [
                'main_product' => $this->request->getParam('product'),
                'collection_type_id' => $collectionTypeId
            ];
            $request = new DataObject($data);
            $this->configuration->removeConfigurableItem($request);
        }
    }
}
