<?php

namespace Balticode\CategoryConfigurator\Block\Adminhtml\Step\Edit;

class SaveButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Step'),
            'class' => 'save primary',
            'sort_order' => 90,
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ]
        ];
    }
}
