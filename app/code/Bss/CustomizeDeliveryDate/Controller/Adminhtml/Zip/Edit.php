<?php

namespace Bss\CustomizeDeliveryDate\Controller\Adminhtml\Zip;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function execute()
    {
        $zipId = $this->getRequest()->getParam('zip_id');
        $this->coreRegistry->register('zip_id', $zipId);
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bss_CustomizeDeliveryDate::zip_delivery');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Zip Delivery'));

        return $resultPage;
    }
}
