<?php

namespace Bss\CustomizeDeliveryDate\Model;

use Magento\Framework\Model\AbstractModel;

class ZipDelivery extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Bss\CustomizeDeliveryDate\Model\ResourceModel\ZipDelivery::class);
    }
}
