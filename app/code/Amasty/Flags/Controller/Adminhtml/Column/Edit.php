<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\Column;

use Amasty\Flags\Api\ColumnRepositoryInterface;
use Amasty\Flags\Controller\Adminhtml\Column as ColumnAction;
use Amasty\Flags\Model\ColumnFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends ColumnAction
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
     * @var ColumnRepositoryInterface
     */
    private $columnRepository;
    /**
     * @var ColumnFactory
     */
    private $columnFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ColumnRepositoryInterface $columnRepository,
        ColumnFactory $columnFactory
    ) {
        parent::__construct($context);
        $this->columnRepository = $columnRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->columnFactory = $columnFactory;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            $model = $this->columnRepository->get($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This column no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $model = $this->columnFactory->create();
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('amflags_column', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Flags::manage_columns');

        $resultPage->addBreadcrumb(
            $id ? __('Edit Column') : __('New Column'),
            $id ? __('Edit Column') : __('New Column')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Columns'));

        $name = $id ? __('Edit Column "%1"', $model->getName()) : __('New Column');

        $resultPage->getConfig()->getTitle()->prepend($name);

        return $resultPage;
    }
}
