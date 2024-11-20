<?php

namespace Bss\CustomizeDeliveryDate\Controller\Adminhtml\Zip;

use Magento\Backend\App\Action;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Bss_CustomizeDeliveryDate::zip_management';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory = false;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bss_CustomizeDeliveryDate::zip_management');
        $resultPage->getConfig()->getTitle()->prepend(__('Zip Delivery Management'));
        return $resultPage;
    }
}
