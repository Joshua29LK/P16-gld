<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Counter\ResourceModel\Counter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\Number\Api\Data\CounterInterface;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\Number\Model\Counter\Counter::class,
            \Amasty\Number\Model\Counter\ResourceModel\Counter::class
        );
        $this->_setIdFieldName(CounterInterface::COUNTER_ID);
    }
}
