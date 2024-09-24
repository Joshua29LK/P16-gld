<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model;

use Amasty\CommonRules\Model\Rule as CommonRule;
use Amasty\Shiprestriction\Api\Data\RuleInterface;
use Amasty\Shiprestriction\Api\Data\RuleExtensionInterface;

class Rule extends CommonRule implements RuleInterface
{
    public const RULE_ID = 'rule_id';
    public const IS_ACTIVE = 'is_active';
    public const FOR_ADMIN = 'for_admin';
    public const OUT_OF_STOCK = 'out_of_stock';
    public const ALL_STORES = 'all_stores';
    public const ALL_GROUPS = 'all_groups';
    public const NAME = 'name';
    public const COUPON = 'coupon';
    public const DISCOUNT_ID = 'discount_id';
    public const DAYS = 'days';
    public const TIME_FROM = 'time_from';
    public const TIME_TO = 'time_to';
    public const STORES = 'stores';
    public const CUST_GROUPS = 'cust_groups';
    public const CARRIERS = 'carriers';
    public const METHODS = 'methods';
    public const CONDITIONS_SERIALIZED = 'conditions_serialized';
    public const COUPON_DISABLE = 'coupon_disable';
    public const DISCOUNT_ID_DISABLE = 'discount_id_disable';
    public const SHOW_RESTRICTION_MESSAGE = 'show_restriction_message';
    public const CUSTOM_RESTRICTION_MESSAGE = 'custom_restriction_message';
    public const SHOW_RESTRICTION_MESSAGE_ONCE = 'show_restriction_message_once';

    /**
     * @var string
     */
    protected $_eventPrefix = 'amasty_shiprestriction_rule';

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
        parent::_construct();
        $this->subtotalModifier->setSectionConfig(ConstantsInterface::SECTION_KEY);
    }

    public function prepareForEdit(): RuleInterface
    {
        foreach (ConstantsInterface::FIELDS as $field) {
            $value = $this->getData($field);

            if (!is_array($value)) {
                $this->setData($field, explode(',', (string)$value));
            }
        }

        $methods = array_merge($this->getCarriers(), $this->getMethods());
        $this->setData(self::METHODS, $methods);

        return $this;
    }

    /**
     * @return array|null
     */
    public function getStores(): ?array
    {
        $stores = $this->_getData(self::STORES);
        if (is_string($stores)) {
            $stores = explode(',', $stores);
        }

        return $stores;
    }

    /**
     * @param array|string $stores
     *
     * @return RuleInterface
     */
    public function setStores($stores): RuleInterface
    {
        if (is_array($stores)) {
            $stores = implode(',', $stores);
        }

        return $this->setData(self::STORES, $stores);
    }

    public function getRuleId(): int
    {
        return (int)$this->getData(self::RULE_ID);
    }

    public function setRuleId(int $ruleId): void
    {
        $this->setData(self::RULE_ID, $ruleId);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): void
    {
        $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getIsForAdmin(): bool
    {
        return (bool)$this->getData(self::FOR_ADMIN);
    }

    public function setIsForAdmin(bool $isForAdmin): void
    {
        $this->setData(self::FOR_ADMIN, $isForAdmin);
    }

    public function getOutOfStock(): int
    {
        return (int)$this->getData(self::OUT_OF_STOCK);
    }

    public function setOutOfStock(int $outOfStockOption): void
    {
        $this->setData(self::OUT_OF_STOCK, $outOfStockOption);
    }

    public function getIsAllStores(): bool
    {
        return (bool)$this->getData(self::ALL_STORES);
    }

    public function setIsAllStores(bool $isAllStores): void
    {
        $this->setData(self::ALL_STORES, $isAllStores);
    }

    public function getIsAllGroups(): bool
    {
        return (bool)$this->getData(self::ALL_GROUPS);
    }

    public function setIsAllGroups(bool $isAllGroups): void
    {
        $this->setData(self::ALL_GROUPS, $isAllGroups);
    }

    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    public function setName(string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    public function getCoupon(): ?string
    {
        return $this->hasData(self::COUPON) ? (string)$this->getData(self::COUPON) : null;
    }

    public function setCoupon(string $coupon): void
    {
        $this->setData(self::COUPON, $coupon);
    }

    public function getDiscountId(): string
    {
        return (string)$this->getData(self::DISCOUNT_ID);
    }

    public function setDiscountId(string $discountId): void
    {
        $this->setData(self::DISCOUNT_ID, $discountId);
    }

    public function getDays(): ?string
    {
        return $this->hasData(self::DAYS) ? (string)$this->getData(self::DAYS) : null;
    }

    public function setDays(string $days): void
    {
        $this->setData(self::DAYS, $days);
    }

    public function getTimeFrom(): int
    {
        return (int)$this->getData(self::TIME_FROM);
    }

    public function setTimeFrom(int $timeFrom): void
    {
        $this->setData(self::TIME_FROM, $timeFrom);
    }

    public function getTimeTo(): int
    {
        return (int)$this->getData(self::TIME_TO);
    }

    public function setTimeTo(int $timeTo): void
    {
        $this->setData(self::TIME_TO, $timeTo);
    }

    public function getCustGroups(): string
    {
        return (string)$this->getData(self::CUST_GROUPS);
    }

    public function setCustGroups(string $custGroups): void
    {
        $this->setData(self::CUST_GROUPS, $custGroups);
    }

    public function getCarriers(): array
    {
        $carriers = $this->hasData(self::CARRIERS) ? (string)$this->getData(self::CARRIERS) : [];
        if (is_string($carriers)) {
            $carriers = explode(',', $carriers);
        }

        return $carriers;
    }

    public function setCarriers(array $carriers): void
    {
        $carriers = implode(',', $carriers);
        $this->setData(self::CARRIERS, $carriers);
    }

    public function getMethods(): array
    {
        $methods = $this->hasData(self::METHODS) && $this->getData(self::METHODS) !== null
            ? $this->getData(self::METHODS)
            : [];
        if (is_string($methods)) {
            $methods = explode(',', $methods);
        }

        return $methods;
    }

    public function setMethods(array $methods): void
    {
        $methods = implode(',', $methods);
        $this->setData(self::METHODS, $methods);
    }

    public function getConditionsSerialized(): ?string
    {
        return $this->hasData(self::CONDITIONS_SERIALIZED) ? (string)$this->getData(self::CONDITIONS_SERIALIZED) : null;
    }

    public function setConditionsSerialized(string $conditionsSerialized): void
    {
        $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    public function getCouponDisable(): ?string
    {
        return $this->hasData(self::COUPON_DISABLE) ? (string)$this->getData(self::COUPON_DISABLE) : null;
    }

    public function setCouponDisable(string $couponDisable): void
    {
        $this->setData(self::COUPON_DISABLE, $couponDisable);
    }

    public function getDiscountIdDisable(): string
    {
        return (string)$this->getData(self::DISCOUNT_ID_DISABLE);
    }

    public function setDiscountIdDisable(string $discountIdDisable): void
    {
        $this->setData(self::DISCOUNT_ID_DISABLE, $discountIdDisable);
    }

    public function getShowRestrictionMessage(): bool
    {
        return (bool)$this->_getData(self::SHOW_RESTRICTION_MESSAGE);
    }

    public function setShowRestrictionMessage(bool $flag): void
    {
        $this->setData(self::SHOW_RESTRICTION_MESSAGE, $flag);
    }

    public function getCustomRestrictionMessage(): ?string
    {
        return $this->hasData(self::CUSTOM_RESTRICTION_MESSAGE)
            ? (string)$this->_getData(self::CUSTOM_RESTRICTION_MESSAGE)
            : null;
    }

    public function setCustomRestrictionMessage(?string $message): void
    {
        $this->setData(self::CUSTOM_RESTRICTION_MESSAGE, $message);
    }

    public function getShowRestrictionMessageOnce(): bool
    {
        return (bool)$this->_getData(self::SHOW_RESTRICTION_MESSAGE_ONCE);
    }

    public function setShowRestrictionMessageOnce(bool $flag): void
    {
        $this->setData(self::SHOW_RESTRICTION_MESSAGE_ONCE, $flag);
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(RuleExtensionInterface $extensionAttributes = null): RuleInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
