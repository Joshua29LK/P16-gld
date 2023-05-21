<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\Order\Plugin;

use Amasty\OrderStatus\Model\Order\ConfigProcessor;
use Magento\Sales\Model\Order\StatusLabel as SalesStatusLabel;

class StatusLabel
{
    /**
     * @var ConfigProcessor
     */
    private $configProcessor;

    public function __construct(ConfigProcessor $configProcessor)
    {
        $this->configProcessor = $configProcessor;
    }

    /**
     * @param SalesStatusLabel $subject
     * @param string|null $result
     * @param string|null $code
     * @param string $area
     * @param int|null $storeId
     *
     * @return string
     */
    public function afterGetStatusFrontendLabel(
        SalesStatusLabel $subject,
        ?string $result,
        ?string $code,
        string $area,
        int $storeId = null
    ): ?string {
        $result = $this->configProcessor->processStatusLabel($result, $code);

        return $result;
    }
}
