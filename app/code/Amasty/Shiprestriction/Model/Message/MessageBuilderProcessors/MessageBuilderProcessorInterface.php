<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Message\MessageBuilderProcessors;

interface MessageBuilderProcessorInterface
{
    public function process(string $message): string;
}
