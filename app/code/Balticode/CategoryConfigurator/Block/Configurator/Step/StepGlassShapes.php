<?php

namespace Balticode\CategoryConfigurator\Block\Configurator\Step;

class StepGlassShapes extends AbstractStep
{
    const GLASS_SHAPES_ACTION_PATH = 'category/configurator_step/glassShapes';

    /**
     * @var string
     */
    protected $_template = 'configurator/step/glass_shapes.phtml';

    /**
     * @return string
     */
    public function getGlassShapesActionUrl()
    {
        return $this->getUrl(self::GLASS_SHAPES_ACTION_PATH);
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMinimumHeight()
    {
        return $this->generalConfig->getGlassShapesMinimumHeight();
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMinimumWidth()
    {
        return $this->generalConfig->getGlassShapesMinimumWidth();
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMaximumHeight()
    {
        return $this->generalConfig->getGlassShapesMaximumHeight();
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMaximumWidth()
    {
        return $this->generalConfig->getGlassShapesMaximumWidth();
    }
}