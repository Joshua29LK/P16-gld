<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Controller\Adminhtml\Status;

class Edit extends \Magento\Backend\App\Action
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

    /**
     * @var \Amasty\OrderStatus\Model\StatusFactory
     */
    protected $statusFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Amasty\OrderStatus\Model\StatusFactory $statusFactory,
        \Magento\Backend\Model\Session $session
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->statusFactory = $statusFactory;
        $this->session = $session;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Amasty\OrderStatus\Model\Status $model */
        $model = $this->statusFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('Record does not exist.'));
                return $this->resultRedirectFactory->create()->setPath('amostatus/*');
            }
        }
        // set entered data if was error when we do save
        $data = $this->session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_amasty_order_status', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $pageResult = $this->resultPageFactory->create();
        $pageResult
            ->setActiveMenu('Amasty_OrderStatus::amostatus')
            ->addBreadcrumb(__('Order Status Information'), __('Order Status Information'))
            ->getConfig()->getTitle()->prepend(__('Edit Order Status'));

        return $pageResult;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_OrderStatus::amostatus');
    }
}
