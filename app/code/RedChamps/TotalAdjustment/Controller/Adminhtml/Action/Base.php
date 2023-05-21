<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use RedChamps\TotalAdjustment\Api\Order\AdjustmentsInterface;

abstract class Base extends Action
{
    protected $adjustmentsModifier;

    /**
     * @var Json
     */
    protected $serializer;

    public function __construct(
        AdjustmentsInterface $adjustments,
        Json $serializer,
        Context $context
    ) {
        $this->adjustmentsModifier = $adjustments;
        parent::__construct($context);
        $this->serializer = $serializer;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'RedChamps_TotalAdjustment::allowed'
        );
    }
}
