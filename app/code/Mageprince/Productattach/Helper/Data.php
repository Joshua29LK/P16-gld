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

namespace Mageprince\Productattach\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Mageprince\Productattach\Model\Config\DefaultConfig;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var DefaultConfig
     */
    protected $defaultConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param DefaultConfig $defaultConfig
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        DefaultConfig $defaultConfig
    ) {
        $this->backendUrl = $backendUrl;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
        $this->defaultConfig = $defaultConfig;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * Upload image and return uploaded image file name or false
     * @param string $scope
     * @param $model
     * @return mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function uploadFile($scope, $model)
    {
        try {
            $uploader = $this->fileUploaderFactory->create(['fileId' => $scope]);
            $uploader->setAllowedExtensions($this->getAllowedExt());
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);

            if ($uploader->save($this->getBaseDir())) {
                $model->setFile($uploader->getUploadedFileName());
                $model->setFileExt($uploader->getFileExtension());
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\FileSystemException(
                __($e->getMessage())
            );
        }

        return $model;
    }

    /**
     * Save file-content to the file on the file-system
     * @param $filename
     * @param $fileContent
     * @return string
     */
    public function saveFile($filename, $fileContent)
    {
        if ($fileContent != "") {
            try {
                $folderAbsolutePath = $this->getDispersionFolderAbsolutePath($filename);
                if (!file_exists($folderAbsolutePath)) {
                    //create folder
                    mkdir($folderAbsolutePath, 0777, true);
                }
                // create file
                file_put_contents($folderAbsolutePath."/".$filename, base64_decode($fileContent));
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get base media directory for attachment images
     * @return string
     */
    public function getBaseDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(DefaultConfig::MEDIA_PATH);
        return $path;
    }

    /**
     * Get base url for attachment images
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . DefaultConfig::MEDIA_PATH;
    }

    /**
     * Get current store id
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get products grid url
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->backendUrl->getUrl('productattach/index/products', ['_current' => true]);
    }

    /**
     * Get customer groups
     * @param $customers
     * @return string
     */
    public function getCustomerGroup($customers)
    {
        $customers = implode(',', $customers);
        return $customers;
    }

    /**
     * Get stores
     * @param $store
     * @return string
     */
    public function getStores($store)
    {
        $store = implode(',', $store);
        return $store;
    }

    /**
     * Get path of the file that will be save in the database
     * @param $fileName
     * @return string
     */
    public function getFilePathForDB($fileName)
    {
        return $this->getFileDispersionPath($fileName) ."/". $fileName;
    }

    /**
     * Return the path to the file acording to the dispersion principle (first and second letter)
     * Example file.tyt => f/i/file.txt
     * @param $fileName
     * @return string
     */
    public function getFileDispersionPath($fileName)
    {
        return \Magento\MediaStorage\Model\File\Uploader::getDispretionPath($fileName);
    }

    /**
     * Delete the file in the folder media folder of product attachment
     * @param $filepathInMediaFolder
     */
    public function deleteFile($filepathInMediaFolder)
    {
        $exts = explode('.', $filepathInMediaFolder);
        $ext = "";
        if (count($exts)) {
            $ext = $exts[count($exts)-1];
        }
        if (in_array($ext, $this->getAllowedExt()) &&
            strpos($filepathInMediaFolder, "..") === false ) {
            $finalPath = $this->getProductAttachMediaFolderAbsolutePath()."/".$filepathInMediaFolder;
            if (file_exists($finalPath)) {
                unlink($finalPath);
            }
        }
    }

    /**
     * Return the media folder absolute path
     * @return string
     */
    private function getProductAttachMediaFolderAbsolutePath()
    {
        $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        return $mediaPath . DefaultConfig::MEDIA_PATH;
    }

    /**
     * Return the dispersion folder absoluite path for the given filename
     * @param $filename
     * @return string
     */
    public function getDispersionFolderAbsolutePath($filename)
    {
        return $this->getProductAttachMediaFolderAbsolutePath()."/".$this->getFileDispersionPath($filename);
    }

    /**
     * Return the allowed file extensions
     * @return array
     */
    public function getAllowedExt()
    {
        return $this->defaultConfig->getAllowedExt();
    }

    /**
     * Get media url
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }

    /**
     * Get file size by attachment
     * @param $file
     * @return string
     */
    public function getFileSize($file)
    {
        try {
            $fileSize = $this->mediaDirectory->stat($file)['size'];
            $readableSize = $this->convertToReadableSize($fileSize);
            return $readableSize;
        } catch (\Exception $e) {
            $message = 'Attachment file error: ' . $e->getMessage();
            $this->_logger->error($message);
        }
    }

    /**
     * Convert size into readable format
     * @param $size
     * @return string
     */
    public function convertToReadableSize($size)
    {
        $base = log($size) / log(1024);
        $suffix = ["", " KB", " MB", " GB", " TB"];
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }

    /**
     * Get default file icon
     * @return array
     */
    public function getDefaultIcons()
    {
        return $this->defaultConfig->getDefaultIcons();
    }
}
