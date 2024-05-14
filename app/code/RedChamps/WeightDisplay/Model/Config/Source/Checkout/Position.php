<?php
namespace RedChamps\WeightDisplay\Model\Config\Source\Checkout;

class Position
{
    const SUMMARY = 'summary';

    const SHIPPING = 'shipping_step';

    const BOTH = 'both';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SUMMARY,
                'label'=> __('Summary Block')
            ],
            [
                'value' => self::SHIPPING,
                'label'=> __('Shipping Step')
            ],
            [
                'value' => self::BOTH,
                'label'=> __('Both')
            ]
        ];
    }
}
