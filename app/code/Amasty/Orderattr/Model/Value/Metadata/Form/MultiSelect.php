<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Model\Value\Metadata\Form;

class MultiSelect extends \Magento\Eav\Model\Attribute\Data\Multiselect
{
    /**
     * @inheritdoc
     */
    public function compactValue($value)
    {
        if ($value === false) {
            $value = '';
        }

        return parent::compactValue($value);
    }
}
