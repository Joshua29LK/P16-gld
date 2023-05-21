<?php

namespace MageArray\Customprice\Controller\Adminhtml\Customcsv;

use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends \Magento\Backend\App\Action
{
    /**
     * [__construct description]
     *
     * @param \Magento\Backend\App\Action\Context             $context          [description]
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * [execute description]
     *
     * @return [type] [description]
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data['option_id'] != "") {
            $csvfileid = 'csvfileupload' . $data['option_id'];
        } else {
            $csvfileid = 'csvfileupload';
        }

        try {
            $uploader = $this->_objectManager->create(
                \Magento\MediaStorage\Model\File\Uploader::class,
                ['fileId' => $csvfileid]
            );
            $uploader->setAllowedExtensions(['csv']);

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $mediaDirectory = $this->_objectManager
                ->get(\Magento\Framework\Filesystem::class)
                ->getDirectoryRead(DirectoryList::MEDIA);

            $result = $uploader
                ->save($mediaDirectory->getAbsolutePath('csvfiles/allfiles'));
        } catch (\Exception $e) {
            $result = [];
            $result['error'] = $e->getMessage();
            $result['errorcode'] = 0;
        }
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
}
