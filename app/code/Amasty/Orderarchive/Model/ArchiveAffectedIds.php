<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Model;

use Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface;

/**
 * @since 1.1.0
 */
class ArchiveAffectedIds extends \Magento\Framework\DataObject implements ArchiveAffectedIdsInterface
{
    /**
     * @return int[]
     */
    public function getOrderIds()
    {
        return $this->_getData('order');
    }

    /**
     * @return int[]
     */
    public function getInvoiceIds()
    {
        return $this->_getData('invoice');
    }

    /**
     * @return int[]
     */
    public function getCreditmemoIds()
    {
        return $this->_getData('creditmemo');
    }

    /**
     * @return int[]
     */
    public function getShipmentIds()
    {
        return $this->_getData('shipment');
    }

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setOrderIds($ids)
    {
        $this->setData('order', $ids);
        return $this;
    }

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setInvoiceIds($ids)
    {
        $this->setData('invoice', $ids);
        return $this;
    }

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setCreditmemoIds($ids)
    {
        $this->setData('creditmemo', $ids);
        return $this;
    }

    /**
     * @param int[] $ids
     *
     * @return $this
     */
    public function setShipmentIds($ids)
    {
        $this->setData('shipment', $ids);
        return $this;
    }
}
