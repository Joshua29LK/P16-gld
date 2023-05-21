<?php

namespace MageArray\Customprice\Model;

use Magento\Framework\Model\AbstractModel;

class Csvprice extends AbstractModel
{
    /**
     * [_construct description]
     *
     * @return [type] [description]
     */
    protected function _construct()
    {
        $this->_init(\MageArray\Customprice\Model\ResourceModel\Csvprice::class);
    }
}
