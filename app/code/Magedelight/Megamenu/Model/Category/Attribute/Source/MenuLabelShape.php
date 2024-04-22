<?php
namespace Magedelight\Megamenu\Model\Category\Attribute\Source;

class MenuLabelShape extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value'=>'square','label'=>"New"],
                ['value'=>'square-pointer','label'=>'New'],
                ['value'=>'rounded','label'=>'New'],
                ['value'=>'circle','label'=>'New'],
                ['value'=>'circle-pointer','label'=>'New'],
            ];
        }
        return $this->_options;
    }
}
