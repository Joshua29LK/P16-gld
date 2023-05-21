<?php

namespace MageArray\Customprice\Model\ResourceModel\Csvprice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * [_construct description]
     *
     * @return [type] [description]
     */
    protected function _construct()
    {
        $this->_init(
            \MageArray\Customprice\Model\Csvprice::class,
            \MageArray\Customprice\Model\ResourceModel\Csvprice::class
        );
    }
}
