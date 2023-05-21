<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\OrdersExportTool\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    public $fileName = '/var/log/OrdersExportTool.log';
    public $loggerType = \Monolog\Logger::NOTICE;
}