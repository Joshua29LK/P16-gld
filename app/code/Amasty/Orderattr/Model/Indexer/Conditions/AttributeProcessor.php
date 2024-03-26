<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Model\Indexer\Conditions;

use Magento\Framework\Indexer\AbstractProcessor;

class AttributeProcessor extends AbstractProcessor
{
    public const INDEXER_ID = 'amasty_order_attribute_product';
}
