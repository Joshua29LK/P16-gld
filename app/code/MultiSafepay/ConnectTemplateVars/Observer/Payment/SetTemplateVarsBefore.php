<?php
/**
 * Copyright © MultiSafepay All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MultiSafepay\ConnectTemplateVars\Observer\Payment;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

class SetTemplateVarsBefore implements ObserverInterface
{
    /**
     * @var State
     */
    private $state;

    /**
     * SetTemplateVarsBefore constructor.
     *
     * @param State $state
     */
    public function __construct(
        State $state
    ) {
        $this->state = $state;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->state->getAreaCode() !== Area::AREA_ADMINHTML) {
            return;
        }

        $transport = $this->getTransportObject($observer);

        /** @var Order $order */
        $order = $transport->getOrder();
        $payment = $order->getPayment();

        if (array_key_exists('payment_link', $payment->getAdditionalInformation())) {
            $paymentLink = $payment->getAdditionalInformation()['payment_link'];
            $transport['payment_link'] = $paymentLink;
        }
    }

    /**
     * Event argument `transport` is deprecated since Magento 2.2.6. Using `transportObject` instead if exists.
     *
     * @param Observer $observer
     * @return mixed
     */
    private function getTransportObject(Observer $observer)
    {
        if (array_key_exists('transportObject', $observer->getData())) {
            return $observer->getData()['transportObject'];
        }
        return $observer->getTransport();
    }
}

