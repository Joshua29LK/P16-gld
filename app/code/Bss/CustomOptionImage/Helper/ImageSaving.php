<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CustomOptionImage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomOptionImage\Helper;

class ImageSaving
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploader;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * ImageSaving constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploader
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Image\AdapterFactory $imageFactory
    ) {
        $this->storeManager = $storeManager;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->fileDriver = $fileDriver;
        $this->messageManager = $messageManager;
        $this->imageFactory = $imageFactory;
    }

    /**
     * Get Media BaseUrl
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Resize
     *
     * @param string $image
     * @param null $width
     * @param null $height
     * @return false|string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resize($image, $width = null, $height = null)
    {
        $directory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $realPathImage = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath('') . $image;

        if (!$directory->isFile($realPathImage) || !$directory->isExist($realPathImage)) {
            return false;
        }
        try {
            $destination = $this->filesystem
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                    ->getAbsolutePath('resized/' . $width . 'x' . $height . '/') . $image;

            if ($directory->isExist($destination)) {
                return $this->getMediaBaseUrl() . 'resized/' . $width . 'x' . $height . '/' . $image;
            }

            $imageResize = $this->imageFactory->create();
            $imageResize->open($realPathImage);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            $imageResize->save($destination);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        return $this->getMediaBaseUrl() . 'resized/' . $width . 'x' . $height . '/' . $image;
    }

    /**
     * Move image
     *
     * @param Object $value
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function moveImage($value)
    {
        $imageUrl = $value->getData('image_url');
        if ($value->getData('bss_image_button')
            && $value->getData('bss_image_button') != 'bss'
            && $value->getData('bss_image_button') != 'delcoi'
        ) {
            $file = $value->getData('bss_image_button');
        } elseif ($imageUrl && strpos($imageUrl, 'bss/temp/') !== false) {
            $file = $imageUrl;
        } else {
            return '';
        }
        $fileNamePieces = explode('/', $file);
        $fileName = end($fileNamePieces);

        return $this->renameImageFile($fileName, $file, 'bss/coi-image/');
    }

    /**
     * Rename image file
     *
     * @param string $fileName
     * @param mixed $file
     * @param string $newPath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function renameImageFile($fileName, $file, $newPath)
    {
        $mediaRootDir = $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath();
        $mediaDirectory = $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
        $this->fileDriver->createDirectory($mediaDirectory->getAbsolutePath($newPath));
        $checkDuplicateName = $fileName;

        if ($file !== $newPath . $fileName) {
            $checkTime = 0;
            while ($this->fileDriver->isFile($mediaRootDir . $newPath . $checkDuplicateName)) {
                $checkDuplicateName = '(' . $checkTime . ')' . $fileName;
                $checkTime++;
            }
            try {
                $this->fileDriver->rename($mediaRootDir . $file, $mediaRootDir . $newPath . $checkDuplicateName);
            } catch (\Exception $e) {
                $message = __("The image picture: " . $file . ", at folder: " . $mediaRootDir . ", does not exist");
                $this->messageManager->addErrorMessage($message);
                return '';
            }
        }
        return $newPath . $checkDuplicateName;
    }

    /**
     * Move image swatch
     *
     * @param Object $value
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function moveImageSwatch($value)
    {
        $imageUrl = $value->getData('swatch_image_url');
        if ($value->getData('swatch_image_url_hidden')
            && $value->getData('swatch_image_url_hidden') != 'bss'
            && $value->getData('swatch_image_url_hidden') != 'delswatch'
        ) {
            $file = $value->getData('swatch_image_url_hidden');
        } elseif ($imageUrl && strpos($imageUrl, 'bss/temp/') !== false) {
            $file = $imageUrl;
        } else {
            return '';
        }
        $fileNamePieces = explode('/', $file);
        $fileName = end($fileNamePieces);

        return $this->renameImageFile($fileName, $file, 'bss/coi-image-swatch/');
    }

    /**
     * clean temp file
     *
     * @return void
     */
    public function cleanTempFile()
    {
        try {
            $mediaRootDir = $this->filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath();
            if ($this->fileDriver->isDirectory($mediaRootDir . 'bss/temp/')) {
                $this->fileDriver->deleteDirectory($mediaRootDir . 'bss/temp/');
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }
    }

    /**
     * Save temporary image
     *
     * @param string $opOrder
     * @param string $valueOrder
     * @return string|null
     */
    public function saveTemporaryImage($opOrder, $valueOrder)
    {
        try {
            $fieldName = 'temporary_image';
            $baseMediaPath = 'bss/temp/' . $opOrder . '_' . $valueOrder . '/';
            $uploader = $this->uploader->create(['fileId' => $fieldName ]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'bmp']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            );
            $result = $uploader->save($mediaDirectory->getAbsolutePath($baseMediaPath));

            $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
            $result['path'] = str_replace('\\', '/', $result['path']);
            $result['url'] = $this->getFilePath($baseMediaPath, $result['file']);
            return $result['url'];
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return null;
        }
    }

    /**
     * Get File path
     *
     * @param string $path
     * @param string $imageName
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Duplicate image
     *
     * @param string $oldUrl
     * @return string|null
     */
    public function duplicateImage($oldUrl)
    {
        $mediaRootDir = $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath();
        if ($oldUrl) {
            $file = $oldUrl;
        } else {
            return '';
        }
        try {
            $fileNamePieces = explode('/', $file);
            $fileName = end($fileNamePieces);
            $mediaDirectory = $this->filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            );
            $newPath = 'bss/temp/';

            $this->fileDriver->createDirectory($mediaDirectory->getAbsolutePath($newPath));
            $checkDuplicateName = $fileName;
            if ($file !== $newPath . $fileName) {
                $checkTime = 0;
                while ($this->fileDriver->isFile($mediaRootDir . $newPath . $checkDuplicateName)) {
                    $checkDuplicateName = '(' . $checkTime . ')' . $fileName;
                    $checkTime++;
                }
                $this->fileDriver->copy($mediaRootDir . $file, $mediaRootDir . $newPath . $checkDuplicateName);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return null;
        }
        return $newPath . $checkDuplicateName;
    }
}
