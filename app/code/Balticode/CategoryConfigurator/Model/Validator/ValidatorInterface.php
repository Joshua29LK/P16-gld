<?php

namespace Balticode\CategoryConfigurator\Model\Validator;

interface ValidatorInterface
{
    /**
     * @param array $values
     * @return bool
     */
    public function validate($values);
}