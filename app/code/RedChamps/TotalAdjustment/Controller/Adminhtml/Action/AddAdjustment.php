<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Controller\Adminhtml\Action;

use Magento\Framework\Controller\ResultFactory;

class AddAdjustment extends Base
{
    public function execute()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $adjustmentAmounts = $this->getRequest()->getParam('adjustment_amount');
            $adjustmentTitles = $this->getRequest()->getParam('adjustment_title');
            $adjustmentTypes = $this->getRequest()->getParam('adjustment_type');
            if ($orderId && $adjustmentTitles && $adjustmentAmounts) {
                $adjustments = [];
                for ($i=0;$i<count($adjustmentTitles);$i++) {
                    $adjustments[] = [
                        'title' => $adjustmentTitles[$i],
                        'type'  => $adjustmentTypes[$i],
                        'amount' => $adjustmentAmounts[$i]
                    ];
                }
                if (!empty($adjustments)) {
                    $response = $this->adjustmentsModifier->addAdjustments($orderId, $adjustments, true);
                    $this->messageManager->addSuccessMessage($response);
                }
            } else {
                $this->messageManager->addErrorMessage(
                    __("Adjustment title and amount are required fields. Please try again.")
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
