<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\ResourceModel\Rule;

class Collection extends \Amasty\CommonRules\Model\ResourceModel\Rule\Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'amasty_shiprestriction_rule_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'rule_collection';

    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'rule_id' => 'main_table.rule_id'
        ]
    ];

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\Shiprestriction\Model\Rule::class,
            \Amasty\Shiprestriction\Model\ResourceModel\Rule::class
        );
    }
}
