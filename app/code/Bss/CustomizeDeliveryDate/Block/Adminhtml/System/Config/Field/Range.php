<?php
namespace Bss\CustomizeDeliveryDate\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Range extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('from', ['label' => __('From (e.g. 1000)'), 'class' => 'required-entry']);
        $this->addColumn('to', ['label' => __('To (e.g. 2000)'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Range');
    }
}
