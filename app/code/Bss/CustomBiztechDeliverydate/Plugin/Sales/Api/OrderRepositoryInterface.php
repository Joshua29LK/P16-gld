<?php

namespace Bss\CustomBiztechDeliverydate\Plugin\Sales\Api;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

class OrderRepositoryInterface
{
    protected $extensionFactory;

    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    public function afterGetList(
        $subject,
        OrderSearchResultInterface $searchResult
    ) {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $isFastestDate = $order->getData('is_fastest_date');
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes->setIsFastestDate($isFastestDate);
            $order->setExtensionAttributes($extensionAttributes);
        }

        return $searchResult;
    }

    public function afterGet(
        $subject,
        $order
    ) {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }

        $extensionAttributes->setIsFastestDate($order->getData('is_fastest_date'));
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}
