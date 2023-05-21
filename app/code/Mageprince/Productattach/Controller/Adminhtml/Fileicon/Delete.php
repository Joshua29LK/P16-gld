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

namespace Mageprince\Productattach\Controller\Adminhtml\Fileicon;

class Delete extends \Mageprince\Productattach\Controller\Adminhtml\Fileicon
{
    /**
     * @var \Mageprince\Productattach\Model\FileiconFactory
     */
    protected $fileiconFactory;

    /**
     * Delete constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Mageprince\Productattach\Model\FileiconFactory $fileiconFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Mageprince\Productattach\Model\FileiconFactory $fileiconFactory
    ) {
        $this->fileiconFactory = $fileiconFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageprince_Productattach::manage');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('fileicon_id');
        if ($id) {
            try {
                $model = $this->fileiconFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Fileicon.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['fileicon_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Fileicon to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
