<?php

namespace Balticode\CategoryConfigurator\Block\Adminhtml\Configurator\Edit;

class AddStepButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Add Step'),
            'on_click' => sprintf("location.href = '%s';", $this->getAddStepUrl()),
            'sort_order' => 90,
        ];
    }

    /**
     * @return string
     */
    public function getAddStepUrl()
    {
        return $this->getUrl('*/step/new', ['configurator_id' => $this->getModelId()]);
    }
}
