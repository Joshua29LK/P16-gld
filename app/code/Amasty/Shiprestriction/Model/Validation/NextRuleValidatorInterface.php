<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Validation;

use Amasty\Shiprestriction\Api\Data\RuleInterface;

interface NextRuleValidatorInterface
{
    public function isValid(RuleInterface $rule): bool;
}
