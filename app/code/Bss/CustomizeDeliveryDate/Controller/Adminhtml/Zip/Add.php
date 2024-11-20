<?php

namespace Bss\CustomizeDeliveryDate\Controller\Adminhtml\Zip;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Add extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bss_CustomizeDeliveryDate::zip_delivery');
        $resultPage->getConfig()->getTitle()->prepend(__('Add Zip Delivery'));

        return $resultPage;
    }
}
