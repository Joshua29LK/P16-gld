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
use Magento\Framework\HTTP\PhpEnvironment\Request as HttpRequest;
use Mageprince\Productattach\Helper\Data;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    private $dataProcessor;

    /**
     * @var \Mageprince\Productattach\Helper\Data
     */
    private $helper;

    /**
     * @var \Mageprince\Productattach\Model\Productattach
     */
    private $attachModel;

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $backSession;

    /**
     * @var \Mageprince\Productattach\Model\ResourceModel\Product
     */
    private $attachResourceModel;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    private $jsHelper;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param \Mageprince\Productattach\Model\Productattach $attachModel
     * @param \Mageprince\Productattach\Model\ResourceModel\Product $attachResourceModel
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param HttpRequest $httpRequest
     * @param Data $helper
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        \Mageprince\Productattach\Model\Productattach $attachModel,
        \Mageprince\Productattach\Model\ResourceModel\Product $attachResourceModel,
        \Magento\Backend\Helper\Js $jsHelper,
        HttpRequest $httpRequest,
        Data $helper
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->attachModel = $attachModel;
        $this->backSession = $context->getSession();
        $this->attachResourceModel = $attachResourceModel;
        $this->helper = $helper;
        $this->jsHelper = $jsHelper;
        $this->httpRequest = $httpRequest;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageprince_Productattach::manage');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            $customerGroup = $this->helper->getCustomerGroup($data['customer_group']);
            $store = $this->helper->getStores($data['store']);
            $data['customer_group'] = $customerGroup;
            $data['store'] = $store;
            $model = $this->attachModel;
            $id = $this->getRequest()->getParam('productattach_id');

            if ($id) {
                $model->load($id);
            }

            if (isset($data['products'])) {
                $data['assigned_products'] = $data['products'];
                $data['products'] = '';
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['productattach_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $file = $this->httpRequest->getFiles()->get('file');
                if ($file['size'] > 0) {
                    $this->helper->uploadFile('file', $model);
                }
                $model->save();
                if (isset($data['assigned_products'])) {
                    $productIds = $this->jsHelper->decodeGridSerializedInput($data['assigned_products']);
                    $this->attachResourceModel->saveProductsRelation($model, $productIds);
                }
                $this->messageManager->addSuccess(__('Attachment has been saved.'));
                $this->backSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['productattach_id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\FileSystemException $e) {
                $message = 'File upload error: ' . $e->getMessage();
                $this->messageManager->addErrorMessage(__($message));
            } catch (\Exception $e) {
                $message = 'Something went wrong while saving the attachment: ' . $e->getMessage();
                $this->messageManager->addErrorMessage(__($message));
            }
            $this->_redirect('*/*/edit', ['productattach_id' => $this->getRequest()->getParam('productattach_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
