<?php
namespace RedChamps\ShareCart\Model\ResourceModel\ShareCart;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \RedChamps\ShareCart\Model\ShareCart::class,
            \RedChamps\ShareCart\Model\ResourceModel\ShareCart::class
        );
    }
}
