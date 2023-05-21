<?php
/**
 * Created by RedChamps.
 * User: Rav
 * Date: 06/04/18
 * Time: 4:11 PM
 */
namespace RedChamps\AdjustmentApiChanges\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderExtension;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class SalesOrderLoadAfter implements ObserverInterface
{
    protected $orderExtension;

    protected $adjustmentManager;

    public function __construct(
        OrderExtension $orderExtension,
        AdjustmentManager $adjustmentManager
    ) {
        $this->orderExtension = $orderExtension;
        $this->adjustmentManager = $adjustmentManager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $attr = $order->getData('adjustments');
        $attrInvoiced = $order->getData('adjustments_invoiced');
        $orderItems = $order->getAllVisibleItems();
        $orderItemsCount = count($orderItems);
        //process regular discount
        if ($attr) {
            $adjustments = $this->adjustmentManager->decodeAdjustments($attr);
            $firstAdjustment = reset($adjustments);
            if (isset($firstAdjustment['amount'])) {
                $baseAmount = isset($firstAdjustment['base_amount']) ? $firstAdjustment['base_amount'] : $firstAdjustment['amount'];
                $order->setBaseDiscountAmount($order->getBaseDiscountAmount() + $baseAmount);
                $order->setDiscountAmount($order->getDiscountAmount() + $firstAdjustment['amount']);
                $perItemAmount = -($firstAdjustment['amount']/$orderItemsCount);
                $perItemBaseAmount = -($baseAmount/$orderItemsCount);
                foreach ($orderItems as $orderItem) {
                    $orderItem->setDiscountAmount($orderItem->getDiscountAmount()+$perItemAmount);
                    $orderItem->setBaseDiscountAmount($orderItem->getBaseDiscountAmount()+$perItemBaseAmount);
                }
            }
        }
        //process invoiced discount
        if ($attrInvoiced) {
            $adjustments = $this->adjustmentManager->decodeAdjustments($attrInvoiced);
            $firstAdjustment = reset($adjustments);
            if (isset($firstAdjustment['amount'])) {
                $baseAmount = isset($firstAdjustment['base_amount']) ? $firstAdjustment['base_amount'] : $firstAdjustment['amount'];
                $order->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced() + $baseAmount);
                $order->setDiscountInvoiced($order->getDiscountInvoiced() + $firstAdjustment['amount']);
                $perItemAmount = -($firstAdjustment['amount']/$orderItemsCount);
                $perItemBaseAmount = -($baseAmount/$orderItemsCount);
                foreach ($orderItems as $orderItem) {
                    $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()+$perItemAmount);
                    $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()+$perItemBaseAmount);
                }
            }
        }
    }
}
