<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Observer;

use Amasty\Flags\Model\Flag;
use Amasty\Flags\Model\ResourceModel\Flag\Collection as FlagCollection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class OrderSaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory
     */
    private $flagCollectionFactory;
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Order\Flag
     */
    private $flagResource;

    public function __construct(
        \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory,
        \Amasty\Flags\Model\ResourceModel\Order\Flag $flagResource
    ) {
        $this->flagCollectionFactory = $flagCollectionFactory;
        $this->flagResource = $flagResource;
    }

    public function getAutoFlags()
    {
        /** @var FlagCollection $flags */
        $flags = $this->flagCollectionFactory->create();

        $flags
            ->addFieldToFilter('apply_column', ['notnull' => true])
            ->setOrder('priority', 'ASC');

        return $flags;
    }

    public function applyFlags($orderId, FlagCollection $flags)
    {
        $filledColumns = [];

        /** @var Flag $flag */
        foreach ($flags as $flag) {
            $columnId = $flag->getApplyColumn();

            if (isset($filledColumns[$columnId])) {
                continue;
            }

            $this->flagResource->assign($orderId, $columnId, $flag->getId());

            $filledColumns[$columnId] = true;
        }
    }

    public function removeFlags($orderId, FlagCollection $flags)
    {
        $clearedColumns = [];

        /** @var Flag $flag */
        foreach ($flags as $flag) {
            $columnId = $flag->getApplyColumn();

            if (isset($clearedColumns[$columnId])) {
                continue;
            }

            $this->flagResource->unassign($orderId, $columnId);

            $clearedColumns[$columnId] = true;
        }
    }

    public function applyByStatus(Order $order)
    {
        $status = $order->getData('status');
        $prevStatus = $order->getOrigData('status');
        if ($status != $prevStatus) {
            $flagsToRemove = $this->getAutoFlags()
                ->addFieldToFilter(
                    'apply_status',
                    ['finset' => $prevStatus]
                );
            $flagsToApply = $this->getAutoFlags()
                ->addFieldToFilter(
                    'apply_status',
                    ['finset' => $status]
                );

            $this->removeFlags($order->getId(), $flagsToRemove);
            $this->applyFlags($order->getId(), $flagsToApply);
        }

        return $this;
    }

    public function applyByShipping(Order $order)
    {
        if (!$order->getOrigData('entity_id')) {
            $flags = $this->getAutoFlags()
                ->addFieldToFilter(
                    'apply_shipping',
                    ['finset' => $order->getShippingMethod()]
                );

            $this->applyFlags($order->getId(), $flags);
        }

        return $this;
    }

    public function applyByPayment(Order $order)
    {
        if (!$order->getOrigData('entity_id')) {
            $flags = $this->getAutoFlags()
                ->addFieldToFilter(
                    'apply_payment',
                    ['finset' => $order->getPayment()->getMethod()]
                );

            $this->applyFlags($order->getId(), $flags);
        }

        return $this;
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getOrder();

        $this
            ->applyByStatus($order)
            ->applyByShipping($order)
            ->applyByPayment($order)
        ;
    }
}
