<?php

namespace Balticode\CategoryConfigurator\Model\Configurator;

use Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator\Image\Upload;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

class ImageInfo
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     */
    public function __construct(Filesystem $filesystem, LoggerInterface $logger)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * @param string $imageName
     * @return array
     */
    public function getImageInfo($imageName)
    {
        $filePath = Upload::DIRECTORY . '/' . $imageName;

        try {
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

            return $mediaDirectory->stat($filePath);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return [];
    }
}