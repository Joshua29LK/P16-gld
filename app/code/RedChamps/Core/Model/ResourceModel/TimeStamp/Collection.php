<?php
namespace RedChamps\Core\Model\ResourceModel\TimeStamp;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \RedChamps\Core\Model\TimeStamp::class,
            \RedChamps\Core\Model\ResourceModel\TimeStamp::class
        );
    }

    public function getProcessedOrdersTimeStampRecord()
    {
        return $this->addFieldToFilter("type", "orders")->getFirstItem();
    }

    public function getFollowupEmailTimeStampRecord()
    {
        return $this->addFieldToFilter("type", "follow_up_email")->getFirstItem();
    }

    public function saveTimeStampRecord($timeStampRecord)
    {
        $this->getResource()->save($timeStampRecord);
    }
}
