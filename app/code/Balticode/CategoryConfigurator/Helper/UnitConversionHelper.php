<?php

namespace Balticode\CategoryConfigurator\Helper;

class UnitConversionHelper
{
    const CONVERSION_RATE = 1000;

    /**
     * @param $value
     * @return float|int
     */
    public static function convertMillimetersToMeters($value)
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return floatval($value / self::CONVERSION_RATE);
    }
}