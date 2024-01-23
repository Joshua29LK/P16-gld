<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Model\Source;

class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var string
     */
    protected static $_options;

    public const CRON_HOURLY = 'H';
    public const CRON_TO_TIME_PER_DAY = '2TD';
    public const CRON_DAILY = 'D';
    public const CRON_WEEKLY = 'W';
    public const CRON_MONTHLY = 'M';
    public const CRON_CUSTOM = 'CUS';
    public const CRON_NEVER = 'NEVER';

    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    protected $helper;

    /**
     * Frequency constructor.
     * @param \Amasty\Orderarchive\Helper\Data $helper
     */
    public function __construct(
        \Amasty\Orderarchive\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @return array|string
     */
    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = [
                [
                    'label' => __('Hourly'),
                    'value' => self::CRON_HOURLY,
                ],
                [
                    'label' => __('Two Times Per Day'),
                    'value' => self::CRON_TO_TIME_PER_DAY,
                ],
                [
                    'label' => __('Daily'),
                    'value' => self::CRON_DAILY,
                ],
                [
                    'label' => __('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ],
                [
                    'label' => __('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ],
                [
                    'label' => __('Never'),
                    'value' => self::CRON_NEVER,
                ]
            ];
        }

        return self::$_options;
    }
}
