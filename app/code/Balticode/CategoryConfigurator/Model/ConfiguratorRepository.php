<?php

namespace Balticode\CategoryConfigurator\Model;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorSearchResultsInterfaceFactory;
use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator as ResourceConfigurator;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator\CollectionFactory as ConfiguratorCollectionFactory;

class ConfiguratorRepository implements ConfiguratorRepositoryInterface
{
    /**
     * @var ResourceConfigurator
     */
    protected $resource;

    /**
     * @var ConfiguratorFactory
     */
    protected $configuratorFactory;

    /**
     * @var ConfiguratorCollectionFactory
     */
    protected $configuratorCollectionFactory;

    /**
     * @var ConfiguratorSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @param ResourceConfigurator $resource
     * @param ConfiguratorFactory $configuratorFactory
     * @param ConfiguratorCollectionFactory $configuratorCollectionFactory
     * @param ConfiguratorSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceConfigurator $resource,
        ConfiguratorFactory $configuratorFactory,
        ConfiguratorCollectionFactory $configuratorCollectionFactory,
        ConfiguratorSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->configuratorFactory = $configuratorFactory;
        $this->configuratorCollectionFactory = $configuratorCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ConfiguratorInterface $configurator)
    {
        try {
            $this->resource->save($configurator);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the configurator: %1', $exception->getMessage()));
        }

        return $configurator;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($configuratorId)
    {
        $configurator = $this->configuratorFactory->create();
        $this->resource->load($configurator, $configuratorId);

        if (!$configurator->getId()) {
            throw new NoSuchEntityException(__('Configurator with id "%1" does not exist.', $configuratorId));
        }

        return $configurator;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->configuratorCollectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];

            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);

                    continue;
                }

                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }

            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();

        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }

        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ConfiguratorInterface $configurator)
    {
        try {
            $this->resource->delete($configurator);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the Configurator: %1', $exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($configuratorId)
    {
        return $this->delete($this->getById($configuratorId));
    }
}
