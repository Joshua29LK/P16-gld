<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Block\Adminhtml\Sales\Order\Total\Item;

use Magento\Sales\Block\Adminhtml\Order\Totals\Item;

class Adjustments extends Item
{
    protected $_beforeCondition = null;

    public function getBeforeCondition()
    {
        if ($this->_beforeCondition === null) {
            $this->_beforeCondition = false;
            $configReader = $this->getConfigReader();
            $taxClass = $configReader->getTaxSetting('tax_class');
            if ($taxClass || (!$taxClass && $configReader->getTaxSetting('before_tax'))) {
                $this->_beforeCondition = ['tax', 'grand_total'];
            }
        }
        return $this->_beforeCondition;
    }

    public function getConfigReader()
    {
        return $this->getViewModel()->getConfigReader();
    }

    public function decodeAdjustments($adjustments)
    {
        return $this->getViewModel()->getAdjustmentmanager()->decodeAdjustments($adjustments);
    }
}
