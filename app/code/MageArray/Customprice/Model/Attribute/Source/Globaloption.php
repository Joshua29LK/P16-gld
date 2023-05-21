<?php

namespace MageArray\Customprice\Model\Attribute\Source;

class Globaloption extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            ['label' => 'Global', 'value' => '2'],
            ['label' => 'Yes', 'value' => '1'],
            ['label' => 'No', 'value' => '0']
        ];
        return $this->_options;
    }
}
