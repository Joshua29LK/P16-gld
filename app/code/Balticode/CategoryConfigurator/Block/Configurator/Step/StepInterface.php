<?php

namespace Balticode\CategoryConfigurator\Block\Configurator\Step;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;

interface StepInterface
{
    const STEP_ID_PREFIX = 'configurator_step_';
    const STEP_CLASS_FULL_SIZE = 'full-size';
    const STEP_CLASS_HALF_SIZE = 'half-size';

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getInfo();

    /**
     * @return int
     */
    public function isFullWidth();

    /**
     * @return ConfiguratorInterface
     */
    public function getConfigurator();

    /**
     * @return string
     */
    public function getHtmlId();

    /**
     * @return string|null
     */
    public function getParentHtmlId();

    /**
     * @return int
     */
    public function getType();

    /**
     * @return string|null
     */
    public function getCurrencySymbol();

    /**
     * @return string|null
     */
    public function getStepCarouselItemCount();

    /**
     * @return string|null
     */
    public function getHalfStepCarouselItemCount();

    /**
     * @return string
     */
    public function getStepSizeHtmlClass();

    /**
     * @return int
     */
    public function getSortOrder();
}