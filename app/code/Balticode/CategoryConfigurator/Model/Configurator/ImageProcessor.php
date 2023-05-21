<?php

namespace Balticode\CategoryConfigurator\Model\Configurator;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator\Image\Upload;
use Balticode\CategoryConfigurator\Helper\SearchCriteria\Configurator as ConfiguratorSearchCriteria;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;

class ImageProcessor
{
    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @var File
     */
    protected $fileManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ConfiguratorSearchCriteria
     */
    protected $configuratorSearchCriteria;

    /**
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param File $fileManager
     * @param Filesystem $filesystem
     * @param ConfiguratorSearchCriteria $configuratorSearchCriteria
     */
    public function __construct(
        ConfiguratorRepositoryInterface $configuratorRepository,
        File $fileManager,
        Filesystem $filesystem,
        ConfiguratorSearchCriteria $configuratorSearchCriteria
    ) {
        $this->configuratorRepository = $configuratorRepository;
        $this->fileManager = $fileManager;
        $this->filesystem = $filesystem;
        $this->configuratorSearchCriteria = $configuratorSearchCriteria;
    }

    /**
     * @throws FileSystemException
     */
    public function removeTemporaryImages()
    {
        $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $categoryImagesPath = $mediaPath . Upload::DIRECTORY;

        $imagePaths = $this->fileManager->readDirectory($categoryImagesPath);

        foreach ($imagePaths as $imagePath) {
            if (!$this->isImageUsed($imagePath)) {
                $this->fileManager->deleteFile($imagePath);
            }
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isImageUsed($path)
    {
        $pathArray = explode('/', $path);
        $imageName = end($pathArray);

        $searchCriteria = $this->configuratorSearchCriteria->getConfiguratorsByImageNameSearchCriteria($imageName);

        try {
            $configurators = $this->configuratorRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return false;
        }

        if (!count($configurators) > 0) {
            return false;
        }

        return true;
    }
}