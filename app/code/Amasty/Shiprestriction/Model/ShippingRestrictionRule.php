<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model;

use Amasty\Shiprestriction\Api\Data\RuleInterface;
use Amasty\Shiprestriction\Model\Validation\NextRuleValidatorInterface;

class ShippingRestrictionRule
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Rule[]
     */
    private $allRules;

    /**
     * @var \Amasty\Shiprestriction\Model\ResourceModel\Rule\Collection
     */
    private $rulesCollection;

    /**
     * @var \Amasty\Shiprestriction\Model\ProductRegistry
     */
    private $productRegistry;

    /**
     * @var Message\MessageBuilder
     */
    private $messageBuilder;

    /**
     * @var \Amasty\CommonRules\Model\Validator\SalesRule
     */
    private $salesRuleValidator;

    /**
     * @var array
     */
    private $nextRulesValidators;

    /**
     * @var bool|null
     */
    private $skipNextRules = null;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Amasty\Shiprestriction\Model\ResourceModel\Rule\Collection $rulesCollection,
        \Amasty\Shiprestriction\Model\ProductRegistry $productRegistry,
        \Amasty\Shiprestriction\Model\Message\MessageBuilder $messageBuilder,
        \Amasty\CommonRules\Model\Validator\SalesRule $salesRuleValidator,
        array $nextRulesValidators = []
    ) {
        $this->appState = $appState;
        $this->rulesCollection = $rulesCollection;
        $this->productRegistry = $productRegistry;
        $this->messageBuilder = $messageBuilder;
        $this->salesRuleValidator = $salesRuleValidator;
        $this->nextRulesValidators = $this->initializeValidators($nextRulesValidators);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRestrictionRules($request)
    {
        /** @var \Magento\Quote\Model\Quote\Item[] $allItems */
        $allItems = $request->getAllItems();

        if (!$allItems) {
            return [];
        }

        $firstItem = current($allItems);
        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $firstItem->getAddress();
        $address->setData('city', trim((string)$address->getData('city')));
        $address->setData('postcode', trim((string)$address->getData('postcode')));
        $address->setItemsToValidateRestrictions($allItems);

        //multishipping optimization
        if (!$this->allRules) {
            $this->allRules = $this->prepareAllRules($address);
        }

        /**
         * Fix for admin checkout
         *
         * UPD: Return missing address data (discount, grandtotal, etc)
         */
        if ($this->isAdmin() && $address->hasOrigData()) {
            $address->addData($address->getOrigData());
        }

        // remember old
        $subtotal = $address->getSubtotal();
        $baseSubtotal = $address->getBaseSubtotal();
        $validRules = $this->getValidRules($address, $allItems);
        // restore
        $address->setSubtotal($subtotal);
        $address->setBaseSubtotal($baseSubtotal);

        return $validRules;
    }

    /**
     * @param $address
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareAllRules($address)
    {
        $allRules = $this->rulesCollection->addAddressFilter($address);

        if ($this->isAdmin()) {
            $allRules->addFieldToFilter('for_admin', 1);
        }

        $allRules = $this->rulesCollection->getItems();

        /** @var \Amasty\Shiprestriction\Model\Rule $rule */
        foreach ($allRules as $rule) {
            $rule->afterLoad();
        }

        return $allRules;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Model\Quote\Item[] $allItems
     *
     * @return \Amasty\Shiprestriction\Model\Rule[]
     */
    protected function getValidRules($address, $allItems)
    {
        $validRules = [];
        /** @var \Amasty\Shiprestriction\Model\Rule $rule */
        foreach ($this->allRules as $rule) {
            $this->productRegistry->clearProducts();
            $this->productRegistry->clearProductValidationData();

            if ($rule->validate($address, $allItems)
                && $this->salesRuleValidator->validate($rule, $allItems)
            ) {
                foreach ($allItems as $item) {
                    $this->productRegistry->addProduct($item->getName());
                }
                // remember used products
                $newMessage = $this->messageBuilder->parseMessage(
                    (string)$rule->getCustomRestrictionMessage(),
                    $this->productRegistry->getProducts()
                );

                $rule->setCustomRestrictionMessage($newMessage);
                $validRules[] = $rule;

                if (!$this->isValidNextRules($rule)) {
                    break;
                }
            }
        }

        return $validRules;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isAdmin()
    {
        return $this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }

    /**
     * @return NextRuleValidatorInterface[]
     */
    private function initializeValidators(array $nextRulesValidators): array
    {
        $validators = [];
        foreach ($nextRulesValidators as $validator) {
            if (!$validator instanceof NextRuleValidatorInterface) {
                throw new \InvalidArgumentException(
                    'Type "' . get_class($validator) . '" is not instance of '
                    . NextRuleValidatorInterface::class
                );
            }
            $validators[] = $validator;
        }

        return $validators;
    }

    protected function isValidNextRules(RuleInterface $rule): bool
    {
        $isValid = true;
        foreach ($this->nextRulesValidators as $validator) {
            $isValid = $validator->isValid($rule);
            if ($isValid === false) {
                break;
            }
        }

        return $isValid;
    }
}
