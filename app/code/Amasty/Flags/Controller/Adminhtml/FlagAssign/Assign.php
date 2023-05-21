<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\FlagAssign;

use Amasty\Flags\Controller\Adminhtml\FlagAssign as FlagAssignAction;
use Magento\Framework\Exception\LocalizedException;

class Assign extends FlagAssignAction
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Order\Flag
     */
    private $flagResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Amasty\Flags\Model\ResourceModel\Order\Flag $flagResource
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->flagResource = $flagResource;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $response */
        $response = $this->resultJsonFactory->create();
        $response->setData(['message' => '']);

        try {
            $orderId = (int)$this->getRequest()->getParam('orderId');
            $columnId = (int)$this->getRequest()->getParam('columnId');
            $flag = $this->getRequest()->getParam('flag');

            if (!$orderId || !$columnId) {
                throw new LocalizedException(__('Bad request'));
            }

            if (is_array($flag)) {
                $note = isset($flag['note']) ? $flag['note'] : null;
                $this->flagResource->assign($orderId, $columnId, $flag['id'], $note);
            } else {
                $this->flagResource->unassign($orderId, $columnId);
            }
        } catch (\Exception $e) {
            $response->setData([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }

        return $response;
    }
}
