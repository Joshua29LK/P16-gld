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
namespace MageVision\UpdateOrderEmailAddress\Block\Adminhtml\Sales\Order\View\Info;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template;

class Email extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('order-account-information-email'), '" . $this->getSubmitUrl() . "')";
        $buttonUpdate = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Update'), 'class' => 'action-save action-secondary', 'onclick' => $onclick]
        );
        $this->setChild('update_button', $buttonUpdate);
        $buttonEdit = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Edit'), 'class' => 'action-save action-secondary', 'onclick' => 'showUpdatedEmailContent()']
        );
        $this->setChild('edit_button', $buttonEdit);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve order model
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }

    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('uoea/order/updateEmail', ['order_id' => $this->getOrder()->getId()]);
    }
}
