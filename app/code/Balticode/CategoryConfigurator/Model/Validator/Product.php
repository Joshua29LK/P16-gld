<?php

namespace Balticode\CategoryConfigurator\Model\Validator;

use Zend_Validate_Int;

class Product implements ValidatorInterface
{
    const ARRAY_INDEX_PRODUCT = 'product';

    /**
     * @var Zend_Validate_Int
     */
    protected $productIdValidator;

    /**
     * @param Zend_Validate_Int $productIdValidator
     */
    public function __construct(Zend_Validate_Int $productIdValidator)
    {
        $this->productIdValidator = $productIdValidator;
    }

    /**
     * @param array $values
     * @return bool
     */
    public function validate($values)
    {
        if (!isset($values[self::ARRAY_INDEX_PRODUCT])) {
            return false;
        }

        return $this->productIdValidator->isValid($values[self::ARRAY_INDEX_PRODUCT]);
    }
}