<?php

namespace Balticode\CategoryConfigurator\Model\ResourceModel;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;

class Step extends AbstractDb
{
    /**
     * Table name
     */
    const TABLE = 'balticode_categoryconfigurator_step';

    const ATTRIBUTE_CODE_SORT_ORDER_IN_STEP = 'sort_order_in_step';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, StepInterface::STEP_ID);
    }
}
