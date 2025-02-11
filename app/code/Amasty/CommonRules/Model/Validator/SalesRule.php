<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model\Validator;

class SalesRule implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate($rule, $items)
    {
        $providedCouponCodes = $this->getCouponCodes($items);

        $providedRuleIds = $this->getRuleIds($items);

        return $this->isApply($rule, $providedCouponCodes, $providedRuleIds) ?
            !$this->isApply($rule, $providedCouponCodes, $providedRuleIds, false) : false;
    }

    /**
     * @param \Magento\Rule\Model\AbstractModel $rule
     * @param array $providedCouponCodes
     * @param array $providedRuleIds
     * @param bool $isDisable
     *
     * @return bool
     */
    private function isApply($rule, $providedCouponCodes, $providedRuleIds, $isDisable = true)
    {
        if ($isDisable) {
            $coupons = $rule->getCouponDisable();
            $discountIds = $rule->getDiscountIdDisable();
        } else {
            $coupons = $rule->getCoupon();
            $discountIds = $rule->getDiscountId();
        }

        if (!$coupons && !$discountIds) {
            return $isDisable;
        }

        $activeCoupons = $coupons ? array_intersect(explode(',', strtolower($coupons)), $providedCouponCodes) : [];
        $activeRules = $discountIds ? array_intersect(explode(',', $discountIds), $providedRuleIds) : [];

        return !($activeCoupons || $activeRules);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return array
     */
    private function getRuleIds($items)
    {
        if (empty($items)) {
            return [];
        }

        /** @var \Magento\Quote\Model\Quote\Item $firstItem */
        $firstItem = current($items);
        $rules = trim((string)$firstItem->getQuote()->getAppliedRuleIds());

        if (!$rules) {
            return [];
        }

        return explode(',', $rules);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return array
     */
    private function getCouponCodes($items)
    {
        if (!count($items)) {
            return [];
        }

        /** @var \Magento\Quote\Model\Quote\Item $firstItem */
        $firstItem = current($items);
        $codes = trim(strtolower((string)$firstItem->getQuote()->getCouponCode()));

        if (!$codes) {
            return [];
        }

        return array_map('trim', explode(",", $codes));
    }
}
