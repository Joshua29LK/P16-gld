<?php

namespace Balticode\CategoryConfigurator\Model\Product;

use Balticode\CategoryConfigurator\Helper\Config\GeneralConfig;
use Balticode\CategoryConfigurator\Model\Validator\StepsData;

class GlassShapesProvider
{
    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @param GeneralConfig $generalConfig
     */
    public function __construct(GeneralConfig $generalConfig)
    {
        $this->generalConfig = $generalConfig;
    }

    /**
     * @param array $glassShapes
     * @return array
     */
    public function appendDefaultGlassShapesDimensions($glassShapes)
    {
        if (!is_array($glassShapes)) {
            return $glassShapes;
        }

        $height = $this->generalConfig->getGlassShapesDefaultHeight();
        $width = $this->generalConfig->getGlassShapesDefaultWidth();

        foreach ($glassShapes as $key => $value) {
            $glassShapes[$key][StepsData::ARRAY_INDEX_HEIGHT] = $height;
            $glassShapes[$key][StepsData::ARRAY_INDEX_WIDTH] = $width;
            $glassShapes[$key][StepsData::ARRAY_INDEX_TOP_WIDTH] = $width;
            $glassShapes[$key][StepsData::ARRAY_INDEX_BOTTOM_WIDTH] = $width;
        }

        return $glassShapes;
    }
}