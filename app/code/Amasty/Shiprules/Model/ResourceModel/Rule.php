<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\ResourceModel;

use Amasty\Base\Model\Serializer;
use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\ConstantsInterface;
use Amasty\Shiprules\Model\ResourceModel\Rule\RelationsResolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Rule resource class.
 */
class Rule extends \Amasty\CommonRules\Model\ResourceModel\AbstractRule
{
    public const TABLE_NAME = 'amasty_shiprules_rule';
    public const ATTRIBUTE_TABLE_NAME = 'amasty_shiprules_attribute';
    public const STORES_TABLE_NAME = 'amasty_shiprules_rule_stores';
    public const CUSTOMERS_TABLE_NAME = 'amasty_shiprules_rule_customer_groups';
    public const DAYS_TABLE_NAME = 'amasty_shiprules_rule_days';

    /**
     * @var RelationsResolver
     */
    private $relationsResolver;

    /**
     * @var array
     */
    private $relationData = [];

    public function __construct(
        Context $context,
        Serializer $serializer,
        $connectionName = null,
        RelationsResolver $relationsResolver = null // TODO move to not optional
    ) {
        parent::__construct($context, $serializer, $connectionName);
        $this->relationsResolver = $relationsResolver ?? ObjectManager::getInstance()->get(RelationsResolver::class);
    }

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, RuleInterface::RULE_ID);
    }

    protected function _beforeSave(AbstractModel $object)
    {
        foreach (ConstantsInterface::FIELDS as $field) {
            $value = $object->getData($field);

            if (is_array($value)) {
                if ($field == 'methods') {
                    $carriers = [];

                    foreach ($value as $key => $shipMethod) {
                        if (strpos($shipMethod, '_') === false) {
                            $carriers[] = $shipMethod;

                            unset($value[$key]);
                        }
                    }
                    $object->setCarriers(implode(',', $carriers));
                }

                $object->setData($field, implode(',', $value));
            }
        }

        foreach (ConstantsInterface::RELATION_FIELDS as $field) {
            $this->relationData[$field] = $object->getData($field);
            $object->setData($field, null);
        }

        return parent::_beforeSave($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->relationsResolver->resolve($this->relationData, $object);

        return parent::_afterSave($object);
    }
}
