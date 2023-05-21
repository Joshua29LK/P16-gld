<?php
/**
 * MageVision Update Order Email Address Extension
 *
 * @category     MageVision
 * @package      MageVision_UpdateOrderEmailAddress
 * @author       MageVision Team
 * @copyright    Copyright (c) 2018 MageVision (http://www.magevision.com)
 * @license      LICENSE_MV.txt or http://www.magevision.com/license-agreement/
 */
namespace MageVision\UpdateOrderEmailAddress\Plugin\Sales\Block\Adminhtml\Order\View;

use Magento\Framework\Module\Manager;
use Magento\Framework\App\ProductMetadataInterface;

class Info
{
    /**
     * @param $moduleManager
     */
    protected $moduleManager;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param Manager $moduleManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Manager $moduleManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->moduleManager = $moduleManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeToHtml(\Magento\Sales\Block\Adminhtml\Order\View\Info $subject)
    {
        if ($subject->getParentBlock()->getNameInLayout() === 'order_tab_info'
            && $subject->getNameInLayout() === 'order_info'
        ) {
            if ($this->moduleManager->isEnabled('Temando_Shipping')) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $config = $objectManager->get(\Temando\Shipping\ViewModel\Order\Location::class);
                $filesystem = $objectManager->get(\Temando\Shipping\ViewModel\Order\OrderDetails::class);
                if (version_compare($this->productMetadata->getVersion(), '2.3.0', '<')) {
                    $subject->setData('locationViewModel', $config);
                    $subject->setData('orderDetailsViewModel', $filesystem);
                    $subject->setTemplate('MageVision_UpdateOrderEmailAddress::sales/order/view/info_shipping.phtml');
                } else {
                    $subject->setData('locationViewModel', $config);
                    $subject->setData('orderDetailsViewModel', $filesystem);
                    $subject->setTemplate('MageVision_UpdateOrderEmailAddress::sales/order/view/info_shipping_v2.3.phtml');
                }

            } else {
                $subject->setTemplate('MageVision_UpdateOrderEmailAddress::sales/order/view/info.phtml');
            }
            $orderInfoEmail = $subject->getLayout()->createBlock(
                \MageVision\UpdateOrderEmailAddress\Block\Adminhtml\Sales\Order\View\Info\Email::class,
                'order_info_email',
                ['data' => ['template' => 'MageVision_UpdateOrderEmailAddress::sales/order/view/info/email.phtml']]
            );
            $subject->getLayout()->getBlock('order_info')->append($orderInfoEmail);
        }
    }
}
