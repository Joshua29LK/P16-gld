<?php

namespace Balticode\CategoryConfigurator\Model\Configurator;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\StepRepositoryInterface;
use Balticode\CategoryConfigurator\Helper\SearchCriteria\Step as StepSearchCriteria;
use Balticode\CategoryConfigurator\Model\Config\Source\Type;
use Balticode\CategoryConfigurator\Model\StepFactory;
use Magento\Framework\Exception\LocalizedException;

class DefaultStepsProcessor
{
    /**
     * @var StepFactory
     */
    protected $stepFactory;

    /**
     * @var StepRepositoryInterface
     */
    protected $stepRepository;

    /**
     * @var StepSearchCriteria
     */
    protected $stepSearchCriteria;

    /**
     * @param StepFactory $stepFactory
     * @param StepRepositoryInterface $stepRepository
     * @param StepSearchCriteria $stepSearchCriteria
     */
    public function __construct(
        StepFactory $stepFactory,
        StepRepositoryInterface $stepRepository,
        StepSearchCriteria $stepSearchCriteria
    ) {
        $this->stepFactory = $stepFactory;
        $this->stepRepository = $stepRepository;
        $this->stepSearchCriteria = $stepSearchCriteria;
    }

    /**
     * @param ConfiguratorInterface $configurator
     * @throws LocalizedException
     */
    public function processDefaultStepsRequest($configurator)
    {
        $configuratorId = $configurator->getConfiguratorId();

        if ($this->hasSteps($configuratorId) || !$categoryId = $configurator->getCategoryId()) {
            return;
        }

        $firstStepId = $this->createStep($configuratorId, 10, Type::FIRST, 'First Step', $categoryId);
        $this->createStep($configuratorId, 20, Type::GLASS_SHAPES, 'Glass Shapes', null, $firstStepId);
    }

    /**
     * @param int $configuratorId
     * @param int $sortOrder
     * @param string $type
     * @param string $title
     * @param null|int $categoryId
     * @param null|int $parentId
     * @return int|null
     * @throws LocalizedException
     */
    protected function createStep($configuratorId, $sortOrder, $type, $title, $categoryId = null, $parentId = null)
    {
        $step = $this->stepFactory->create();

        $step->setConfiguratorId($configuratorId);
        $step->setEnable(true);
        $step->setFullWidthFlag(true);
        $step->setSortOrder($sortOrder);
        $step->setType($type);
        $step->setTitle($title);

        if ($categoryId) {
            $step->setCategoryId($categoryId);
        }

        if ($parentId) {
            $step->setParentId($parentId);
        }

        $this->stepRepository->save($step);

        return $step->getStepId();
    }

    /**
     * @param int $configuratorId
     * @return bool
     */
    protected function hasSteps($configuratorId)
    {
        $stepSearchCriteria = $this->stepSearchCriteria->getEnabledConfiguratorStepsSearchCriteria($configuratorId);

        try {
            $configuratorSteps = $this->stepRepository->getList($stepSearchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return false;
        }

        if (count($configuratorSteps) > 0) {
            return true;
        }

        return false;
    }
}