<?php

namespace Balticode\CategoryConfigurator\Block\Configurator\Step;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Model\Config\Source\Type;
use Magento\Framework\ObjectManagerInterface;

class StepFactoryMethod
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param StepInterface $step
     * @return AbstractStep
     */
    public function create(StepInterface $step)
    {
        $data['step'] = $step;

        switch ($step->getType()) {
            case Type::CATEGORY:
            case Type::FIRST:
                return $this->objectManager->create(StepCategory::class, $data);
            case Type::RELATED:
                return $this->objectManager->create(StepRelated::class, $data);
            case Type::GLASS_SHAPES:
                return $this->objectManager->create(StepGlassShapes::class, $data);
        }

        return $this->objectManager->create(StepDefault::class, $data);
    }
}