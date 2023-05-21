<?php
namespace Hoofdfabriek\BePostcodeNL\Model\Config\Source;

/**
 * Class Validation
 */
class Validation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'strict', 'label' => __('Strict')],
            ['value' => 'street', 'label' => __('Street')],
            ['value' => 'number', 'label' => __('Number')],
            ['value' => 'none', 'label' => __('None')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'strict' => __('Strict'),
            'street' => __('Street'),
            'number' => __('Number'),
            'none' => __('None'),
        ];
    }
}
