<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\ResourceModel\Order;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Sales\Api\Data\OrderInterface;

class StateResolver
{
    public const SALES_ORDER_STATUS_STATE_TABLE_NAME = 'sales_order_status_state';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function getStateByStatus(string $status): string
    {
        $connection = $this->resourceConnection->getConnection();
        $mainTable = $this->resourceConnection->getTableName(self::SALES_ORDER_STATUS_STATE_TABLE_NAME);
        $select = $connection->select()
            ->from($mainTable)
            ->reset(Select::COLUMNS)
            ->columns(OrderInterface::STATE)
            ->where(OrderInterface::STATUS . ' = ?', $status);

        return $connection->fetchOne($select);
    }
}
