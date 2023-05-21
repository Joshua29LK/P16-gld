<?php

namespace Balticode\CategoryConfigurator\Helper\SearchCriteria;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Configurator
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
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param null|int $categoryId
     * @return SearchCriteria
     */
    public function getEnabledConfiguratorsSearchCriteria($categoryId = null)
    {
        $enabledFilter = $this->filterBuilder
            ->create()
            ->setField(ConfiguratorInterface::ENABLE)
            ->setConditionType('eq')
            ->setValue(1);

        $enabledFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$enabledFilter]);

        $filterGroups = [$enabledFilterGroup];

        if ($categoryId) {
            $filterGroups[] = $this->getCategoryFilterGroup($categoryId);
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setFilterGroups($filterGroups);

        return $searchCriteria;
    }

    /**
     * @param string $categoryIds
     * @return SearchCriteria
     */
    public function getEnabledConfiguratorsByCategoryIdsSearchCriteria($categoryIds)
    {
        $enabledFilter = $this->filterBuilder
            ->create()
            ->setField(ConfiguratorInterface::ENABLE)
            ->setConditionType('eq')
            ->setValue(1);

        $categoryIdsFilter = $this->filterBuilder
            ->create()
            ->setField('category_id')
            ->setConditionType('in')
            ->setValue($categoryIds);

        $enabledFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$enabledFilter]);

        $categoryIdsFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$categoryIdsFilter]);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setFilterGroups([$enabledFilterGroup, $categoryIdsFilterGroup]);

        return $searchCriteria;
    }

    /**
     * @param string $imageName
     * @return SearchCriteria
     */
    public function getConfiguratorsByImageNameSearchCriteria($imageName)
    {
        $enabledFilter = $this->filterBuilder
            ->create()
            ->setField(ConfiguratorInterface::IMAGE_NAME)
            ->setConditionType('eq')
            ->setValue($imageName);

        $enabledFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$enabledFilter]);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setFilterGroups([$enabledFilterGroup]);

        return $searchCriteria;
    }

    /**
     * @param $categoryId
     * @return AbstractSimpleObject
     */
    protected function getCategoryFilterGroup($categoryId)
    {
        $categoryIdFilter = $this->filterBuilder
            ->create()
            ->setField(ConfiguratorInterface::CATEGORY_ID)
            ->setConditionType('eq')
            ->setValue($categoryId);

        $categoryIdFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$categoryIdFilter]);

        return $categoryIdFilterGroup;
    }
}