<?php

namespace FME\CanonicalUrl\Model\Config\Source;

class Selectcategorypath implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => '1', 'label' => __('Include Main Category Only')],
                ['value' => '2', 'label' => __('Include Sub Categories with Main Category')]
            ];
    }
}
