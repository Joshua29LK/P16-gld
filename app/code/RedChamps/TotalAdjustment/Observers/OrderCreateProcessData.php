<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class OrderCreateProcessData implements ObserverInterface
{
    /**
     * @var AdjustmentManager
     */
    private $adjustmentManager;

    public function __construct(AdjustmentManager $adjustmentManager)
    {
        $this->adjustmentManager = $adjustmentManager;
    }

    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        if (isset($request['adjustments'])) {
            $model = $observer->getEvent()->getOrderCreateModel();
            $quote = $model->getQuote();
            if ($request['adjustments'] == 'remove-all') {
                $adjustmentsData = [];
            } else {
                $adjustmentsData = $this->adjustmentManager->decodeAdjustments($request['adjustments']);
            }
            $adjustments = [];
            foreach ($adjustmentsData as $adjustment) {
                $adjustments[] = [
                    "title" => $adjustment["title"],
                    "type" => $adjustment["type"],
                    "amount" => $adjustment["amount"]
                ];
            }
            //if (count($adjustments)) {
            $adjustments = $this->adjustmentManager->encodeAdjustments($adjustments);
            $quote->getShippingAddress()->setAdjustments($adjustments);
            $quote->setAdjustments($adjustments);
            //}
        }
    }
}
