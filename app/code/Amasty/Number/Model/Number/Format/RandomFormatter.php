<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Number\Format;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Amasty\Number\Model\Number\AbstractFormatter;

class RandomFormatter extends AbstractFormatter
{
    public const PLACEHOLDER = 'rand';

    /**
     * @param string $template
     * @return string
     * @throws LocalizedException
     */
    public function format(string $template): string
    {
        return $this->replacePlaceholder(
            $template,
            self::PLACEHOLDER,
            sprintf('%04d', Random::getRandomNumber(0, 9999))
        );
    }
}
