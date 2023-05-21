<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\Sales\Order\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class Adjustment extends AbstractTotal
{
    /**
     * @var AdjustmentManager
     */
    private $adjustmentManager;

    public function __construct(
        AdjustmentManager $adjustmentManager,
        array $data = []
    ) {
        parent::__construct($data);
        $this->adjustmentManager = $adjustmentManager;
    }


    public function collect(Creditmemo $creditmemo)
	{
	    $order = $creditmemo->getOrder();
        $adjustmentsInvoiced = $order->getAdjustmentsInvoiced();
        $adjustmentsRefunded = $order->getAdjustmentsRefunded();
        if ($adjustmentsInvoiced) {
            $invoicedAdjustmentsUnserialized = $this->adjustmentManager->decodeAdjustments($adjustmentsInvoiced);
            $refundedAdjustmentsUnserialized = $this->adjustmentManager->decodeAdjustments($adjustmentsRefunded);
            foreach ($invoicedAdjustmentsUnserialized as $adjustmentNumber => $adjustment) {
                if(!isset($refundedAdjustmentsUnserialized[$adjustmentNumber])) {
                    $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $adjustment['amount']);
                    $creditmemo->setBaseGrandTotal(
                        $creditmemo->getBaseGrandTotal() + (isset($adjustment['base_amount'])?$adjustment['base_amount']:$adjustment['amount'])
                    );
                } else {
                    unset($invoicedAdjustmentsUnserialized[$adjustmentNumber]);
                }
            }
            $creditmemo->setAdjustments(
                $this->adjustmentManager->encodeAdjustments($invoicedAdjustmentsUnserialized)
            );
        }
        return $this;
	}
}
