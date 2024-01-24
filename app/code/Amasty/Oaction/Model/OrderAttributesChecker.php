<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model;

use Magento\Framework\Exception\LocalizedException;

class OrderAttributesChecker
{
    public const AMASTY_ORDER_ATTRIBUTES_MODULE_NAME = 'Amasty_Orderattr';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param bool $throwException
     * @return bool
     * @throws LocalizedException
     */
    public function isModuleExist(bool $throwException = true): bool
    {
        if (!$this->moduleManager->isEnabled(self::AMASTY_ORDER_ATTRIBUTES_MODULE_NAME)) {
            if ($throwException) {
                throw new LocalizedException(__('%1 module is not exist.', self::AMASTY_ORDER_ATTRIBUTES_MODULE_NAME));
            }

            return false;
        }

        return true;
    }
}
