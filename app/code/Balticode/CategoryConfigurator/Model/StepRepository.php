<?php

namespace Balticode\CategoryConfigurator\Model;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Api\StepRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\StepSearchResultsInterfaceFactory;
use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step as ResourceStep;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step\CollectionFactory as StepCollectionFactory;

class StepRepository implements StepRepositoryInterface
{
    /**
     * @var ResourceStep
     */
    protected $resource;

    /**
     * @var StepFactory
     */
    protected $stepFactory;

    /**
     * @var StepCollectionFactory
     */
    protected $stepCollectionFactory;

    /**
     * @var StepSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @param ResourceStep $resource
     * @param StepFactory $stepFactory
     * @param StepCollectionFactory $stepCollectionFactory
     * @param StepSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceStep $resource,
        StepFactory $stepFactory,
        StepCollectionFactory $stepCollectionFactory,
        StepSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->stepFactory = $stepFactory;
        $this->stepCollectionFactory = $stepCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(StepInterface $step)
    {
        try {
            $this->resource->save($step);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the step: %1', $exception->getMessage()));
        }

        return $step;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($stepId)
    {
        $step = $this->stepFactory->create();
        $this->resource->load($step, $stepId);

        if (!$step->getId()) {
            throw new NoSuchEntityException(__('Step with id "%1" does not exist.', $stepId));
        }

        return $step;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->stepCollectionFactory->create();

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
    public function delete(StepInterface $step)
    {
        try {
            $this->resource->delete($step);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the Step: %1', $exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($stepId)
    {
        return $this->delete($this->getById($stepId));
    }
}
