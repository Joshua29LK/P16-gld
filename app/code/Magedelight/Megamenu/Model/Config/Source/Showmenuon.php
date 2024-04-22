<?php
namespace Magedelight\Megamenu\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Showmenuon implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'hover',
                'label' => __('Hover'),
            ],
            [
                'value' => 'click',
                'label' => __('Click'),
            ]
        ];
    }
}
