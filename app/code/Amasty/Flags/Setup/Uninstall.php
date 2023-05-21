<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Setup;

use Amasty\Flags\Model\Flag;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public const TABLE_NAMES = [
        'amasty_flags_order_flag',
        'amasty_flags_flag_column',
        'amasty_flags_flag',
        'amasty_flags_column'
    ];

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->uninstallTables($setup)->cleanFolders();
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $setup->startSetup();
        foreach (self::TABLE_NAMES as $tableName) {
            $setup->getConnection()->dropTable($setup->getTable($tableName));
        }
        $setup->endSetup();

        return $this;
    }

    private function cleanFolders(): self
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->delete(
            Flag::FLAGS_FOLDER
        );

        return $this;
    }
}
