<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\Sales\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;
use RedChamps\TotalAdjustment\Api\Order\AdjustmentsInterface;
use RedChamps\TotalAdjustment\Model\AdjustmentModifier\Actions;

class AdjustmentsModifier implements AdjustmentsInterface
{
    protected $addActionProcessor;

    protected $removeActionProcessor;

    protected $editActionProcessor;

    protected $orderModel;

    protected $_order;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * AdjustmentsModifier constructor.
     * @param Actions\Add $addAction
     * @param Actions\Remove $removeAction
     * @param Actions\Edit $editAction
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Json $serializer,
        Actions\Add $addAction,
        Actions\Remove $removeAction,
        Actions\Edit $editAction,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->addActionProcessor = $addAction;
        $this->removeActionProcessor = $removeAction;
        $this->editActionProcessor = $editAction;
        $this->orderModel = $orderRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param int $orderId
     * @param mixed $adjustments
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addAdjustments($orderId, $adjustments, $nonApi = false)
    {
        $order = $this->getOrder($orderId);
        $this->checkIfAllowed($order);
        $result =  $this->addActionProcessor->execute($order, $adjustments);
        return $this->postProcess($order, $result, $nonApi);
    }

    /**
     * @param int $orderId
     * @param mixed $adjustmentTitles
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeAdjustments($orderId, $adjustmentTitles, $nonApi = false)
    {
        $order = $this->getOrder($orderId);
        $this->checkIfAllowed($order);
        $result = $this->removeActionProcessor->execute($order, $adjustmentTitles);
        return $this->postProcess($order, $result, $nonApi);
    }

    /**
     * @param int $orderId
     * @param mixed $adjustments
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function editAdjustments($orderId, $adjustments, $nonApi = false)
    {
        $order = $this->getOrder($orderId);
        $this->checkIfAllowed($order);
        $result = $this->editActionProcessor->execute($order, $adjustments);
        return $this->postProcess($order, $result, $nonApi);
    }

    protected function getOrder($orderId)
    {
        if (!$this->_order) {
            $order = $this->orderModel->get($orderId);
            if (!$order || !$order->getId()) {
                throw new LocalizedException(__("No order exist with specified ID."));
            }
            $this->_order = $order;
        }
        return $this->_order;
    }

    protected function checkIfAllowed($order)
    {
        if (!$order->canInvoice()) {
            throw new LocalizedException(
                __("Order is completely invoiced. So the order totals can't be changed.")
            );
        }
    }

    protected function postProcess($order, $result, $nonApi)
    {
        $this->orderModel->save($order);
        if ($nonApi) {
            return $result;
        }
        return $this->formatResponse($result, $order);
    }

    protected function formatResponse($response, $order)
    {
        $result = [
            'message' => $response,
            "new_grand_total" => $order->getGrandTotal(),
            "new_base_grand_total" => $order->getBaseGrandTotal()
        ];
        return $this->serializer->serialize($result);
    }
}
