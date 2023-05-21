<?php

namespace Balticode\CategoryConfigurator\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Values implements ArrayInterface
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @param array $values
     */
    public function __construct($values = [])
    {
        $this->values = $this->values + $values;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->values as $label => $value) {
            $options[] = ['label' => __($label), 'value' => $value];
        }

        return $options;
    }
}