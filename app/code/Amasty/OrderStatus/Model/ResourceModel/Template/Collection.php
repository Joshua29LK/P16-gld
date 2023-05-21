<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\ResourceModel\Template;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\OrderStatus\Model\Template::class,
            \Amasty\OrderStatus\Model\ResourceModel\Template::class
        );
    }

    public function loadTemplateId($statusId, $storeId)
    {
        $connection = $this->getConnection();
        //phpcs:ignore
        $sql = 'SELECT template_id FROM `' . $this->getMainTable() 
            . '` WHERE `status_id` = "' . $statusId . '" AND `store_id` = "' . $storeId . '" ' ;
        return $connection->fetchOne($sql);
    }
}
