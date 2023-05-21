<?php

namespace Balticode\CategoryConfigurator\Api;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Api\Data\StepSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface StepRepositoryInterface
{
    /**
     * @param StepInterface $step
     * @return StepInterface
     * @throws LocalizedException
     */
    public function save(StepInterface $step);

    /**
     * @param int $stepId
     * @return StepInterface
     * @throws LocalizedException
     */
    public function getById($stepId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return StepSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param StepInterface $step
     * @return bool
     * @throws LocalizedException
     */
    public function delete(StepInterface $step);

    /**
     * @param int $stepId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($stepId);
}
