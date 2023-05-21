<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Plugin\Sales\Model\Order\Email\Sender\OrderSender;

use Amasty\OrderStatus\Block\Adminhtml\Status\Edit\Tab\Email;
use Amasty\OrderStatus\Model\AmOrderStatusRegistry;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class SendStatusNotificationOnOrderSend
{
    /**
     * @var OrderCommentSender
     */
    private $orderCommentSender;

    /**
     * @var AmOrderStatusRegistry
     */
    private $amOrderStatusRegistry;

    public function __construct(
        OrderCommentSender $orderCommentSender,
        AmOrderStatusRegistry $amOrderStatusRegistry
    ) {
        $this->orderCommentSender = $orderCommentSender;
        $this->amOrderStatusRegistry = $amOrderStatusRegistry;
    }

    public function afterSend(OrderSender $subject, bool $result, Order $order): bool
    {
        $amastyStatus = $this->amOrderStatusRegistry->get($order->getStatus());

        if ($amastyStatus && (int)$amastyStatus->getNotifyByEmail() === Email::NOTIFY_ENABLED) {
            $this->orderCommentSender->send($order);
        }

        return $result;
    }
}
