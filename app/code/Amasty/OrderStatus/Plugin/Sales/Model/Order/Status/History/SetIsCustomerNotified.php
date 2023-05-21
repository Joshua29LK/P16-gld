<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Plugin\Sales\Model\Order\Status\History;

use Amasty\OrderStatus\Block\Adminhtml\Status\Edit\Tab\Email;
use Amasty\OrderStatus\Model\AmOrderStatusRegistry;
use Magento\Sales\Model\Order\Status\History;

class SetIsCustomerNotified
{
    /**
     * @var AmOrderStatusRegistry
     */
    private $amOrderStatusRegistry;

    public function __construct(
        AmOrderStatusRegistry $amOrderStatusRegistry
    ) {
        $this->amOrderStatusRegistry = $amOrderStatusRegistry;
    }

    public function afterSetIsCustomerNotified(
        History $subject,
        History $result
    ): History {
        $amastyStatus = $this->amOrderStatusRegistry->get((string)$subject->getStatus());

        if ($amastyStatus && (int)$amastyStatus->getNotifyByEmail() !== Email::NOTIFY_OPTIONAL) {
            $result->setData('is_customer_notified', (bool)$amastyStatus->getNotifyByEmail());
        }

        return $result;
    }
}
