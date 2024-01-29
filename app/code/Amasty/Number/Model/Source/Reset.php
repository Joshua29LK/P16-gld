<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Reset implements OptionSourceInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->toOptionArray() as $row) {
            $result[$row['value']] = $row['label'];
        }

        return $result;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'value' => '',
            'label' => __('Never'),

        ];
        $options[] = [
            'value' => 'Y-m-d',
            'label' => __('Each Day'),

        ];
        $options[] = [
            'value' => 'Y-m',
            'label' => __('Each Month'),

        ];
        $options[] = [
            'value' => 'Y-01-01',
            'label' => __('Each Year'),

        ];

        return $options;
    }
}
