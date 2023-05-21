<?php

namespace Balticode\CategoryConfigurator\Helper\SearchCriteria;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

class Step
{
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @param int $configuratorId
     * @return SearchCriteria
     */
    public function getEnabledConfiguratorStepsSearchCriteria($configuratorId)
    {
        $configuratorIdFilter = $this->filterBuilder
            ->create()
            ->setField(StepInterface::CONFIGURATOR_ID)
            ->setConditionType('eq')
            ->setValue($configuratorId);

        $enabledFilter = $this->filterBuilder
            ->create()
            ->setField(StepInterface::ENABLE)
            ->setConditionType('eq')
            ->setValue(1);

        $configuratorIdFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$configuratorIdFilter]);

        $enabledFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$enabledFilter]);

        $sortOrder = $this->sortOrderBuilder->create()
            ->setField(StepInterface::SORT_ORDER)
            ->setDirection(SortOrder::SORT_ASC);

        $searchCriteria = $this->searchCriteriaBuilder->create();

        $searchCriteria->setSortOrders([$sortOrder]);
        $searchCriteria->setFilterGroups([$configuratorIdFilterGroup, $enabledFilterGroup]);

        return $searchCriteria;
    }
}