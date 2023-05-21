<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Flags\Setup\Patch\Data;

use Amasty\Flags\Setup\SampleData\Installer;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddModuleData implements DataPatchInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var Installer
     */
    private $installer;

    public function __construct(
        ResourceInterface $moduleResource,
        Installer $installer
    ) {
        $this->moduleResource = $moduleResource;
        $this->installer = $installer;
    }

    public function apply()
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_Flags');
        if (!$setupDataVersion) {
            $this->installer->install();
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
