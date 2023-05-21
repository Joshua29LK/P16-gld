<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Mageprince\Productattach\Model\Productattach
     */
    protected $attachModel;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backSession;

    /**
     * Edit constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Mageprince\Productattach\Model\Productattach $attachModel
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Mageprince\Productattach\Model\Productattach $attachModel
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->attachModel = $attachModel;
        $this->backSession = $context->getSession();
        parent::__construct($context);
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageprince_Productattach::manage');
    }

    /**
     * Init actions
     *
     * @return $this
     */
    public function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Mageprince_Productattach::manage'
        )->addBreadcrumb(
            __('Attachment'),
            __('Attachment')
        )->addBreadcrumb(
            __('Manage Attachment'),
            __('Manage Attachment')
        );
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('productattach_id');
        $model = $this->attachModel;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Attachment no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $this->coreRegistry->register('productattach_id', $model->getId());
        }

        $data = $this->backSession->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('productattach', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Attachment') : __('New Attachment'),
            $id ? __('Edit Attachment') : __('New Attachment')
        );
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Attachment'));
        return $resultPage;
    }
}
