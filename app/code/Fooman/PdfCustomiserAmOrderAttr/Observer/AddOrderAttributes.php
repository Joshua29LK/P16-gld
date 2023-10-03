<?php

namespace Fooman\PdfCustomiserAmOrderAttr\Observer;

use Fooman\PdfCustomiserAmOrderAttr\Model\AmOrderAttributes;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddOrderAttributes implements ObserverInterface
{
    /**
     * @var AmOrderAttributes
     */
    private $amOrderAttributes;

    public function __construct(
        AmOrderAttributes $amOrderAttributes
    ) {
        $this->amOrderAttributes = $amOrderAttributes;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $comments = $this->amOrderAttributes->getOrderAttributesData($order);
        if (empty($comments)) {
            return;
        }
        $transport = $observer->getEvent()->getTransport();
        $transport->setCustomComments(array_merge_recursive($transport->getCustomComments(), $comments));
    }
}
