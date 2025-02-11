<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Model\Value\Metadata\Form;

/**
 * EAV Entity Attribute Boolean Data Model
 */
class Boolean extends \Magento\Eav\Model\Attribute\Data\Boolean
{
    public function validateValue($value)
    {
        $errors = [];
        $attribute = $this->getAttribute();

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->getEntity()->getData($attribute->getAttributeCode());
        }

        if ($attribute->getIsRequired() && ($value == '-1' || $value == '')) {
            $label = __($attribute->getStoreLabel());
            $errors[] = __('"%1" is a required value.', $label);
        }

        if (!$errors && !$attribute->getIsRequired() && empty($value)) {
            return true;
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }
}
