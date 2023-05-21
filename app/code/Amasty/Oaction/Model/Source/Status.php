<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Source;

class Status extends \Magento\Sales\Model\Config\Source\Order\Status
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[0] = [
            'value' => '',
            'label' => __('Magento Default')
        ];
        return $options;
    }
}
