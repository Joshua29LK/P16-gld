<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Custom Order Number for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Number\Model\Number\Format;

use Amasty\Number\Model\Number\AbstractFormatter;

class CountryCodeFormatter extends AbstractFormatter
{
    public const PLACEHOLDER = 'country_code';

    /**
     * @param string $template
     * @return string
     */
    public function format(string $template): string
    {
        $replacement = '';

        if ($order = $this->getSequence()->getOrder()) {
            if ($order->getShippingAddress()) {
                $replacement = $order->getShippingAddress()->getCountryId();
            } elseif ($order->getBillingAddress()) {
                $replacement = $order->getBillingAddress()->getCountryId();
            }
        } else { // need to skip this replacement for order
            return $template;
        }

        return $this->replacePlaceholder($template, self::PLACEHOLDER, $replacement);
    }
}
