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
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Mageprince\Productattach\Model\Productattach;

class DeleteFile extends Action
{
    /**
     * @var Productattach
     */
    private $attachModel;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * DeleteFile constructor.
     *
     * @param Action\Context $context
     * @param Productattach $attachModel
     * @param File $file
     * @param Filesystem $fileSystem
     */
    public function __construct(
        Action\Context $context,
        Productattach $attachModel,
        File $file,
        Filesystem $fileSystem
    ) {
        $this->attachModel = $attachModel;
        $this->file = $file;
        $this->fileSystem = $fileSystem;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageprince_Productattach::manage');
    }

    /**
     * Delete attachment file
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('productattach_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->attachModel;
                $model->load($id);
                $model->setFile('');
                $model->save();
                $this->messageManager->addSuccessMessage(__('The file has been removed from the attachment.'));

                //Delete file from the folder
                /*$mediaDirectory = $this->fileSystem
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $fileRootDir = $mediaDirectory->getAbsolutePath() . 'productattach';
                $currentFile = $model->getFile();
                if ($this->file->isExists($fileRootDir . $currentFile)) {
                    $this->file->deleteFile($fileRootDir . $currentFile);
                }*/
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            return $resultRedirect->setPath('*/*/edit', ['productattach_id' => $id]);
        }
    }
}
