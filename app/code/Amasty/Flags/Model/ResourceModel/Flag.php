<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Model\ResourceModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Flag extends AbstractDb
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Context $context,
        Filesystem $filesystem,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->filesystem = $filesystem;
    }

    protected function _construct()
    {
        $this->_init('amasty_flags_flag', 'id');
    }

    protected function _afterDelete(AbstractModel $object)
    {
        if ($object->getImageName()) {
            $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->delete(
                $object->getImageRelativePath()
            );
        }

        return parent::_afterDelete($object);
    }
}
