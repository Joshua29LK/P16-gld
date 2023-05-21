<?php
namespace Bss\CustomToolTipCO\Model\ResourceModel\MinIncrementQty;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(
            \Bss\CustomToolTipCO\Model\ToolTipCO::class,
            \Bss\CustomToolTipCO\Model\ResourceModel\ToolTipCO::class
        );
    }
}
