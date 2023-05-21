<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->uninstallTables($setup)
            ->uninstallConfigData($setup);
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $tablesToDrop = [
            'amasty_orderstatus_status',
            'amasty_orderstatus_status_template'
        ];
        foreach ($tablesToDrop as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            "`path` LIKE 'amostatus%'"
        );

        return $this;
    }
}
