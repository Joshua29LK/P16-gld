<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Model\ResourceModel\InvoiceGrid;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Amasty\Orderarchive\Model\InvoiceGrid',
            'Amasty\Orderarchive\Model\ResourceModel\InvoiceGrid');
    }
}
