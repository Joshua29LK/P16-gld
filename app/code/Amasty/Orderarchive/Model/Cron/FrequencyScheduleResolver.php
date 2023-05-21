<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Orderarchive\Model\Cron;

use Amasty\Orderarchive\Model\Source\Frequency;

class FrequencyScheduleResolver
{
    /**
     * get shedule
     * @param string $frequency
     * @return string
     */
    public function getSchedule(string $frequency): string
    {
        switch ($frequency) {
            case Frequency::CRON_HOURLY:
                $cronExpString = "0 * * * * *";
                break;
            case Frequency::CRON_TO_TIME_PER_DAY:
                $cronExpString = '0 */12 * * *';
                break;
            case Frequency::CRON_DAILY:
                $cronExpString = "0 0 * * * *";
                break;
            case Frequency::CRON_WEEKLY:
                $cronExpString = "0 0 * * 0";
                break;
            case Frequency::CRON_MONTHLY:
                $cronExpString = "0 0 1 * *";
                break;
            case Frequency::CRON_CUSTOM:
                $cronExpString = "0 0 1 * *";
                break;
            default:
                $cronExpString = '*/10 * * * * *';
        }

        return $cronExpString;
    }
}
