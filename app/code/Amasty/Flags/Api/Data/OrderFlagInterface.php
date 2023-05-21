<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Api\Data;

interface OrderFlagInterface
{
    public const ID = 'id';
    public const ORDER_ID = 'order_id';
    public const FLAG_ID = 'flag_id';
    public const COLUMN_ID = 'column_id';
    public const NOTE = 'note';

    /**
     * @return int
     */
    public function getId();
    
    /**
     * @param int $id
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getOrderId();
    
    /**
     * @param int $orderId
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setOrderId($orderId);

    /**
     * @return int
     */
    public function getFlagId();
    
    /**
     * @param int $flagId
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setFlagId($flagId);

    /**
     * @return int
     */
    public function getColumnId();
    
    /**
     * @param int $columnId
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setColumnId($columnId);

    /**
     * @return string
     */
    public function getNote();
    
    /**
     * @param string $note
     * @return \Amasty\Flags\Api\Data\OrderFlagInterface
     */
    public function setNote($note);
}
