<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Model;

use Amasty\Flags\Api\Data\FlagInterface;
use Amasty\Flags\Api\FlagRepositoryInterface;
use Amasty\Flags\Model\ResourceModel\Flag as FlagResource;
use Amasty\Flags\Model\FlagFactory;
use Amasty\Flags\Utils\FileUpload;
use Magento\Framework\File\Uploader;

class FlagRepository implements FlagRepositoryInterface
{
    /**
     * @var ResourceModel\Flag
     */
    private $flagResource;
    /**
     * @var FlagFactory
     */
    private $flagFactory;
    /**
     * @var FileUpload
     */
    private $fileUpload;

    public function __construct(
        FlagResource $flagResource,
        FlagFactory $flagFactory,
        FileUpload $fileUpload
    ) {
        $this->flagResource = $flagResource;
        $this->flagFactory = $flagFactory;
        $this->fileUpload = $fileUpload;
    }

    /**
     * @param int $id Flag ID.
     *
     * @return FlagInterface
     */
    public function get($id)
    {
        $model = $this->flagFactory->create();

        $this->flagResource->load($model, $id);

        return $model;
    }

    public function delete(FlagInterface $entity)
    {
        return $this->flagResource->delete($entity);
    }

    public function save(FlagInterface $entity)
    {
        $imagePath = $this->getUploadedImagePath();
        $result = $this->flagResource->save($entity);

        if (empty($imagePath)) {
            return $result;
        }

        $imageName = $this->fileUpload->moveFileToMedia($imagePath, Flag::FLAGS_FOLDER, $entity->getId());
        $entity->setImageName($imageName);

        return $this->flagResource->save($entity);
    }

    private function getUploadedImagePath(): string
    {
        $imagePath = '';

        try {
            $uploadedFile = $this->fileUpload->saveFileToTmpDir();
            $imagePath = $uploadedFile['path'] . DIRECTORY_SEPARATOR . $uploadedFile['file'];
        } catch (\Exception $e) {
            if ($e->getCode() != Uploader::TMP_NAME_EMPTY) {
                throw $e;
            }
        }

        return $imagePath;
    }
}
