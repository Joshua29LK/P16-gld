<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Recurring implements InstallSchemaInterface
{
    private $tablesMap = [
        'sales_order_grid'      => 'amasty_orderachive_sales_order_grid_archive',
        'sales_invoice_grid'    => 'amasty_orderachive_sales_invoice_grid_archive',
        'sales_creditmemo_grid' => 'amasty_orderachive_sales_creditmemo_grid_archive',
        'sales_shipment_grid'   => 'amasty_orderachive_sales_shipment_grid_archive',
    ];

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->tablesMap as $gridTable => $archiveTable) {
            if (!$setup->tableExists($setup->getTable($archiveTable))) {
                $table = $setup->getConnection()
                    ->createTableByDdl($setup->getTable($gridTable), $setup->getTable($archiveTable));

                $setup->getConnection()->createTable($table);
            }
        }
    }
}
