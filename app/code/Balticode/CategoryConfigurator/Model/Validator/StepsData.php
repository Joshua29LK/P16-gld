<?php

namespace Balticode\CategoryConfigurator\Model\Validator;

use Balticode\CategoryConfigurator\Helper\Config\GeneralConfig;
use Zend_Validate_Int;

class StepsData implements ValidatorInterface
{
    const ARRAY_INDEX_PRODUCT_ID = 'product_id';
    const ARRAY_INDEX_SELECTED_OPTIONS = 'selected_options';
    const ARRAY_INDEX_OPTION_ID = 'option_id';
    const ARRAY_INDEX_OPTION_TYPE_ID = 'option_type_id';
    const ARRAY_INDEX_SELECTED_CONFIGURATION = 'selected_configuration';
    const ARRAY_INDEX_HEIGHT = 'height';
    const ARRAY_INDEX_WIDTH = 'width';
    const ARRAY_INDEX_TOP_WIDTH = 'top_width';
    const ARRAY_INDEX_BOTTOM_WIDTH = 'bottom_width';
    const ARRAY_INDEX_USES_CUSTOM_WIDTH = 'uses_custom_width';

    const GLASS_SHAPES_REGULAR_DIMENSION_FIELDS = [
        self::ARRAY_INDEX_HEIGHT,
        self::ARRAY_INDEX_WIDTH
    ];

    const GLASS_SHAPES_CUSTOM_DIMENSION_FIELDS = [
        self::ARRAY_INDEX_HEIGHT,
        self::ARRAY_INDEX_TOP_WIDTH,
        self::ARRAY_INDEX_BOTTOM_WIDTH
    ];

    /**
     * @var Zend_Validate_Int
     */
    protected $intValidator;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @param GeneralConfig $generalConfig
     * @param Zend_Validate_Int $intValidator
     */
    public function __construct(GeneralConfig $generalConfig, Zend_Validate_Int $intValidator)
    {
        $this->generalConfig = $generalConfig;
        $this->intValidator = $intValidator;
    }

    /**
     * @param array $values
     * @return bool
     */
    public function validate($values)
    {
        if (!is_array($values) || count($values) == 0) {
            return false;
        }

        foreach ($values as $stepData) {
            if (!$this->validateStepData($stepData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $stepData
     * @return bool
     */
    protected function validateStepData($stepData)
    {
        if (!is_array($stepData)) {
            return false;
        }

        foreach ($stepData as $stepProduct) {
            if (!$this->validateStepProduct($stepProduct)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $stepProduct
     * @return bool
     */
    protected function validateStepProduct($stepProduct)
    {
        if (!isset($stepProduct[self::ARRAY_INDEX_PRODUCT_ID]) ||
            !$this->intValidator->isValid($stepProduct[self::ARRAY_INDEX_PRODUCT_ID])
        ) {
            return false;
        }

        if (isset($stepProduct[self::ARRAY_INDEX_SELECTED_CONFIGURATION])) {
            return $this->validateGlassShape($stepProduct);
        }

        if (isset($stepProduct[self::ARRAY_INDEX_SELECTED_OPTIONS])) {
            return $this->validateSelectedOptions($stepProduct[self::ARRAY_INDEX_SELECTED_OPTIONS]);
        }

        return true;
    }

    /**
     * @param array $stepProduct
     * @return bool
     */
    protected function validateGlassShape($stepProduct)
    {
        if (!$this->intValidator->isValid($stepProduct[self::ARRAY_INDEX_SELECTED_CONFIGURATION])) {
            return false;
        }

        if (!isset($stepProduct[self::ARRAY_INDEX_USES_CUSTOM_WIDTH]) ||
            !$this->intValidator->isValid($stepProduct[self::ARRAY_INDEX_USES_CUSTOM_WIDTH])
        ) {
            return false;
        }

        if ($usesCustomValues = $stepProduct[self::ARRAY_INDEX_USES_CUSTOM_WIDTH]) {
            $mandatoryFields = self::GLASS_SHAPES_CUSTOM_DIMENSION_FIELDS;
        } else {
            $mandatoryFields = self::GLASS_SHAPES_REGULAR_DIMENSION_FIELDS;
        }

        foreach ($mandatoryFields as $field) {
            if (!isset($stepProduct[$field]) || !$this->intValidator->isValid($stepProduct[$field])) {
                return false;
            }
        }

        $minimumHeight = $this->generalConfig->getGlassShapesMinimumHeight();
        $maximumHeight = $this->generalConfig->getGlassShapesMaximumHeight();
        $minimumWidth = $this->generalConfig->getGlassShapesMinimumWidth();
        $maximumWidth = $this->generalConfig->getGlassShapesMaximumWidth();

        if (!$this->validateDimensionValue($stepProduct[self::ARRAY_INDEX_HEIGHT], $minimumHeight, $maximumHeight)) {
            return false;
        }

        if (!$usesCustomValues) {
            return $this->validateRegularWidthValues($stepProduct, $minimumWidth, $maximumWidth);
        }

        return $this->validateCustomWidthValues($stepProduct, $minimumWidth, $maximumWidth);

    }

    /**
     * @param array $stepProduct
     * @param int $minimum
     * @param int $maximum
     * @return bool
     */
    protected function validateRegularWidthValues($stepProduct, $minimum, $maximum)
    {
        if (!$this->validateDimensionValue($stepProduct[self::ARRAY_INDEX_WIDTH], $minimum, $maximum)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $stepProduct
     * @param int $minimum
     * @param int $maximum
     * @return bool
     */
    protected function validateCustomWidthValues($stepProduct, $minimum, $maximum)
    {
        if (!$this->validateDimensionValue($stepProduct[self::ARRAY_INDEX_TOP_WIDTH], $minimum, $maximum) ||
            !$this->validateDimensionValue($stepProduct[self::ARRAY_INDEX_BOTTOM_WIDTH], $minimum, $maximum)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param int $value
     * @param int $minimum
     * @param int $maximum
     * @return bool
     */
    protected function validateDimensionValue($value, $minimum, $maximum)
    {
        if ($value >= $minimum && $value <= $maximum) {
            return true;
        }

        return false;
    }

    /**
     * @param array $selectedOptions
     * @return bool
     */
    protected function validateSelectedOptions($selectedOptions)
    {
        if (!is_array($selectedOptions) || count($selectedOptions) == 0) {
            return false;
        }

        foreach ($selectedOptions as $selectedOption) {
            if (!$this->validateSelectedOption($selectedOption)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $selectedOption
     * @return bool
     */
    protected function validateSelectedOption($selectedOption)
    {
        if (!isset($selectedOption[self::ARRAY_INDEX_OPTION_ID]) ||
            !isset($selectedOption[self::ARRAY_INDEX_OPTION_TYPE_ID])
        ) {
            return false;
        }

        if (!$this->intValidator->isValid($selectedOption[self::ARRAY_INDEX_OPTION_ID]) ||
            !$this->intValidator->isValid($selectedOption[self::ARRAY_INDEX_OPTION_TYPE_ID])
        ) {
            return false;
        }

        return true;
    }
}