<?php

namespace Balticode\CategoryConfigurator\Api;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ConfiguratorRepositoryInterface
{
    /**
     * @param ConfiguratorInterface $configurator
     * @return ConfiguratorInterface
     * @throws LocalizedException
     */
    public function save(ConfiguratorInterface $configurator);

    /**
     * @param int $configuratorId
     * @return ConfiguratorInterface
     * @throws LocalizedException
     */
    public function getById($configuratorId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ConfiguratorSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param ConfiguratorInterface $configurator
     * @return bool
     * @throws LocalizedException
     */
    public function delete(ConfiguratorInterface $configurator);

    /**
     * @param string $configuratorId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($configuratorId);
}
