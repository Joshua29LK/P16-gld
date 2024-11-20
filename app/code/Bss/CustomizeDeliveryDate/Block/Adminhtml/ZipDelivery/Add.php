<?php
namespace Bss\CustomizeDeliveryDate\Block\Adminhtml\ZipDelivery;

/**
 * Zip Delivery add block
 */
class Add extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Update Save buttons.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_group';
        $this->_blockGroup = 'Bss_CustomizeDeliveryDate';
        $this->buttonList->update('save', 'label', __('Save'));
    }

    /**
     * Retrieve the header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Add Zip Delivery');
    }
}