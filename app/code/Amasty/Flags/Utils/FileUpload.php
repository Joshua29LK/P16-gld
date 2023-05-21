<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Flags\Utils;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

class FileUpload
{
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png', 'svg'];

    public const TEMP_DIR_NAME = 'amasty_flags';

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $ioFile;

    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        File $ioFile
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
    }

    public function saveFileToTmpDir(): array
    {
        /** @var $uploader Uploader */
        $uploader = $this->uploaderFactory->create(
            ['fileId' => 'image_name']
        );
        $uploader->setAllowedExtensions(self::ALLOWED_EXTENSIONS);

        $basePath = $this->getTempDirPath();
        $fileName = $this->getUniqueFileName();
        $imageName = $fileName . '.' . $uploader->getFileExtension();

        return $uploader->save($basePath, $imageName);
    }

    public function moveFileToMedia($sourceFileName = null, $filePath = null, $fileName = null): string
    {
        $mediaDirWriter = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $tempDirWriter = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);

        $basePath = $mediaDirWriter->getAbsolutePath($filePath);
        if (!$mediaDirWriter->isExist($basePath)) {
            $mediaDirWriter->create($basePath);
        }

        $newFileName = $this->getFileNameBySourceFile($sourceFileName, $fileName);
        $targetFileName = $basePath . DIRECTORY_SEPARATOR . $newFileName;

        if ($tempDirWriter->copyFile($sourceFileName, $targetFileName, $mediaDirWriter)
            && $tempDirWriter->delete($sourceFileName)) {
            return $newFileName;
        }

        return '';
    }

    private function getFileNameBySourceFile($sourceFileName = null, $fileName = null): string
    {
        $tempDirReader = $this->filesystem->getDirectoryRead(DirectoryList::TMP);
        if (!$tempDirReader->isExist($sourceFileName)) {
            throw new LocalizedException(__('Source file does not exist'));
        }

        if (empty($fileName)) {
            throw new LocalizedException(__('File name is not specified'));
        }

        return $fileName . '.' . $this->ioFile->getPathInfo($sourceFileName)['extension'];
    }

    private function getTempDirPath(): string
    {
        $tempDirWriter = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        if (!$tempDirWriter->isExist(self::TEMP_DIR_NAME)) {
            $tempDirWriter->create(self::TEMP_DIR_NAME);
        }

        return $tempDirWriter->getAbsolutePath(self::TEMP_DIR_NAME);
    }

    private function getUniqueFileName(): string
    {
        return uniqid('flag_');
    }
}
