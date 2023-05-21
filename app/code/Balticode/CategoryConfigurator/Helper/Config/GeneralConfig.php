<?php

namespace Balticode\CategoryConfigurator\Helper\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class GeneralConfig
{
    const SECTION_CATEGORY_CONFIGURATOR_CONFIG = 'category_configurator_config/';
    const GROUP_GENERAL = 'general/';
    const FIELD_GLASS_SHAPES_CATEGORY = 'glass_shapes_category';
    const FIELD_GLASS_SHAPES_DEFAULT_HEIGHT = 'glass_shapes_default_height';
    const FIELD_GLASS_SHAPES_DEFAULT_WIDTH = 'glass_shapes_default_width';
    const FIELD_GLASS_SHAPES_MINIMUM_HEIGHT = 'glass_shapes_minimum_height';
    const FIELD_GLASS_SHAPES_MINIMUM_WIDTH = 'glass_shapes_minimum_width';
    const FIELD_GLASS_SHAPES_MAXIMUM_HEIGHT = 'glass_shapes_maximum_height';
    const FIELD_GLASS_SHAPES_MAXIMUM_WIDTH = 'glass_shapes_maximum_width';
    const FIELD_FULL_STEP_CAROUSEL_ITEM_COUNT = 'full_step_carousel_item_count';
    const FIELD_HALF_STEP_CAROUSEL_ITEM_COUNT = 'half_step_carousel_item_count';
    const XML_PATH_GLASS_SHAPES_WIDTH_TITLE = 'category_configurator_config/general/glass_shapes_width_title';
    const XML_PATH_GLASS_SHAPES_HEIGHT_TITLE = 'category_configurator_config/general/glass_shapes_height_title';
    const XML_PATH_GLASS_SHAPES_TOP_WIDTH_TITLE = 'category_configurator_config/general/glass_shapes_top_width_title';
    const XML_PATH_GLASS_SHAPES_BOTTOM_WIDTH_TITLE = 'category_configurator_config/general/glass_shapes_bottom_width_title';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string|null
     */
    public function getGlassShapesCategory()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_GLASS_SHAPES_CATEGORY
        );
    }

    /**
     * @return string|null
     */
    public function getGlassShapesDefaultHeight()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_GLASS_SHAPES_DEFAULT_HEIGHT
        );
    }

    /**
     * @return string|null
     */
    public function getGlassShapesDefaultWidth()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_GLASS_SHAPES_DEFAULT_WIDTH
        );
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMinimumHeight()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_GLASS_SHAPES_MINIMUM_HEIGHT
        );
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMinimumWidth()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_GLASS_SHAPES_MINIMUM_WIDTH
        );
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMaximumHeight()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_GLASS_SHAPES_MAXIMUM_HEIGHT
        );
    }

    /**
     * @return string|null
     */
    public function getGlassShapesMaximumWidth()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_GLASS_SHAPES_MAXIMUM_WIDTH
        );
    }

    /**
     * @return string|null
     */
    public function getFullStepCarouselItemCount()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_FULL_STEP_CAROUSEL_ITEM_COUNT
        );
    }

    /**
     * @return string|null
     */
    public function getHalfStepCarouselItemCount()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_CATEGORY_CONFIGURATOR_CONFIG . self::GROUP_GENERAL . self::FIELD_HALF_STEP_CAROUSEL_ITEM_COUNT
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getGlassShapesWidthTitle($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GLASS_SHAPES_WIDTH_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getGlassShapesHeightTitle($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GLASS_SHAPES_HEIGHT_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getGlassShapesTopWidthTitle($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GLASS_SHAPES_TOP_WIDTH_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getGlassShapesBottomWidthTitle($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GLASS_SHAPES_BOTTOM_WIDTH_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}