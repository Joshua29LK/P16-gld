<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model;

interface ConstantsInterface
{
    public const REGISTRY_KEY = 'current_amasty_shiprules_rule';
    public const SECTION_KEY = 'amshiprules';
    public const DATA_PERSISTOR_FORM = 'amasty_shiprules_form_data';

    public const FIELDS = [
        'stores',
        'cust_groups',
        'methods',
        'carriers',
        'days',
        'discount_id',
        'discount_id_disable'
    ];
}
