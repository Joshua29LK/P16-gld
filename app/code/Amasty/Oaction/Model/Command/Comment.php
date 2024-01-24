<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command;

use Amasty\Oaction\Model\Command;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\OrderRepositoryInterface;

class Comment extends Command
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct();
        $this->orderRepository = $orderRepository;
    }

    public function execute(AbstractCollection $collection, $comment, $oaction)
    {
        $numAffectedOrders = 0;

        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $orderIncrementId = $order->getIncrementId();
            $order = $this->orderRepository->get($order->getId());

            try {
                $order->addCommentToStatusHistory($comment);
                $this->orderRepository->save($order);
                ++$numAffectedOrders;
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->_errors[] = __('Can not update order #%1: %2', $orderIncrementId, $errorMessage);
            }
        }

        return ($numAffectedOrders)
            ? __('Total of %1 order(s) have been successfully updated.', $numAffectedOrders)
            : '';
    }
}
