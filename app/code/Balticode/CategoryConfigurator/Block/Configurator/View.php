<?php

namespace Balticode\CategoryConfigurator\Block\Configurator;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\StepRepositoryInterface;
use Balticode\CategoryConfigurator\Block\Configurator\Step\AbstractStep;
use Balticode\CategoryConfigurator\Helper\SearchCriteria\Step as StepSearchCriteria;
use Balticode\CategoryConfigurator\Model\Config\Source\Type;
use Balticode\CategoryConfigurator\Model\CurrencySymbolProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Balticode\CategoryConfigurator\Block\Configurator\Step\StepFactoryMethod;
use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Magento\Framework\View\Element\Template\Context;
use Zend_Json;

class View extends Template
{
    /**
     * @var ConfiguratorInterface|null
     */
    protected $configurator = null;

    /**
     * @var StepFactoryMethod
     */
    protected $stepFactoryMethod;

    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @var CurrencySymbolProvider
     */
    protected $currencySymbolProvider;

    /**
     * @var StepSearchCriteria
     */
    protected $stepSearchCriteria;

    /**
     * @var StepRepositoryInterface
     */
    protected $stepRepository;

    /**
     * @param Context $context
     * @param StepFactoryMethod $stepFactoryMethod
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param CurrencySymbolProvider $currencySymbolProvider
     * @param StepSearchCriteria $stepSearchCriteria
     * @param StepRepositoryInterface $stepRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        StepFactoryMethod $stepFactoryMethod,
        ConfiguratorRepositoryInterface $configuratorRepository,
        CurrencySymbolProvider $currencySymbolProvider,
        StepSearchCriteria $stepSearchCriteria,
        StepRepositoryInterface $stepRepository,
        array $data = []
    ) {
        $this->stepFactoryMethod = $stepFactoryMethod;
        $this->configuratorRepository = $configuratorRepository;
        $this->currencySymbolProvider = $currencySymbolProvider;
        $this->stepSearchCriteria = $stepSearchCriteria;
        $this->stepRepository = $stepRepository;

        parent::__construct($context, $data);
    }

    /**
     * @return AbstractStep[]
     */
    public function getStepBlocks()
    {
        $blocks = [];
        $configurator = $this->getConfigurator();

        if (!$configurator instanceof ConfiguratorInterface || !$configuratorId = $configurator->getConfiguratorId()) {
            return $blocks;
        }

        $stepSearchCriteria = $this->stepSearchCriteria->getEnabledConfiguratorStepsSearchCriteria($configuratorId);

        try {
            $configuratorSteps = $this->stepRepository->getList($stepSearchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return $blocks;
        }

        foreach ($configuratorSteps as $step) {
            $blocks[] = $this->generateStepBlock($step);
        }

        return $blocks;
    }

    /**
     * @param AbstractStep[] $steps
     * @return string
     */
    public function getStepsJsonData($steps)
    {
        $stepsData = [];

        foreach ($steps as $step) {
            if ($step->getType() == Type::DEFAULT) {
                continue;
            }

            $stepsData[$step->getHtmlId()] = $this->formatStepData($step);
        }

        return Zend_Json::encode($stepsData);
    }

    /**
     * @return null|string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbolProvider->getCurrencySymbol();
    }

    /**
     * @return ConfiguratorInterface|null
     */
    protected function getConfigurator()
    {
        if ($this->configurator instanceof ConfiguratorInterface) {
            return $this->configurator;
        }

        $configuratorId = $this->getRequest()->getParam(ConfiguratorInterface::CONFIGURATOR_ID);

        try {
            $configurator = $this->configuratorRepository->getById($configuratorId);
        } catch (LocalizedException $e) {
            $configurator = null;
        }

        $this->configurator = $configurator;

        return $this->configurator;
    }

    /**
     * @param StepInterface $step
     * @return AbstractStep
     */
    protected function generateStepBlock(StepInterface $step)
    {
        return $this->stepFactoryMethod->create($step);
    }

    /**
     * @param AbstractStep $step
     * @return array
     */
    protected function formatStepData($step)
    {
        $stepData = [];
        $stepData['init'] = false;

        $stepType = $step->getType();

        if ($stepType == Type::GLASS_SHAPES || $stepType == Type::RELATED) {
            $stepData['parent'] = $step->getParentHtmlId();
        }

        return $stepData;
    }
}
