<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Controller\Adminhtml\Status;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getLayout();
        $pageResult->setActiveMenu('Amasty_OrderStatus::amostatus');
        $pageResult->addBreadcrumb(__('Manage Custom Order Statuses'), __('Manage Custom Order Statuses'));
        $pageResult->getConfig()->getTitle()->prepend(__('Manage Custom Order Statuses'));

        return $pageResult;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_OrderStatus::amostatus');
    }
}
