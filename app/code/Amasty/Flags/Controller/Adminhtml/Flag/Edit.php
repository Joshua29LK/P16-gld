<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\Flag;

use Amasty\Flags\Api\FlagRepositoryInterface;
use Amasty\Flags\Controller\Adminhtml\Flag as FlagAction;
use Amasty\Flags\Model\FlagFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends FlagAction
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $coreRegistry;
    /**
     * @var FlagRepositoryInterface
     */
    private $flagRepository;
    /**
     * @var FlagFactory
     */
    private $flagFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        FlagRepositoryInterface $flagRepository,
        FlagFactory $flagFactory
    ) {
        parent::__construct($context);
        $this->flagRepository = $flagRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->flagFactory = $flagFactory;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            $model = $this->flagRepository->get($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This flag no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $model = $this->flagFactory->create();
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('amflags_flag', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Flags::manage_flags');

        $resultPage->addBreadcrumb(
            $id ? __('Edit Flag') : __('New Flag'),
            $id ? __('Edit Flag') : __('New Flag')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Flags'));

        $name = $id ? __('Edit Flag "%1"', $model->getName()) : __('New Flag');

        $resultPage->getConfig()->getTitle()->prepend($name);

        return $resultPage;
    }
}
