<?php

namespace Balticode\CategoryConfigurator\Block\Adminhtml\Configurator\Edit;

class DeleteButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];

        if ($this->getModelId()) {
            $data = [
                'label' => __('Delete Configurator'),
                'class' => 'delete',
                'sort_order' => 20,
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')'
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['configurator_id' => $this->getModelId()]);
    }
}
