<?php

namespace Balticode\CategoryConfigurator\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator\Collection;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator\CollectionFactory;

class Configurator implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        return $collection->toOptionArray();
    }
}