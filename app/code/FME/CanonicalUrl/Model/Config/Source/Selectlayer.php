<?php

namespace FME\CanonicalUrl\Model\Config\Source;

class Selectlayer implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 'no', 'label' => __('No')],
                ['value' => 'filteredpage', 'label' => __('Yes, For the Filtered Page')],
                ['value' => 'currentcat', 'label' => __('Yes, For the Current Category')]

            ];
    }
}
