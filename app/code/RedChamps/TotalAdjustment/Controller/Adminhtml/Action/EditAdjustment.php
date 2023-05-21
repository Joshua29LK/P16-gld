<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Controller\Adminhtml\Action;

class EditAdjustment extends Base
{
    public function execute()
    {
        $response = [];
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $adjustmentNumber = $this->getRequest()->getParam('adjustment_number');
            $newAdjustmentAmount = (float)$this->getRequest()->getParam('value');
            if ($orderId &&
                (!empty($adjustmentNumber) || $adjustmentNumber == 0) &&
                (!empty($newAdjustmentAmount) || $newAdjustmentAmount == 0)
            ) {
                $adjustment = ['adjustment_number' => $adjustmentNumber, 'new_value' => $newAdjustmentAmount];
                $result = $this->adjustmentsModifier->editAdjustments($orderId, $adjustment, true);
                $response['success'] = 1;
                $response['message'] = $result;
                $response['new'] = $newAdjustmentAmount;
            } else {
                $response['error'] = __("Adjustment title and amount are required fields. Please try again.");
            }
        } catch (\Exception $e) {
            $response['error'] = 1;
            $response['message'] = $e->getMessage();
        }
        $this->getResponse()->setBody($this->serializer->serialize($response));
    }
}
