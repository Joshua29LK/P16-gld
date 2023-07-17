<?php
namespace RedChamps\WeightDisplay\Model\Config\Source\Checkout;

class Position
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'summary',
                'label'=> __('Summary Block')
            ],
            [
                'value' => 'shipping_step',
                'label'=> __('Shipping Step')
            ],
            [
                'value' => 'both',
                'label'=> __('Both')
            ]
        ];
    }
}
