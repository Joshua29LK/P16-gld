<?php

namespace Balticode\CategoryConfigurator\Model\Configurator;

use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator\Collection;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var Normalizer
     */
    protected $normalizer;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Normalizer $normalizer
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        Normalizer $normalizer,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->normalizer = $normalizer;
        $this->collection = $collectionFactory->create();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $model) {
            $model->setImageName($this->normalizer->getNormalizedImageData($model->getImageName()));
            $this->loadedData[$model->getId()] = $model->getData();
        }

        $data = $this->dataPersistor->get('balticode_categoryconfigurator_configurator');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('balticode_categoryconfigurator_configurator');
        }
        
        return $this->loadedData;
    }
}
