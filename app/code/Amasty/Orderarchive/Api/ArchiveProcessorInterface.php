<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Api;

/**
 * Interface for Order Archive Management
 *
 * @api
 * @sine 1.1.0
 */
interface ArchiveProcessorInterface
{
    /**
     * Operation moves orders and order-related entities to archive tables amastyorderarchive%.
     * Operation affect all the entities associated with the order:
     * invoice, credit memos, shipping if they exist for order.
     *
     * @param int[] $orderIds
     *
     * @return \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface
     */
    public function moveToArchive($orderIds);

    /**
     * Operation moves orders and order-related entities to archive tables amastyorderarchive%.
     * Operation affect all the entities associated with the order:
     * invoice, credit memos, shipping if they exist for order.
     *
     * @return \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface
     */
    public function moveAllToArchive();

    /**
     * Completely removes orders and related data from the database without the possibility of their recovery.
     *
     * @api
     * @param int[] $orderIds
     *
     * @return \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface
     */
    public function removePermanently($orderIds);

    /**
     * Operation moves orders and order-related entities from archive tables “amastyorderarchive%”.
     * Operation affect all the entities associated with the order:
     * invoice, credit memos, shipping if they exist for order.
     *
     * @param int[] $orderIds
     *
     * @return \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface
     */
    public function removeFromArchive($orderIds);
}
