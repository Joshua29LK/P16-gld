<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Controller\Adminhtml\Action;

use Magento\Framework\Controller\ResultFactory;

class RemoveAdjustment extends Base
{
    public function execute()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $adjustmentNumber = $this->getRequest()->getParam('adjustment');
            if ($orderId && ($adjustmentNumber || $adjustmentNumber == 0)) {
                $response = $this->adjustmentsModifier->removeAdjustments($orderId, $adjustmentNumber, true);
                $this->messageManager->addSuccessMessage($response);
            } else {
                $this->messageManager->addErrorMessage(
                    __("Couldn't find order or adjustment. Please try again.")
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
