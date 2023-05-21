<?php

namespace Balticode\CategoryConfigurator\Model\Step;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\StepRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Exception;

class ConfiguratorProvider
{
    /**
     * @var StepRepositoryInterface
     */
    protected $stepRepository;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @param StepRepositoryInterface $stepRepository
     * @param Http $request
     */
    public function __construct(StepRepositoryInterface $stepRepository, Http $request)
    {
        $this->stepRepository = $stepRepository;
        $this->request = $request;
    }

    /**
     * @param $stepId
     * @return null|string
     */
    public function getConfiguratorId($stepId)
    {
        if (!$stepId) {
            return $this->request->getParam(ConfiguratorInterface::CONFIGURATOR_ID);
        }

        try {
            $step = $this->stepRepository->getById($stepId);
        } catch (Exception $e) {
            return null;
        }

        if (!$step->getStepId() || !$configuratorId = $step->getConfiguratorId()) {
            return null;
        }

        return $configuratorId;
    }
}