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

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /** @var \Mageprince\Productattach\Model\FileiconFactory */
    private $fileiconFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Mageprince\Productattach\Model\FileiconFactory $fileiconFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Mageprince\Productattach\Model\FileiconFactory $fileiconFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->fileiconFactory = $fileiconFactory;
        parent::__construct($context);
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
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('fileicon_id');

            $model = $this->fileiconFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Fileicon no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $data = $this->_filterFileiconData($data);
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Fileicon.'));
                $this->dataPersistor->clear('prince_productattach_fileicon');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['fileicon_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Fileicon.'));
            }

            $this->dataPersistor->set('prince_productattach_fileicon', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['fileicon_id' => $this->getRequest()->getParam('fileicon_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function _filterFileiconData(array $rawData)
    {
        $data = $rawData;
        if (isset($data['icon_image'][0]['name'])) {
            $data['icon_image'] = $data['icon_image'][0]['name'];
        } else {
            $data['icon_image'] = null;
        }
        return $data;
    }
}
