<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Model\Order;

use Amasty\Flags\Api\Data\OrderFlagInterface;
use Magento\Framework\Model\AbstractModel;

class Flag extends AbstractModel implements OrderFlagInterface
{
    protected function _construct()
    {
        $this->_init(\Amasty\Flags\Model\ResourceModel\Order\Flag::class);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(OrderFlagInterface::ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(OrderFlagInterface::ORDER_ID, $orderId);
    }

    /**
     * @return int
     */
    public function getFlagId()
    {
        return $this->getData(OrderFlagInterface::FLAG_ID);
    }

    /**
     * @param int $flagId
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setFlagId($flagId)
    {
        return $this->setData(OrderFlagInterface::FLAG_ID, $flagId);
    }

    /**
     * @return int
     */
    public function getColumnId()
    {
        return $this->getData(OrderFlagInterface::COLUMN_ID);
    }

    /**
     * @param int $columnId
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setColumnId($columnId)
    {
        return $this->setData(OrderFlagInterface::COLUMN_ID, $columnId);
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->getData(OrderFlagInterface::NOTE);
    }

    /**
     * @param string $note
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setNote($note)
    {
        return $this->setData(OrderFlagInterface::NOTE, $note);
    }
}
