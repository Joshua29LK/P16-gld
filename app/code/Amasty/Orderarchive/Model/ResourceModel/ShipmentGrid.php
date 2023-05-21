<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Model\ResourceModel;

class ShipmentGrid extends \Amasty\Orderarchive\Model\ArchiveAbstract
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\Orderarchive\Model\ArchiveFactory::SHIPMENT_ARCHIVE_NAMESPACE,
            \Amasty\Orderarchive\Model\ArchiveAbstract::ARCHIVE_ENTITY_ID
        );
    }
}
