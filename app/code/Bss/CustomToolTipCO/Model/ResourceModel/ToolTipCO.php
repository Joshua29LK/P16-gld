<?php
namespace Bss\CustomToolTipCO\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ToolTipCO extends AbstractDb
{
    /**
     * construct
     */
    public function _construct()
    {
        $this->_init('bss_customizetooltip_custom_option', 'id');
    }

    /**
     * @param int $optionTypeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionId($optionId)
    {
        $bind = ['option_id' => $optionId];
        $select = $this->getConnection()->select()->from(
            $this->getMainTable()
        )->where(
            'option_id = :option_id'
        )->limit(1);

        return $this->getConnection()->fetchRow($select, $bind);
    }

}
