<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\Order\Email\Sender\Plugin;

use Amasty\CustomerAttributes\Model\Customer\GuestAttributes;
use Amasty\OrderStatus\Block\Adminhtml\Status\Edit\Tab\Email;
use Amasty\OrderStatus\Model\AmOrderStatusRegistry;
use Amasty\OrderStatus\Model\ResourceModel\Template\CollectionFactory;
use Amasty\OrderStatus\Model\Status;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender as MagentoOrderCommentSender;
use Magento\Sales\Model\Order\Email\SenderBuilder;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;
use Psr\Log\LoggerInterface;

class OrderCommentSender extends MagentoOrderCommentSender
{
    public const AMASTY_CUSTOMERATTRIBUTES_MODULE = 'Amasty_CustomerAttributes';

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var AmOrderStatusRegistry
     */
    private $amOrderStatusRegistry;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        Template $templateContainer,
        OrderCommentIdentity $identityContainer,
        SenderBuilderFactory $senderBuilderFactory,
        LoggerInterface $logger,
        Renderer $addressRenderer,
        ManagerInterface $eventManager,
        Registry $coreRegistry,
        ObjectManagerInterface $objectManager,
        CustomerFactory $customerFactory,
        Manager $moduleManager,
        PaymentHelper $paymentHelper,
        CollectionFactory $templateCollectionFactory,
        AmOrderStatusRegistry $amOrderStatusRegistry,
        DataObjectFactory $dataObjectFactory = null
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->objectManager = $objectManager;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->moduleManager = $moduleManager;
        $this->customerFactory = $customerFactory;
        $this->paymentHelper = $paymentHelper;
        $this->amOrderStatusRegistry = $amOrderStatusRegistry;
        $this->dataObjectFactory = $dataObjectFactory ?? ObjectManager::getInstance()->get(DataObjectFactory::class);

        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $eventManager
        );
    }

    public function aroundSend(
        MagentoOrderCommentSender $subject,
        \Closure $proceed,
        Order $order,
        bool $notify = true,
        string $comment = ''
    ): bool {
        $this->coreRegistry->register('amorderstatus_store_id', $order->getStoreId(), true);
        $this->coreRegistry->register('amorderstatus_state', $order->getState(), true);

        $transport = [
            'order' => $order,
            'customer' => $this->getCustomer($order),
            'comment' => $comment,
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'payment_html' => $this->getPaymentHtml($order),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];

        $transportObject = $this->dataObjectFactory->create()->setData($transport);

        $this->eventManager->dispatch(
            'email_order_comment_set_template_vars_before',
            [
                'sender' => $this,
                'transport' => $transport,
                'transportObject' => $transportObject
            ]
        );

        $this->templateContainer->setTemplateVars($transportObject->getData());

        return $this->customCheckAndSend($order, $notify);
    }

    private function getCustomer(Order $order): Customer
    {
        /** @var Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->setStore($order->getStore());
        $customer->setWebsiteId($order->getStore()->getWebsite()->getId());

        if ($order->getCustomerId()) {
            $customer->loadByEmail($order->getCustomerEmail());
        } elseif ($this->moduleManager->isEnabled(self::AMASTY_CUSTOMERATTRIBUTES_MODULE)) {
            $model = $this->objectManager->create(GuestAttributes::class)
                ->loadByOrderId($order->getId());

            if ($model && $model->getId()) {
                foreach ($model->getData() as $key => $value) {
                    if ($key == 'id') {
                        continue;
                    }
                    if ($value) {
                        $customer->setData($key, $value);
                    }
                }
            }
        }

        return $customer;
    }

    private function customCheckAndSend(
        Order $order,
        bool $notify
    ): bool {
        $this->identityContainer->setStore($order->getStore());

        if (!$this->identityContainer->isEnabled()) {
            return false;
        }

        /** @var Status $ourStatus */
        $amastyStatus = $this->amOrderStatusRegistry->get($order->getStatus());

        if ($amastyStatus && (int)$amastyStatus->getNotifyByEmail() !== Email::NOTIFY_OPTIONAL) {
            $notify = (bool)$amastyStatus->getNotifyByEmail();
        }

        $this->prepareTemplate($order);

        /** @var SenderBuilder $sender */
        $sender = $this->getSender();

        if ($notify) {
            $sender->send();
        } else {
            /*Email copies are sent as separated emails
             * if their copy method is 'copy' or a customer should not be notified
            */
            $sender->sendCopyTo();
        }

        return true;
    }

    protected function prepareTemplate(Order $order): void
    {
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->identityContainer->getTemplateId();
            $customerName = $order->getCustomerName();
        }

        /** @var Status $ourStatus */
        $ourStatus = $this->amOrderStatusRegistry->get($order->getStatus());

        if ($ourStatus) {
            $storeId = $order->getStoreId();

            $ourTemplateCollection = $this->templateCollectionFactory->create();
            $ourTemplateId = $ourTemplateCollection->loadTemplateId($ourStatus->getId(), $storeId);

            if ($ourTemplateId != 0) {
                $templateId = $ourTemplateId;
            }
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }

    private function getPaymentHtml(Order $order): string
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStore()->getId()
        );
    }
}
