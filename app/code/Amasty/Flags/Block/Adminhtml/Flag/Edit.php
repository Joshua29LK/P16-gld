<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Block\Adminhtml\Flag;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;

class Edit extends FormContainer
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_flag';
        $this->_blockGroup = 'Amasty_Flags';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ],
                ]
            ],
            10
        );
    }

    protected function _prepareLayout()
    {
        $flag = $this->_coreRegistry->registry('amflags_flag');

        if (!$flag->getId()) {
            $this->removeButton('delete');
        }

        parent::_prepareLayout();
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $model = $this->_coreRegistry->registry('amflags_flag');
        if ($model->getId()) {
            $title = __("Edit Flag '%1'", $model->getName());
        } else {
            $title = __("New Flag");
        }

        return $title;
    }
}
