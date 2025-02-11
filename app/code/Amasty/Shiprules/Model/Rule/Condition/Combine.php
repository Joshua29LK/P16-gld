<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Condition;

/**
 * Conditions Combine.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Combine extends \Amasty\CommonRules\Model\Rule\Condition\Combine
{
    public const AMASTY_SHIP_RULES_PATH_TO_CONDITIONS = 'Amasty\Shiprules\Model\Rule\Condition\\';

    /**
     * @var string
     */
    protected $conditionsAddressPath = self::AMASTY_SHIP_RULES_PATH_TO_CONDITIONS .'Address';

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Address $conditionAddress,
        \Amasty\CommonRules\Model\Rule\Factory\HandleFactory $handleFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Amasty\CommonRules\Model\Rule\Factory\CombineHandleFactory $combineHandleFactory,
        array $data = []
    ) {
        $this->_conditionAddress = $conditionAddress;
        $this->setType(self::AMASTY_SHIP_RULES_PATH_TO_CONDITIONS . 'Combine');

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
}
