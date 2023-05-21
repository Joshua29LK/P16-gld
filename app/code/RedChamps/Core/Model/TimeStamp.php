<?php
namespace RedChamps\Core\Model;

use Magento\Framework\Model\AbstractModel;

class TimeStamp extends AbstractModel
{

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\TimeStamp::class);
    }

    public function getLastProcessed()
    {
        return $this->getData("last_processed");
    }

    public function setLastProcessed($lastProcessed)
    {
        return $this->setData("last_processed", $lastProcessed);
    }
}
