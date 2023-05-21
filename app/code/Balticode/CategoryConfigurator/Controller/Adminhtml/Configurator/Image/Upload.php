<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator\Image;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploaderFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Upload extends Action
{
    const DIRECTORY = 'configurator_images';

    /**
     * @var ImageUploaderFactory
     */
    protected $imageUploaderFactory;

    /**
     * @param Context $context
     * @param ImageUploaderFactory $imageUploaderFactory
     */
    public function __construct(
        Context $context,
        ImageUploaderFactory $imageUploaderFactory
    ) {
        $this->imageUploaderFactory = $imageUploaderFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $imageUploader = $this->imageUploaderFactory->create([
            'baseTmpPath' => self::DIRECTORY,
            'basePath' => self::DIRECTORY,
            'allowedExtensions' => [ 'jpg', 'jpeg', 'gif', 'png' ]
        ]);

        try {
            $result = $imageUploader->saveFileToTmpDir(ConfiguratorInterface::IMAGE_NAME);

            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(
            ['error' => 'File was not successfully uploaded.']
        );
    }
}