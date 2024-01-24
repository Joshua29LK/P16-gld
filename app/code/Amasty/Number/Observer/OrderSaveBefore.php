<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Observer;

use Amasty\Number\Model\Number\AbstractFormatter;
use Amasty\Number\Model\SequenceStorage;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class OrderSaveBefore implements ObserverInterface
{
    /**
     * @var SequenceStorage
     */
    private $sequenceStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AbstractFormatter[]|array
     */
    private $formatterPool;

    public function __construct(
        SequenceStorage $sequenceStorage,
        LoggerInterface $logger,
        $formatterPool = []
    ) {
        $this->sequenceStorage = $sequenceStorage;
        $this->logger = $logger;
        $this->formatterPool = $formatterPool;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getOrder();

            if (strpos($order->getIncrementId(), '{') !== false) {
                $newIncrementId = $order->getIncrementId();
                $this->sequenceStorage->setOrder($order);

                foreach ($this->formatterPool as $formatter) {
                    if ($formatter instanceof AbstractFormatter) {
                        $newIncrementId = $formatter->format($newIncrementId);
                    }
                }

                $order->setIncrementId($newIncrementId);
            }
        } catch (\Throwable $e) {
            $this->logger->critical($e);
        }
    }
}
