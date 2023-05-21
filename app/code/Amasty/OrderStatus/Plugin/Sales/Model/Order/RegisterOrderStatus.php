<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Plugin\Sales\Model\Order;

use Amasty\OrderStatus\Model\AmOrderStatusRegistry;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Model\Order as SalesOrder;

class RegisterOrderStatus
{
    /**
     * @var AmOrderStatusRegistry
     */
    private $amOrderStatusRegister;

    public function __construct(
        AmOrderStatusRegistry $amOrderStatusRegister
    ) {
        $this->amOrderStatusRegister = $amOrderStatusRegister;
    }

    public function afterAddCommentToStatusHistory(
        SalesOrder $subject,
        OrderStatusHistoryInterface $result
    ): OrderStatusHistoryInterface {
        $this->amOrderStatusRegister->register($result->getStatus());

        return $result;
    }
}
