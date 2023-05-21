<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Model;

class ShipmentGrid extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Amasty\Orderarchive\Model\ResourceModel\ShipmentGrid');
    }
}
