<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Counter\ResourceModel;

use Amasty\Number\Api\Data\CounterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Amasty\Number\Model\ConfigProvider;

class Counter extends AbstractDb
{
    public const SEPARATE_CONNECTION_NAME = 'amasty_number_connection';

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        $connectionName = null
    ) {
        if ($configProvider->isUseSeparateConnection()) {
            $connectionName = self::SEPARATE_CONNECTION_NAME;
        }

        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('amasty_number_counter_data', CounterInterface::COUNTER_ID);
    }
}
