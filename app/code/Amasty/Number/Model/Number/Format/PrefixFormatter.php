<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Number\Format;

use Amasty\Number\Model\Number\AbstractFormatter;

class PrefixFormatter extends AbstractFormatter
{
    /**
     * @param string $template
     * @return string
     */
    public function format(string $template): string
    {
        $entityType = $this->getSequence()->getEntityType();

        if ($this->configProvider->isFormatSameAsOrder($entityType)) {
            $prefixToReplace = $this->configProvider->getNumberReplacePrefix($entityType);
            $template = str_replace(
                $prefixToReplace,
                $this->configProvider->getNumberPrefix($entityType),
                $template
            );

            if (!$prefixToReplace) {
                $template = $this->configProvider->getNumberPrefix($entityType) . $template;
            }
        }

        return $template;
    }
}
