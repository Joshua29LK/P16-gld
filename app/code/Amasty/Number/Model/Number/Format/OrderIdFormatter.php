<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Number\Format;

use Amasty\Number\Model\Number\AbstractFormatter;

class OrderIdFormatter extends AbstractFormatter
{
    public const PLACEHOLDER = 'order_id';

    /**
     * @param string $template
     * @return string
     */
    public function format(string $template): string
    {
        $replacement = '';

        if ($this->getSequence()->getOrder()) {
            $replacement = (string)$this->getSequence()->getOrder()->getId();
        }

        return $this->replacePlaceholder($template, self::PLACEHOLDER, $replacement);
    }
}
