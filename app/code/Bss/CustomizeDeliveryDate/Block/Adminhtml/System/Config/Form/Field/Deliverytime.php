<?php
namespace Bss\CustomizeDeliveryDate\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Deliverytime extends AbstractFieldArray
{
    /**
     * Prepare to render the block
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('levertijd_indicatie', ['label' => __('Delivery time')]);
        $this->addColumn('day', ['label' => __('Longest preparation time')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}