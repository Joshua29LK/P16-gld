<?php
namespace Bss\CustomToolTipCO\Model;

use Magento\Framework\Model\AbstractModel;

class ToolTipCO extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(\Bss\CustomToolTipCO\Model\ResourceModel\ToolTipCO::class);
    }

     /**
     * @param int $optionTypeId
     * @return ImageUrl
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionId($optionId)
    {
        $data = $this->_getResource()->loadByOptionId($optionId);
        return $data ? $this->setData($data) : $this;
    }

}
