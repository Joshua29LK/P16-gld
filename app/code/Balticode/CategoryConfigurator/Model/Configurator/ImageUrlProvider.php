<?php

namespace Balticode\CategoryConfigurator\Model\Configurator;

use Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator\Image\Upload;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class ImageUrlProvider
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $imageName
     * @return string
     */
    public function getImageUrl($imageName)
    {
        if (!$imageName) {
            return '';
        }

        try {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        } catch (NoSuchEntityException $e) {
            return '';
        }

        $filePath = Upload::DIRECTORY . '/' . $imageName;
        $imageUrl = $baseUrl . $filePath;

        return $imageUrl;
    }
}