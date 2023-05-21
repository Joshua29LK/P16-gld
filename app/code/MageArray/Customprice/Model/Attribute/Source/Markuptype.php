<?php

namespace MageArray\Customprice\Model\Attribute\Source;

class Markuptype extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var $_options
     */
    protected $_options;

    /**
     * [getAllOptions description]
     *
     * @return [type] [description]
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => 'Select Options', 'value' => ''],
            ['label' => 'Fixed', 'value' => 'fixed'],
            ['label' => 'Percent', 'value' => 'percent']
        ];
        return $this->_options;
    }
}
