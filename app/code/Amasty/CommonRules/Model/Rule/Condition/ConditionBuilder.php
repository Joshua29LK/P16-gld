<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model\Rule\Condition;

class ConditionBuilder
{
    public const AMASTY_COMMON_RULES_PATH_TO_CONDITIONS = 'Amasty\CommonRules\Model\Rule\Condition\\';
    public const AMASTY_SHIP_RESTRICTION_PATH_TO_CONDITIONS = 'Amasty\Shiprestriction\Model\Rule\Condition\\';
    public const AMASTY_SHIP_RULES_PATH_TO_CONDITIONS = 'Amasty\Shiprules\Model\Rule\Condition\\';
    public const MAGENTO_SALES_RULE_PATH_TO_CONDITIONS = 'Magento\SalesRule\Model\Rule\Condition\\';

    /**
     * @param $conditions
     * @return array
     */
    public function getChangedNewChildSelectOptions($conditions)
    {
        foreach ($conditions as $key => $value) {
            if (isset($value['value'])
                && $value['value'] == self::MAGENTO_SALES_RULE_PATH_TO_CONDITIONS . 'Product\Combine'
            ) {
                $conditions[$key]['value'] = self::AMASTY_COMMON_RULES_PATH_TO_CONDITIONS . 'Product\Combine';
            }
        }

        return $conditions;
    }
}
