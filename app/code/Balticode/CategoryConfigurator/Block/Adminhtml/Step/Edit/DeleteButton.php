<?php

namespace Balticode\CategoryConfigurator\Block\Adminhtml\Step\Edit;

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
                'label' => __('Delete Step'),
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
        return $this->getUrl('*/*/delete', ['step_id' => $this->getModelId()]);
    }
}
