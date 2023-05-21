<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Custom Order Number for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Number\Model\Counter;

use Amasty\Number\Api\Data\CounterInterface;
use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\Number\Format\DateFormatter;

class CounterResetDateProvider
{
    /**
     * @var DateFormatter
     */
    private $dateFormatter;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        DateFormatter $dateFormatter,
        ConfigProvider $configProvider
    ) {
        $this->dateFormatter = $dateFormatter;
        $this->configProvider = $configProvider;
    }

    public function getCounterResetDateInfo(CounterInterface $counter): array
    {
        $counterResetDateFormat = $this->configProvider->getCounterResetOnDateChange($counter->getEntityTypeId());
        $counterResetDate = $this->dateFormatter->formatDate($counterResetDateFormat);

        return [$counterResetDateFormat, $counterResetDate];
    }
}
