<?php

namespace Balticode\CategoryConfigurator\Block\Adminhtml\Configurator\Edit;

class SaveAndContinueButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'sort_order' => 80,
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ]
        ];
    }
}
