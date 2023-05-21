<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_OrderDeliveryDate
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OrderDeliveryDate\Model\Plugin\Quote;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory as OrderFactory;
use Magento\Sales\Api\OrderManagementInterface as OrderManagement;
use Magento\Quote\Model\Quote\Address\ToOrder as ToOrderConverter;
use Magento\Quote\Model\Quote\Address\ToOrderAddress as ToOrderAddressConverter;
use Magento\Quote\Model\Quote\Item\ToOrderItem as ToOrderItemConverter;
use Magento\Quote\Model\Quote\Payment\ToOrderPayment as ToOrderPaymentConverter;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuoteManagement extends \Magento\Quote\Model\QuoteManagement
{
    const DELIVERY_FORM_DISPLAY_AT_REVIEW_PAYMENTS = 2;

    /**
     * @var \Bss\OrderDeliveryDate\Helper\Data
     */
    protected $helperBss;
    /**
     * @var \Magento\Quote\Model\SubmitQuoteValidator
     */
    private $submitQuoteValidator;

    /**
     * QuoteManagement constructor.
     * @param EventManager $eventManager
     * @param \Magento\Quote\Model\SubmitQuoteValidator $submitQuoteValidator
     * @param OrderFactory $orderFactory
     * @param OrderManagement $orderManagement
     * @param \Magento\Quote\Model\CustomerManagement $customerManagement
     * @param ToOrderConverter $quoteAddressToOrder
     * @param ToOrderAddressConverter $quoteAddressToOrderAddress
     * @param ToOrderItemConverter $quoteItemToOrderItem
     * @param ToOrderPaymentConverter $quotePaymentToOrderPayment
     * @param UserContextInterface $userContext
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerModelFactory
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Bss\OrderDeliveryDate\Helper\Data $helperBss
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EventManager $eventManager,
        \Magento\Quote\Model\SubmitQuoteValidator $submitQuoteValidator,
        OrderFactory $orderFactory,
        OrderManagement $orderManagement,
        \Magento\Quote\Model\CustomerManagement $customerManagement,
        ToOrderConverter $quoteAddressToOrder,
        ToOrderAddressConverter $quoteAddressToOrderAddress,
        ToOrderItemConverter $quoteItemToOrderItem,
        ToOrderPaymentConverter $quotePaymentToOrderPayment,
        UserContextInterface $userContext,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerModelFactory,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Bss\OrderDeliveryDate\Helper\Data $helperBss
    ) {
        parent::__construct(
            $eventManager,
            $submitQuoteValidator,
            $orderFactory,
            $orderManagement,
            $customerManagement,
            $quoteAddressToOrder,
            $quoteAddressToOrderAddress,
            $quoteItemToOrderItem,
            $quotePaymentToOrderPayment,
            $userContext,
            $quoteRepository,
            $customerRepository,
            $customerModelFactory,
            $quoteAddressFactory,
            $dataObjectHelper,
            $storeManager,
            $checkoutSession,
            $customerSession,
            $accountManagement,
            $quoteFactory
        );
        $this->helperBss = $helperBss;
    }

    /**
     * Submit Quote to Order
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $orderData
     * @return \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Api\Data\OrderInterface|object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function submitQuote(\Magento\Quote\Model\Quote $quote, $orderData = [])
    {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        $order = $this->orderFactory->create();
        $this->quoteValidator->validateBeforeSubmit($quote);
        if (!$quote->getCustomerIsGuest()) {
            if ($quote->getCustomerId()) {
                $this->_prepareCustomerQuote($quote);
            }
            $this->customerManagement->populateCustomerInfo($quote);
        }
        $addresses = [];
        $quote->reserveOrderId();
        if ($quote->isVirtual()) {
            $this->dataObjectHelper->mergeDataObjects(
                OrderInterface::class,
                $order,
                $this->quoteAddressToOrder->convert($quote->getBillingAddress(), $orderData)
            );
        } else {
            $this->dataObjectHelper->mergeDataObjects(
                OrderInterface::class,
                $order,
                $this->quoteAddressToOrder->convert($quote->getShippingAddress(), $orderData)
            );
            $shippingAddress = $this->quoteAddressToOrderAddress->convert(
                $quote->getShippingAddress(),
                [
                    'address_type' => 'shipping',
                    'email' => $quote->getCustomerEmail()
                ]
            );
            $addresses[] = $shippingAddress;
            $order->setShippingAddress($shippingAddress);
            $order->setShippingMethod($quote->getShippingAddress()->getShippingMethod());
        }
        $billingAddress = $this->quoteAddressToOrderAddress->convert(
            $quote->getBillingAddress(),
            [
                'address_type' => 'billing',
                'email' => $quote->getCustomerEmail()
            ]
        );
        $addresses[] = $billingAddress;
        $order->setBillingAddress($billingAddress);
        $order->setAddresses($addresses);
        $order->setPayment($this->quotePaymentToOrderPayment->convert($quote->getPayment()));
        $order->setItems($this->resolveItems($quote));
        if ($quote->getCustomer()) {
            $order->setCustomerId($quote->getCustomer()->getId());
        }
        $order->setQuoteId($quote->getId());
        $order->setCustomerEmail($quote->getCustomerEmail());
        $order->setCustomerFirstname($quote->getCustomerFirstname());
        $order->setCustomerMiddlename($quote->getCustomerMiddlename());
        $order->setCustomerLastname($quote->getCustomerLastname());

        $this->setDeliveryDate($data, $order, $quote);

        $this->eventManager->dispatch(
            'sales_model_service_quote_submit_before',
            [
                'order' => $order,
                'quote' => $quote
            ]
        );
        try {
            $order = $this->orderManagement->place($order);
            $quote->setIsActive(false);
            $this->eventManager->dispatch(
                'sales_model_service_quote_submit_success',
                [
                    'order' => $order,
                    'quote' => $quote
                ]
            );
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->eventManager->dispatch(
                'sales_model_service_quote_submit_failure',
                [
                    'order'     => $order,
                    'quote'     => $quote,
                    'exception' => $e
                ]
            );
            throw $e;
        }
        return $order;
    }

    /**
     * Convert ODD Quote to Order
     *
     * @param $data
     * @param $order
     * @param $quote
     */
    protected function setDeliveryDate($data, $order, $quote)
    {
        if ($this->helperBss->isEnabled()) {
            if ($quote->getShippingArrivalDate()
                || $quote->getShippingArrivalTimeslot()
                || $quote->getShippingArrivalComments()
            ) {
                $order->setShippingArrivalDate($quote->getShippingArrivalDate())
                      ->setShippingArrivalTimeslot($quote->getShippingArrivalTimeslot())
                      ->setShippingArrivalComments($quote->getShippingArrivalComments());
            } else {
                $this->addDeliveryDate($data, $order);
            }
        }
    }

    /**
     * @param null $data
     * @param null $order
     */
    protected function addDeliveryDate($data = null, $order = null)
    {
        if (isset($data['billingAddress']['extension_attributes']['shipping_arrival_date'])) {
            $order->setShippingArrivalDate($data['billingAddress']['extension_attributes']['shipping_arrival_date']);
        }
        if (isset($data['billingAddress']['extension_attributes']['shipping_arrival_comments'])) {
            $order->setShippingArrivalComments(
                $data['billingAddress']['extension_attributes']['shipping_arrival_comments']
            );
        }
        if (isset($data['billingAddress']['extension_attributes']['delivery_time_slot'])) {
            $order->setShippingArrivalTimeslot($data['billingAddress']['extension_attributes']['delivery_time_slot']);
        }
    }
}
