<?php
namespace RedChamps\Core\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TimeStamp extends AbstractDb
{
    public function _construct()
    {
        $this->_init('redchamps_timestamp', 'id');
    }
}
