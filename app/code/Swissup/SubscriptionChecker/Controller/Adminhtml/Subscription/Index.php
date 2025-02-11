<?php

namespace Swissup\SubscriptionChecker\Controller\Adminhtml\Subscription;

class Index extends \Swissup\SubscriptionChecker\Controller\Adminhtml\Subscription
{
    const ADMIN_RESOURCE = 'Swissup_SubscriptionChecker::subscription';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $model = $this->_objectManager->create('Swissup\Core\Model\Module');
        $model->load(self::MODULE_CODE);

        $session = $this->_objectManager->get('Magento\Backend\Model\Session');
        $data = $session->getFormData(true);
        if (!empty($data)) {
            // do not use setData, as it removes non-editable fields
            $model->addData($data);
        }

        $this->coreRegistry->register('subscription', $model);

        $firecheckout = $this->_objectManager->create('Swissup\Core\Model\Module');
        $firecheckout->load('Swissup_Firecheckout');
        if ($firecheckout->getLocal()) {
            $this->coreRegistry->register('firecheckout', $firecheckout);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Swissup_SubscriptionChecker::subscription');
        $resultPage->addBreadcrumb(__('License Checker'), __('License Checker'));
        $resultPage->addBreadcrumb(__('Manage License'), __('Manage License'));
        $resultPage->getConfig()->getTitle()->prepend(__('Activate License'));

        return $resultPage;
    }
}
