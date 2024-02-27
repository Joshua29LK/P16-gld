<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Rule\Condition;

use Amasty\Shiprestriction\Model\ProductRegistry;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;

class Combine extends \Amasty\CommonRules\Model\Rule\Condition\Combine
{
    public const AMASTY_SHIP_RESTRICTION_PATH_TO_CONDITIONS = 'Amasty\Shiprestriction\Model\Rule\Condition\\';

    /**
     * @var string
     */
    protected $conditionsAddressPath = self::AMASTY_SHIP_RESTRICTION_PATH_TO_CONDITIONS .'Address';

    /**
     * @var ProductRegistry
     */
    private $productRegistry;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Address $conditionAddress,
        \Amasty\CommonRules\Model\Rule\Factory\HandleFactory $handleFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Amasty\CommonRules\Model\Rule\Factory\CombineHandleFactory $combineHandleFactory,
        array $data = [],
        ProductRegistry $productRegistry = null
    ) {
        $this->_conditionAddress = $conditionAddress;
        $this->setType(self::AMASTY_SHIP_RESTRICTION_PATH_TO_CONDITIONS . 'Combine');
        $this->productRegistry = $productRegistry ?? ObjectManager::getInstance()->get(ProductRegistry::class);

        parent::__construct(
            $context,
            $eventManager,
            $conditionAddress,
            $handleFactory,
            $moduleManager,
            $combineHandleFactory,
            $data
        );
    }

    /**
     * @param int|AbstractModel $entity
     * @return bool
     */
    protected function _isValid($entity): bool
    {
        $this->productRegistry->clearRestrictedProducts();
        $result = parent::_isValid($entity);
        if (!$result) {
            return $result;
        }

        if ($this->needToValidateAllConditions()) {
            $this->validateAllConditions($entity);
        }
        $this->saveRestrictedProducts();

        return $result;
    }

    private function saveRestrictedProducts(): void
    {
        $productData = $this->productRegistry->getProductValidationData();
        $true = (bool)$this->getValue();
        $restrictedProducts = [];

        foreach ($productData as $product) {
            if (isset($product['validation_result'], $product['name'])) {
                if ($true && $product['validation_result']) {
                    $restrictedProducts[] = $product['name'];
                }
                if (!$true && !$product['validation_result']) {
                    $restrictedProducts[] = $product['name'];
                }
            }
        }

        $this->productRegistry->setRestrictedProducts($restrictedProducts);
    }

    /**
     * To save restricted products we need to validate all conditions
     * @param int|AbstractModel $entity
     * @return void
     */
    private function validateAllConditions($entity): void
    {
        $conditions = $this->getConditions();
        // First condition is already validated
        array_shift($conditions);

        foreach ($conditions as $cond) {
            if ($entity instanceof AbstractModel) {
                $cond->validate($entity);
            } else {
                $cond->validateByEntityId($entity);
            }
        }
    }

    private function needToValidateAllConditions(): bool
    {
        // If aggregator == 'all', all conditions are already validated
        if ($this->getAggregator() === 'all') {
            return false;
        }

        try {
            $message = (string)$this->getRule()->getCustomRestrictionMessage();
        } catch (\Exception $e) {
            $message = '';
        }

        // Saving Restricted products can decrease performance.
        // We should do it only if we want to show products in restriction message.
        return strpos($message, '{restricted-products}') !== false
            || strpos($message, '{last-restricted-product}') !== false;
    }
}
