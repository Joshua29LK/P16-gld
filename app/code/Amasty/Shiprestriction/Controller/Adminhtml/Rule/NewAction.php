<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\Controller\ResultInterface;

/**
 * Action of Rule creating.
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Shiprestriction::rule';

    /**
     * @var Forward
     */
    private $resultForward;

    public function __construct(Context $context, Forward $resultForward)
    {
        parent::__construct($context);
        $this->resultForward = $resultForward;
    }

    public function execute(): ResultInterface
    {
        return $this->resultForward->forward('edit');
    }
}
