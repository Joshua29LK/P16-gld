<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Model\ResourceModel;

use Amasty\Orderarchive\Model\ArchiveFactory;

class ShipmentGrid extends \Amasty\Orderarchive\Model\ArchiveAbstract
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\Orderarchive\Model\ArchiveFactory::SHIPMENT_ARCHIVE_NAMESPACE,
            \Amasty\Orderarchive\Model\ArchiveAbstract::ARCHIVE_ENTITY_ID
        );
    }

    public function removePermanently(array $selectedIds): array
    {
        $this->removeFromGrid(
            ArchiveFactory::SHIPMENT_ARCHIVE_NAMESPACE,
            $this->prepareParams($selectedIds)
        );

        return [];
    }
}
