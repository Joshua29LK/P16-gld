<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Custom Order Number for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Number\Model\Number\Format;

use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\Number\AbstractFormatter;
use Amasty\Number\Model\SequenceStorage;
use Magento\Framework\Stdlib\DateTime\DateTime;

class DateFormatter extends AbstractFormatter
{
    public const SECONDS_IN_HOUR = 3600;
    public const YEAR_PLACEHOLDER = 'yyyy';
    public const YEAR_SHORT_PLACEHOLDER = 'yy';
    public const MONTH_PLACEHOLDER = 'mm';
    public const MONTH_SHORT_PLACEHOLDER = 'm';
    public const DAY_PLACEHOLDER = 'dd';
    public const DAY_SHORT_PLACEHOLDER = 'd';
    public const HOUR_PLACEHOLDER = 'hh';

    public const PLACEHOLDERS_ALIASES = [
        self::YEAR_PLACEHOLDER => 'Y',
        self::YEAR_SHORT_PLACEHOLDER => 'y',
        self::MONTH_PLACEHOLDER => 'm',
        self::MONTH_SHORT_PLACEHOLDER => 'n',
        self::DAY_PLACEHOLDER => 'd',
        self::DAY_SHORT_PLACEHOLDER => 'j',
        self::HOUR_PLACEHOLDER => 'H'
    ];

    /**
     * @var int
     */
    private $timestamp = 0;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        ConfigProvider $configProvider,
        SequenceStorage $sequenceStorage,
        DateTime $dateTime
    ) {
        parent::__construct($configProvider, $sequenceStorage);
        $this->dateTime = $dateTime;
    }

    /**
     * @param string $template
     * @return string
     */
    public function format(string $template): string
    {
        foreach (self::PLACEHOLDERS_ALIASES as $placeholder => $alias) {
            $template = $this->replacePlaceholder($template, $placeholder, $this->formatDate($alias));
        }

        return $template;
    }

    /**
     * @param string $format
     * @return string
     */
    public function formatDate(string $format): string
    {
        if (!$this->timestamp) {
            $timestampMixin = $this->configProvider->getTimezoneOffset() * self::SECONDS_IN_HOUR;
            $this->timestamp = $this->dateTime->timestamp() + $timestampMixin;
        }

        return $this->date($format, $this->timestamp);
    }

    /**
     * @param null $format
     * @param null $input
     * @return string
     */
    public function date($format = null, $input = null)
    {
        return $this->dateTime->date($format, $input);
    }
}
