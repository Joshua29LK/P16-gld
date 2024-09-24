<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface RuleInterface extends ExtensibleDataInterface
{
    /**
     * @return int
     */
    public function getRuleId(): int;

    /**
     * @param int $ruleId
     * @return void
     */
    public function setRuleId(int $ruleId): void;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void;

    /**
     * @return bool
     */
    public function getIsForAdmin(): bool;

    /**
     * @param bool $isForAdmin
     * @return void
     */
    public function setIsForAdmin(bool $isForAdmin): void;

    /**
     * @return int
     */
    public function getOutOfStock(): int;

    /**
     * @param int $outOfStockOption
     * @return void
     */
    public function setOutOfStock(int $outOfStockOption): void;

    /**
     * @return bool
     */
    public function getIsAllStores(): bool;

    /**
     * @param bool $isAllStores
     * @return void
     */
    public function setIsAllStores(bool $isAllStores): void;

    /**
     * @return bool
     */
    public function getIsAllGroups(): bool;

    /**
     * @param bool $isAllGroups
     * @return void
     */
    public function setIsAllGroups(bool $isAllGroups): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return string|null
     */
    public function getCoupon(): ?string;

    /**
     * @param string $coupon
     * @return void
     */
    public function setCoupon(string $coupon): void;

    /**
     * @return string
     */
    public function getDiscountId(): string;

    /**
     * @param string $discountId
     * @return void
     */
    public function setDiscountId(string $discountId): void;

    /**
     * @return string|null
     */
    public function getDays(): ?string;

    /**
     * @param string $days
     * @return void
     */
    public function setDays(string $days): void;

    /**
     * @return int
     */
    public function getTimeFrom(): int;

    /**
     * @param int $timeFrom
     * @return void
     */
    public function setTimeFrom(int $timeFrom): void;

    /**
     * @return int
     */
    public function getTimeTo(): int;

    /**
     * @param int $timeTo
     * @return void
     */
    public function setTimeTo(int $timeTo): void;

    /**
     * @return array|null
     */
    public function getStores(): ?array;

    /**
     * @param array|string $stores
     * @return RuleInterface
     */
    public function setStores($stores): RuleInterface;

    /**
     * @return string
     */
    public function getCustGroups(): string;

    /**
     * @param string $custGroups
     * @return void
     */
    public function setCustGroups(string $custGroups): void;

    /**
     * @return array
     */
    public function getCarriers(): array;

    /**
     * @param array $carriers
     * @return void
     */
    public function setCarriers(array $carriers): void;

    /**
     * @return array
     */
    public function getMethods(): array;

    /**
     * @param array $methods
     * @return void
     */
    public function setMethods(array $methods): void;

    /**
     * @return string|null
     */
    public function getConditionsSerialized(): ?string;

    /**
     * @param string $conditionsSerialized
     * @return void
     */
    public function setConditionsSerialized(string $conditionsSerialized): void;

    /**
     * @return string|null
     */
    public function getCouponDisable(): ?string;

    /**
     * @param string $couponDisable
     * @return void
     */
    public function setCouponDisable(string $couponDisable): void;

    /**
     * @return string
     */
    public function getDiscountIdDisable(): string;

    /**
     * @param string $discountIdDisable
     * @return void
     */
    public function setDiscountIdDisable(string $discountIdDisable): void;

    /**
     * @return bool
     */
    public function getShowRestrictionMessage(): bool;

    /**
     * @param bool $flag
     * @return void
     */
    public function setShowRestrictionMessage(bool $flag): void;

    /**
     * @return string|null
     */
    public function getCustomRestrictionMessage(): ?string;

    /**
     * @param string|null $message
     * @return void
     */
    public function setCustomRestrictionMessage(?string $message): void;

    /**
     * @return bool
     */
    public function getShowRestrictionMessageOnce(): bool;

    /**
     * @param bool $flag
     * @return void
     */
    public function setShowRestrictionMessageOnce(bool $flag): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Amasty\Shiprestriction\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Amasty\Shiprestriction\Api\Data\RuleExtensionInterface|null $extensionAttributes
     * @return \Amasty\Shiprestriction\Api\Data\RuleInterface
     */
    public function setExtensionAttributes(RuleExtensionInterface $extensionAttributes = null);
}
