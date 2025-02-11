<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Controller\Adminhtml\Rule;

/**
 * Edit action.
 */
class Edit extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractEdit
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Shiprules::rule';

    /**
     * @var string
     */
    protected $registryKey = \Amasty\Shiprules\Model\ConstantsInterface::REGISTRY_KEY;

    protected function getDefaultTitle()
    {
        return __('Add new Shipping Rule');
    }

    protected function getErrorMessage($ruleId)
    {
        return __('Unable to load Shipping Rule with ID %1. Please review the log and try again.', $ruleId);
    }
}
