<?php

namespace Balticode\CategoryConfigurator\Block\Adminhtml\Step\Edit;

use Balticode\CategoryConfigurator\Model\Step\ConfiguratorProvider;
use Magento\Backend\Block\Widget\Context;

class BackButton extends GenericButton
{
    /**
     * @var ConfiguratorProvider
     */
    protected $configuratorProvider;

    /**
     * @param Context $context
     * @param ConfiguratorProvider $configuratorProvider
     */
    public function __construct(Context $context, ConfiguratorProvider $configuratorProvider)
    {
        $this->configuratorProvider = $configuratorProvider;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/configurator/edit', [
            'configurator_id' => $this->configuratorProvider->getConfiguratorId($this->getModelId())
        ]);
    }
}
