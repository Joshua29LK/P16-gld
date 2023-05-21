<?php

namespace FME\CanonicalUrl\Model\Config\Source;

class Addtrailingslash implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 'crop', 'label' => __('Crop')],
                ['value' => 'add', 'label' => __('Add')]
            ];
    }
}
