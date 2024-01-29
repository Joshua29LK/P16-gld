<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Plugin\SalesRule\Model\Rule\Condition\Product;

use Amasty\Shiprestriction\Model\ProductRegistry;
use Amasty\Shiprestriction\Model\Rule;
use Magento\Framework\Model\AbstractModel;
use Magento\SalesRule\Model\Rule\Condition\Product;

class SaveValidationData
{
    /**
     * @var ProductRegistry
     */
    private $productRegistry;

    public function __construct(
        ProductRegistry $productRegistry
    ) {
        $this->productRegistry = $productRegistry;
    }

    public function afterValidate(Product $subject, bool $result, AbstractModel $model): bool
    {
        $rule = $subject->getRule();
        if (!$rule instanceof Rule) {
            return $result;
        }

        if ($model->getSku()) {
            $this->productRegistry->addProductValidationData(
                [$model->getSku() => ['name' => $model->getName(), 'validation_result' => $result]]
            );
        }

        return $result;
    }
}
