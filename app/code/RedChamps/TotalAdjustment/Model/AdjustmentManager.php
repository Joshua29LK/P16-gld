<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model;

use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class AdjustmentManager
{
    /**
     * @var Json
     */
    private $serializer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function encodeAdjustments($adjustments)
    {
        return $this->serializer->serialize($adjustments);
    }

    public function decodeAdjustments($adjustments)
    {
        try {
            return $this->serializer->unserialize($adjustments);
        } catch (\Exception $exception) {
            $this->logger->critical(
                "Error while decoding adjustments in extension RedChamps_TotalAdjustment: ".$exception->getMessage()
            );
        }
        return false;
    }

    public function getPendingAdjustments($order)
    {
        $orderAdjustments = $order->getAdjustments();
        if ($orderAdjustments) {
            $orderAdjustments = $this->decodeAdjustments($orderAdjustments);
            $invoicedAdjustments = $order->getAdjustmentsInvoiced();
            if ($invoicedAdjustments) {
                $invoicedAdjustments = $this->decodeAdjustments($invoicedAdjustments);
                foreach ($orderAdjustments as $adjustmentNumber => $adjustment) {
                    foreach ($invoicedAdjustments as $invoicedAdjustment) {
                        if ($adjustment["title"] == $invoicedAdjustment["title"]) {
                            if ($adjustment["amount"] == $invoicedAdjustment["amount"]) {
                                unset($orderAdjustments[$adjustmentNumber]);
                            } else {
                                $orderAdjustments[$adjustmentNumber]["amount"] = $adjustment["amount"] - $invoicedAdjustment["amount"];
                            }
                        }
                    }
                }
            }
        }
        return $orderAdjustments;
    }

    public function mergeAdjustments($source, $target)
    {
        if ($target) {
            $target = $this->decodeAdjustments($target);
            $source = $this->decodeAdjustments($source);
            foreach ($source as $adjustmentNumber => $adjustment) {
                foreach ($target as $targetAdjustmentNumber => $targetAdjustment) {
                    if ($targetAdjustment["title"] == $adjustment["title"]) {
                        $target[$targetAdjustmentNumber]["amount"] = $targetAdjustment["amount"] + $adjustment["amount"];
                    } else {
                        $target[] = $adjustment;
                    }
                }
            }
            $target = $this->encodeAdjustments($target);
        } else {
            $target = $source;
        }
        return $target;
    }
}
