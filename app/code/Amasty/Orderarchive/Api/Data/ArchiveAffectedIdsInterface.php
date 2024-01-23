<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Api\Data;

/**
 * Interface ArchiveAffectedIdsInterface
 * Contains result of Archive Actions, like affected Order Ids, Invoice Ids etc.
 *
 * @api
 * @since 1.1.0
 */
interface ArchiveAffectedIdsInterface
{
    /**
     * @return int[]
     */
    public function getOrderIds();

    /**
     * @return int[]
     */
    public function getInvoiceIds();

    /**
     * @return int[]
     */
    public function getCreditmemoIds();

    /**
     * @return int[]
     */
    public function getShipmentIds();

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setOrderIds($ids);

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setInvoiceIds($ids);

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setCreditmemoIds($ids);

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setShipmentIds($ids);
}
