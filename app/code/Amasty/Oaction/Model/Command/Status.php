<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command;

use Amasty\Oaction\Model\Command;
use Amasty\Oaction\Helper\Data;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\ResourceModel\GridPool;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Status extends Command
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var GridPool
     */
    private $gridPool;

    /**
     * @var OrderStatusFactory
     */
    private $orderStatusFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderCommentSender
     */
    private $commentSender;

    public function __construct(
        Data $helper,
        GridPool $gridPool,
        OrderStatusFactory $orderStatusFactory,
        OrderRepositoryInterface $orderRepository,
        OrderCommentSender $commentSender
    ) {
        parent::__construct();
        $this->helper = $helper;
        $this->gridPool = $gridPool;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->orderRepository = $orderRepository;
        $this->commentSender = $commentSender;
    }

    /**
     * Executes the command
     *
     * @param AbstractCollection $collection
     * @param $param
     * @param $oaction
     * @return \Magento\Framework\Phrase|string
     */
    public function execute(AbstractCollection $collection, $param, $oaction)
    {
        $numAffectedOrders = 0;

        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $orderIncrementId = $order->getIncrementId();
            $order = $this->orderRepository->get($order->getId());

            try {
                if ($this->helper->getModuleConfig('status/check_state')) {
                    $state = $order->getState();
                    $statuses = $this->orderStatusFactory->create()
                        ->addStateFilter($state)
                        ->toOptionHash();

                    if (!array_key_exists($param['status'], $statuses)) {
                        $errorMessage = __('Selected status does not correspond to the state of order.');
                        $this->_errors[] = __('Can not update order #%1: %2', $orderIncrementId, $errorMessage);
                        continue;
                    }
                }

                if ($param['notify']) {
                    $statusHistory = $order->addCommentToStatusHistory($param['comment_text'], $param['status']);
                    $statusHistory->setIsVisibleOnFront(false);
                    $statusHistory->setIsCustomerNotified(true);
                    $this->commentSender->send($order, $statusHistory->getIsCustomerNotified(), $param['comment_text']);
                } else {
                    $order->setStatus($param['status']);
                }

                $order->save();
                ++$numAffectedOrders;
                $this->gridPool->refreshByOrderId($order->getId());
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->_errors[] = __('Can not update order #%1: %2', $orderIncrementId, $errorMessage);
            }

            unset($order);
        }

        return ($numAffectedOrders)
            ? __('Total of %1 order(s) have been successfully updated.', $numAffectedOrders)
            : '';
    }
}
