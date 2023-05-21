<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Orderarchive\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->uninstallTables($setup)
            ->uninstallConfigData($setup);
        $setup->endSetup();
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $tablesToDrop = [
            'amasty_orderachive_sales_order_grid_archive',
            'amasty_orderachive_sales_invoice_grid_archive',
            'amasty_orderachive_sales_creditmemo_grid_archive',
            'amasty_orderachive_sales_shipment_grid_archive'
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
            "`path` LIKE 'orderarchive%'"
        );

        return $this;
    }
}
