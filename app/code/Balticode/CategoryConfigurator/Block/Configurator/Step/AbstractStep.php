<?php

namespace Balticode\CategoryConfigurator\Block\Configurator\Step;

use Balticode\CategoryConfigurator\Helper\Config\GeneralConfig;
use Balticode\CategoryConfigurator\Model\CurrencySymbolProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Block\Configurator\Step\StepInterface as StepBlockInterface;

abstract class AbstractStep extends Template implements StepBlockInterface
{
    /**
     * @var StepInterface
     */
    protected $step;

    /**
     * @var ConfiguratorInterface
     */
    protected $configurator;

    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var CurrencySymbolProvider
     */
    protected $currencySymbolProvider;

    /**
     * @param Context $context
     * @param StepInterface $step
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param GeneralConfig $generalConfig
     * @param CurrencySymbolProvider $currencySymbolProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        StepInterface $step,
        ConfiguratorRepositoryInterface $configuratorRepository,
        GeneralConfig $generalConfig,
        CurrencySymbolProvider $currencySymbolProvider,
        array $data = []
    ) {
        $this->step = $step;
        $this->configuratorRepository = $configuratorRepository;
        $this->generalConfig = $generalConfig;
        $this->currencySymbolProvider = $currencySymbolProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->step->getTitle();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->step->getDescription();
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->step->getInfo();
    }

    /**
     * @return int
     */
    public function isFullWidth()
    {
        return $this->step->isFullWidth();
    }

    /**
     * @return ConfiguratorInterface
     * @throws LocalizedException
     */
    public function getConfigurator()
    {
        if (null !== $this->configurator) {
            return $this->configurator;
        }

        $this->configurator = $this->configuratorRepository->getById($this->step->getConfiguratorId());

        return $this->configurator;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getStepHtmlId($this->step->getStepId());
    }

    /**
     * @return string|null
     */
    public function getParentHtmlId()
    {
        $parentId = $this->step->getParentId();

        if (!$parentId) {
            return null;
        }

        return $this->getStepHtmlId($parentId);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->step->getType();
    }

    /**
     * @return null|string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbolProvider->getCurrencySymbol();
    }

    /**
     * @return null|string
     */
    public function getStepCarouselItemCount()
    {
        if ($this->isFullWidth()) {
            return $this->generalConfig->getFullStepCarouselItemCount();
        }

        return $this->getHalfStepCarouselItemCount();
    }

    /**
     * @return null|string
     */
    public function getHalfStepCarouselItemCount()
    {
        return $this->generalConfig->getHalfStepCarouselItemCount();
    }

    /**
     * @return string
     */
    public function getStepSizeHtmlClass()
    {
        if ($this->isFullWidth()) {
            return self::STEP_CLASS_FULL_SIZE;
        }

        return self::STEP_CLASS_HALF_SIZE;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->step->getSortOrder();
    }

    /**
     * @param int $id
     * @return string
     */
    protected function getStepHtmlId($id)
    {
        return self::STEP_ID_PREFIX . $id;
    }
}
