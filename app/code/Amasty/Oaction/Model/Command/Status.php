<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command;

use Amasty\Oaction\Helper\Data;
use Amasty\Oaction\Model\Command;
use Amasty\Oaction\Model\ResourceModel\Order\StateResolver;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Phrase;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Magento\Sales\Model\ResourceModel\GridPool;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusFactory;

class Status extends Command
{
    public const CUSTOM_STATUS_SEPARATOR = '_';

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

    /**
     * @var OrderConfig
     */
    private $orderConfig;

    /**
     * @var StateResolver
     */
    private $stateResolver;

    public function __construct(
        Data $helper,
        GridPool $gridPool,
        OrderStatusFactory $orderStatusFactory,
        OrderRepositoryInterface $orderRepository,
        OrderCommentSender $commentSender,
        OrderConfig $orderConfig,
        StateResolver $stateResolver
    ) {
        parent::__construct();
        $this->helper = $helper;
        $this->gridPool = $gridPool;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->orderRepository = $orderRepository;
        $this->commentSender = $commentSender;
        $this->orderConfig = $orderConfig;
        $this->stateResolver = $stateResolver;
    }

    /**
     * Executes the command
     *
     * @param AbstractCollection $collection
     * @param array $param
     * @param array $oaction
     * @return Phrase|string
     */
    public function execute(AbstractCollection $collection, $param, $oaction)
    {
        $numAffectedOrders = 0;
        $status = $param['status'] ?? null;
        if (empty($status)) {
            $this->_errors[] = __('Please specify the status.');

            return '';
        }

        foreach ($collection as $order) {
            /** @var Order $order */
            $orderIncrementId = $order->getIncrementId();
            $order = $this->orderRepository->get($order->getId());

            try {
                if ($this->helper->getModuleConfig('status/check_state')) {
                    $state = $order->getState();
                    $statuses = $this->orderConfig->getStateStatuses($state);

                    if (!array_key_exists($status, $statuses)) {
                        $errorMessage = __('Selected status does not correspond to the state of order.');
                        $this->_errors[] = __('Can not update order #%1: %2', $orderIncrementId, $errorMessage);
                        continue;
                    }
                }

                if (strpos($status, self::CUSTOM_STATUS_SEPARATOR) !== false) {
                    [$newState] = explode(self::CUSTOM_STATUS_SEPARATOR, $status, 2);
                    if (!$this->isStateExist($newState)) {
                        $newState = $this->stateResolver->getStateByStatus($status);
                    }
                } else {
                    $newState = $this->stateResolver->getStateByStatus($status);
                }
                if (!empty($newState)) {
                    $order->setState($newState);
                }

                if ($param['notify']) {
                    $statusHistory = $order->addCommentToStatusHistory($param['comment_text'], $status);
                    $statusHistory->setIsVisibleOnFront(false);
                    $statusHistory->setIsCustomerNotified(true);
                    $this->commentSender->send($order, $statusHistory->getIsCustomerNotified(), $param['comment_text']);
                } else {
                    $order->setStatus($status);
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

    private function isStateExist(string $state): bool
    {
        $states = array_keys($this->orderConfig->getStates());

        return in_array($state, $states);
    }
}
