<?php

namespace Balticode\CategoryConfigurator\Model\Config\Source;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Model\Step\ConfiguratorProvider;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Option\ArrayInterface;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step\Collection;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step\CollectionFactory;

class Step implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ConfiguratorProvider
     */
    protected $configuratorProvider;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Http $request
     * @param ConfiguratorProvider $configuratorProvider
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Http $request,
        ConfiguratorProvider $configuratorProvider
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->configuratorProvider = $configuratorProvider;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(StepInterface::ENABLE, 1);

        $stepId = $this->request->getParam(StepInterface::STEP_ID);

        if (!$stepId) {
            $configuratorId = $this->request->getParam(StepInterface::CONFIGURATOR_ID);
        } else {
            $configuratorId = $this->configuratorProvider->getConfiguratorId($stepId);
        }

        if ($configuratorId) {
            $collection = $collection->addFieldToFilter(StepInterface::CONFIGURATOR_ID, $configuratorId);
        }

        return $collection->toOptionArray();
    }
}