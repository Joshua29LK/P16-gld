<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\OrdersExportTool\Logger;

class HandlerCron extends \Magento\Framework\Logger\Handler\Base
{
    public $fileName = '/var/log/OrdersExportTool-cron.log';
    public $loggerType = \Monolog\Logger::NOTICE;
}