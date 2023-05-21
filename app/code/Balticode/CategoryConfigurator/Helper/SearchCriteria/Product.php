<?php

namespace Balticode\CategoryConfigurator\Helper\SearchCriteria;

use Balticode\CategoryConfigurator\Model\ResourceModel\Step as StepResource;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

class Product
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
     * @param string $ids
     * @param string|null $typeId
     * @return SearchCriteria
     */
    public function getProductsByListOfIdsSearchCriteria($ids, $typeId = null)
    {
        $idsFilter = $this->filterBuilder
            ->create()
            ->setField('entity_id')
            ->setConditionType('in')
            ->setValue($ids);

        $idsFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$idsFilter]);

        $filterGroups = [$idsFilterGroup];

        if ($typeId) {
            $filterGroups[] = $this->getProductTypeIdFilterGroup($typeId);
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setFilterGroups($filterGroups);

        $sortOrder = $this->sortOrderBuilder
            ->create()
            ->setField(StepResource::ATTRIBUTE_CODE_SORT_ORDER_IN_STEP)
            ->setDirection(SortOrder::SORT_ASC);

        $searchCriteria->setSortOrders([$sortOrder]);

        return $searchCriteria;
    }

    /**
     * @param string $typeId
     * @return AbstractSimpleObject
     */
    protected function getProductTypeIdFilterGroup($typeId)
    {
        $typeIdFilter = $this->filterBuilder
            ->create()
            ->setField('type_id')
            ->setConditionType('eq')
            ->setValue($typeId);

        $typeIdFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$typeIdFilter]);

        return $typeIdFilterGroup;
    }
}